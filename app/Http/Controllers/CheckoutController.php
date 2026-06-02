<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\UserBanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find(session('user_id'));
        $restrictionMessage = $user ? UserBanService::getRestrictionMessage($user) : null;

        if ($restrictionMessage) {
            return redirect('/cart')->with('error', $restrictionMessage);
        }

        $auctionPicture = null;
        return view('checkout', compact('auctionPicture'));
    }

    public function process(Request $request)
    {
        $userId = (int) session('user_id');
        $user = User::find($userId);
        $restrictionMessage = $user ? UserBanService::getRestrictionMessage($user) : null;

        if ($restrictionMessage) {
            return redirect('/cart')->with('error', $restrictionMessage);
        }

        $pictureIds = (string) $request->input('picture_ids', '');
        $pickupPoint = trim((string) $request->input('pickup_point', ''));
        $recipientName = trim((string) $request->input('recipient_name', ''));
        $keepSoldInGallery = $request->boolean('keep_sold_in_gallery');

        if ($pictureIds === '' || $pickupPoint === '' || $recipientName === '') {
            return redirect('/cart')->with('error', 'Заполните все обязательные поля');
        }

        if (!$this->isValidPickupPoint($pickupPoint)) {
            return redirect('/cart')->with('error', 'Укажите адрес в свободной форме, например: Москва, Ленина 1.');
        }

        $pictureIdsArray = array_values(array_filter(array_map('intval', explode(',', $pictureIds))));
        if ($pictureIdsArray === []) {
            return redirect('/cart')->with('error', 'Товары не найдены');
        }

        $cartItems = $this->loadCheckoutCartItems($userId, $pictureIdsArray);
        if ($cartItems->isEmpty()) {
            return redirect('/cart')->with('error', 'Товары не найдены');
        }

        foreach ($cartItems as $item) {
            $picture = $item->picture;
            $latestBidUserId = optional($picture->latestAuctionBid)->user_id;

            if ($picture->user_id == $userId || $picture->orders()->where('payment_status', 'succeeded')->exists()) {
                return redirect('/cart')->with('error', 'Нельзя оформить одну из картин');
            }

            if ($picture->listing_type === 'auction') {
                if (!$picture->auction_ends_at || $picture->auction_ends_at->isFuture() || $latestBidUserId != $userId) {
                    return redirect('/cart')->with('error', 'Аукционную картину может оплатить только победитель');
                }
            }
        }

        $totalAmount = (int) $cartItems->sum(fn ($item) => (int) $item->picture->price);
        $checkoutToken = (string) Str::uuid();
        $returnUrl = $request->getSchemeAndHttpHost() . '/checkout/callback?checkout_token=' . urlencode($checkoutToken);

        Log::info('Checkout payment creation started.', [
            'buyer_id' => $userId,
            'picture_ids' => $pictureIdsArray,
            'amount' => $totalAmount,
            'return_url' => $returnUrl,
        ]);

        $payment = $this->createYooKassaPayment($totalAmount, $userId, $pictureIds, $pickupPoint, $recipientName, $returnUrl);

        if (!$payment['success']) {
            Log::warning('Checkout payment creation failed.', [
                'buyer_id' => $userId,
                'picture_ids' => $pictureIdsArray,
                'message' => $payment['message'],
            ]);

            return redirect('/cart')->with('error', $payment['message']);
        }

        Log::info('Checkout payment creation succeeded.', [
            'buyer_id' => $userId,
            'payment_id' => $payment['payment_id'],
            'checkout_token' => $checkoutToken,
        ]);

        $pendingPayment = [
            'checkout_token' => $checkoutToken,
            'payment_id' => $payment['payment_id'],
            'buyer_id' => $userId,
            'picture_ids' => $pictureIdsArray,
            'pickup_point' => $pickupPoint,
            'recipient_name' => $recipientName,
            'keep_sold_in_gallery' => $keepSoldInGallery,
            'cart_items' => $cartItems->map(function ($item) {
                return [
                    'picture_id' => (int) $item->picture_id,
                    'seller_id' => (int) $item->picture->user_id,
                    'price' => (int) $item->picture->price,
                    'name' => (string) $item->picture->name,
                ];
            })->values()->all(),
        ];

        session(['pending_payment' => $pendingPayment]);
        session()->save();
        Cache::put($this->pendingPaymentCacheKey($checkoutToken), $pendingPayment, now()->addHours(3));

        return redirect()->away($payment['confirmation_url']);
    }

    public function callback(Request $request)
    {
        $checkoutToken = (string) $request->query('checkout_token', '');
        $pendingPayment = session('pending_payment');

        Log::info('Checkout callback received.', [
            'checkout_token' => $checkoutToken,
            'has_session_payment' => is_array($pendingPayment) && !empty($pendingPayment['payment_id']),
            'full_url' => $request->fullUrl(),
        ]);

        if ((!$pendingPayment || empty($pendingPayment['payment_id'])) && $checkoutToken !== '') {
            $pendingPayment = Cache::get($this->pendingPaymentCacheKey($checkoutToken));

            Log::info('Checkout callback cache lookup completed.', [
                'checkout_token' => $checkoutToken,
                'has_cached_payment' => is_array($pendingPayment) && !empty($pendingPayment['payment_id']),
            ]);
        }

        if (!$pendingPayment || empty($pendingPayment['payment_id'])) {
            Log::warning('Checkout callback has no pending payment.', [
                'checkout_token' => $checkoutToken,
            ]);

            return redirect('/cart')->with('error', 'Не найден ожидающий платеж');
        }

        $paymentStatus = $this->waitForYooKassaPaymentStatus((string) $pendingPayment['payment_id']);
        if (!$paymentStatus['success']) {
            Log::warning('Checkout callback could not fetch payment status.', [
                'payment_id' => $pendingPayment['payment_id'],
                'message' => $paymentStatus['message'],
            ]);

            return redirect('/cart')->with('error', $paymentStatus['message']);
        }

        Log::info('Checkout callback payment status received.', [
            'payment_id' => $pendingPayment['payment_id'],
            'status' => $paymentStatus['status'],
        ]);

        if ($this->isYooKassaPaymentPaid($paymentStatus['status'])) {
            try {
                $this->finalizeSucceededPayment($pendingPayment);
                session()->forget('pending_payment');
                if (!empty($pendingPayment['checkout_token'])) {
                    Cache::forget($this->pendingPaymentCacheKey((string) $pendingPayment['checkout_token']));
                }

                Log::info('Checkout payment finalized successfully.', [
                    'payment_id' => $pendingPayment['payment_id'],
                    'buyer_id' => $pendingPayment['buyer_id'] ?? null,
                ]);

                return response()->view('checkout-success');
            } catch (\Throwable $e) {
                Log::error('Checkout payment finalization failed.', [
                    'payment_id' => $pendingPayment['payment_id'],
                    'buyer_id' => $pendingPayment['buyer_id'] ?? null,
                    'error' => $e->getMessage(),
                ]);

                return redirect('/cart')->with('error', 'Ошибка при создании заказа: ' . $e->getMessage());
            }
        }

        if ($paymentStatus['status'] === 'canceled') {
            session()->forget('pending_payment');
            if (!empty($pendingPayment['checkout_token'])) {
                Cache::forget($this->pendingPaymentCacheKey((string) $pendingPayment['checkout_token']));
            }
            return redirect('/cart')->with('error', 'Оплата отменена');
        }

        return redirect('/cart')->with('error', 'Платеж еще не завершен. Если оплата прошла, нажмите «Вернуться на сайт» на странице ЮKassa еще раз или обновите страницу возврата.');
    }

    protected function loadCheckoutCartItems(int $userId, array $pictureIdsArray)
    {
        return Cart::where('user_id', $userId)
            ->whereIn('picture_id', $pictureIdsArray)
            ->with(['picture.user', 'picture.latestAuctionBid'])
            ->get();
    }

    protected function isValidPickupPoint(string $pickupPoint): bool
    {
        $normalized = mb_strtolower(trim($pickupPoint));

        if (preg_match('/бари\s+галеева/u', $normalized)) {
            return false;
        }

        return mb_strlen($pickupPoint) >= 5;
    }

    protected function createYooKassaPayment(int $totalAmount, int $userId, string $pictureIds, string $pickupPoint, string $recipientName, string $returnUrl): array
    {
        $idempotenceKey = uniqid('', true);
        $paymentData = [
            'amount' => [
                'value' => number_format($totalAmount, 2, '.', ''),
                'currency' => 'RUB',
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $returnUrl,
            ],
            'description' => 'Покупка картин на сайте Канвас',
            'metadata' => [
                'user_id' => $userId,
                'picture_ids' => $pictureIds,
                'pickup_point' => $pickupPoint,
                'recipient_name' => $recipientName,
            ],
        ];

        $headers = [
            'Content-Type: application/json',
            'Idempotence-Key: ' . $idempotenceKey,
            'Authorization: Basic ' . base64_encode($this->getYooKassaShopId() . ':' . $this->getYooKassaSecretKey()),
        ];

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => json_encode($paymentData, JSON_UNESCAPED_UNICODE),
                'ignore_errors' => true,
                'timeout' => 30,
            ],
        ]);

        $result = @file_get_contents('https://api.yookassa.ru/v3/payments', false, $context);
        if ($result === false) {
            return ['success' => false, 'message' => 'Ошибка при создании платежа'];
        }

        $response = json_decode($result, true);
        if (!is_array($response) || empty($response['id']) || empty($response['confirmation']['confirmation_url'])) {
            return ['success' => false, 'message' => 'ЮKassa вернула некорректный ответ'];
        }

        return [
            'success' => true,
            'payment_id' => $response['id'],
            'confirmation_url' => $response['confirmation']['confirmation_url'],
        ];
    }

    protected function fetchYooKassaPaymentStatus(string $paymentId): array
    {
        $headers = [
            'Authorization: Basic ' . base64_encode($this->getYooKassaShopId() . ':' . $this->getYooKassaSecretKey()),
        ];

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'ignore_errors' => true,
                'timeout' => 30,
            ],
        ]);

        $result = @file_get_contents("https://api.yookassa.ru/v3/payments/{$paymentId}", false, $context);
        if ($result === false) {
            return ['success' => false, 'message' => 'Ошибка проверки платежа'];
        }

        $paymentInfo = json_decode($result, true);
        if (!is_array($paymentInfo) || empty($paymentInfo['status'])) {
            return ['success' => false, 'message' => 'Не удалось получить статус платежа'];
        }

        return ['success' => true, 'status' => $paymentInfo['status']];
    }

    protected function waitForYooKassaPaymentStatus(string $paymentId): array
    {
        $lastStatus = ['success' => false, 'message' => 'Не удалось получить статус платежа'];

        for ($attempt = 0; $attempt < 6; $attempt++) {
            $lastStatus = $this->fetchYooKassaPaymentStatus($paymentId);

            if (
                !$lastStatus['success']
                || $this->isYooKassaPaymentPaid($lastStatus['status'])
                || $lastStatus['status'] === 'canceled'
            ) {
                return $lastStatus;
            }

            sleep(1);
        }

        return $lastStatus;
    }

    protected function isYooKassaPaymentPaid(string $status): bool
    {
        return in_array($status, ['succeeded', 'waiting_for_capture'], true);
    }

    protected function pendingPaymentCacheKey(string $checkoutToken): string
    {
        return 'pending_payment:' . $checkoutToken;
    }

    protected function finalizeSucceededPayment(array $pendingPayment): void
    {
        DB::transaction(function () use ($pendingPayment) {
            $paymentId = (string) $pendingPayment['payment_id'];

            Log::info('Checkout finalization transaction started.', [
                'payment_id' => $paymentId,
                'buyer_id' => $pendingPayment['buyer_id'] ?? null,
                'cart_items_count' => count($pendingPayment['cart_items'] ?? []),
            ]);

            if (Order::where('payment_id', $paymentId)->where('payment_status', 'succeeded')->exists()) {
                Log::info('Checkout finalization skipped because order already exists.', [
                    'payment_id' => $paymentId,
                ]);

                return;
            }

            $userId = (int) ($pendingPayment['buyer_id'] ?? session('user_id'));
            $pickupPoint = (string) $pendingPayment['pickup_point'];
            $recipientName = (string) $pendingPayment['recipient_name'];
            $keepSoldInGallery = (bool) ($pendingPayment['keep_sold_in_gallery'] ?? false);
            $cartItems = collect($pendingPayment['cart_items'] ?? []);

            $sellerCounts = [];

            foreach ($cartItems as $item) {
                $pictureId = (int) $item['picture_id'];
                $picture = \App\Models\Picture::with('user')->lockForUpdate()->findOrFail($pictureId);

                $uniqueCode = $this->generateUniqueCode();

                $order = Order::create([
                    'buyer_id' => $userId,
                    'seller_id' => (int) $item['seller_id'],
                    'picture_id' => $pictureId,
                    'price' => (int) $item['price'],
                    'pickup_point' => $pickupPoint,
                    'recipient_name' => $recipientName,
                    'unique_code' => $uniqueCode,
                    'payment_id' => $paymentId,
                    'payment_status' => 'succeeded',
                    'status' => 'waiting_shipment',
                ]);

                try {
                    NotificationService::push(
                        (int) $item['seller_id'],
                        'picture_sold',
                        'Картина продана',
                        'Картина "' . $picture->name . '" успешно оплачена покупателем.',
                        url('/orders'),
                        $pictureId,
                        $order->id
                    );
                } catch (\Throwable $e) {
                    Log::warning('Seller notification failed during checkout finalization.', [
                        'payment_id' => $paymentId,
                        'order_id' => $order->id,
                        'seller_id' => (int) $item['seller_id'],
                        'error' => $e->getMessage(),
                    ]);
                }

                $this->updateSoldVisibilityIfSupported($picture, $keepSoldInGallery);

                if ($keepSoldInGallery) {
                    Cart::where('user_id', $userId)->where('picture_id', $pictureId)->delete();
                } else {
                    $picture->cartEntries()->delete();
                    $picture->favoriteEntries()->delete();
                }

                $sellerCounts[(int) $item['seller_id']] = ($sellerCounts[(int) $item['seller_id']] ?? 0) + 1;
            }

            User::whereKey($userId)->increment('orders_count', $cartItems->count());

            foreach ($sellerCounts as $sellerId => $count) {
                User::whereKey($sellerId)->increment('orders_count', $count);
            }

            Log::info('Checkout finalization transaction completed.', [
                'payment_id' => $paymentId,
                'buyer_id' => $userId,
                'orders_count' => $cartItems->count(),
            ]);
        });
    }

    protected function generateUniqueCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (Order::where('unique_code', $code)->exists());

        return $code;
    }

    protected function updateSoldVisibilityIfSupported(\App\Models\Picture $picture, bool $keepSoldInGallery): void
    {
        try {
            if (
                Schema::hasColumn('pictures', 'show_sold_badge')
                && Schema::hasColumn('pictures', 'hidden_after_sale')
            ) {
                DB::table('pictures')
                    ->where('id', $picture->id)
                    ->update([
                        'show_sold_badge' => $keepSoldInGallery,
                        'hidden_after_sale' => !$keepSoldInGallery,
                    ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Sale visibility columns are unavailable during checkout finalization.', [
                'picture_id' => $picture->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function getYooKassaShopId(): string
    {
        return (string) env('YOOKASSA_SHOP_ID', '1178497');
    }

    protected function getYooKassaSecretKey(): string
    {
        return (string) env('YOOKASSA_SECRET_KEY', 'test_KRP-CbJQKnnfEq2Eueb58MhSf6KAsyu8GDH3mD1V2Vk');
    }
}
