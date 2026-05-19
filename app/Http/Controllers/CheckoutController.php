<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\UserBanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            return redirect('/cart')->with('error', 'Адрес должен быть в формате: г. Москва, ул. Ленина, д. 1.');
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
        $payment = $this->createYooKassaPayment($totalAmount, $userId, $pictureIds, $pickupPoint, $recipientName);

        if (!$payment['success']) {
            return redirect('/cart')->with('error', $payment['message']);
        }

        session([
            'pending_payment' => [
                'payment_id' => $payment['payment_id'],
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
            ],
        ]);

        return redirect()->away($payment['confirmation_url']);
    }

    public function callback(Request $request)
    {
        $pendingPayment = session('pending_payment');
        if (!$pendingPayment || empty($pendingPayment['payment_id'])) {
            return redirect('/cart')->with('error', 'Не найден ожидающий платеж');
        }

        $paymentStatus = $this->fetchYooKassaPaymentStatus((string) $pendingPayment['payment_id']);
        if (!$paymentStatus['success']) {
            return redirect('/cart')->with('error', $paymentStatus['message']);
        }

        if ($paymentStatus['status'] === 'succeeded') {
            try {
                $this->finalizeSucceededPayment($pendingPayment);
                session()->forget('pending_payment');

                return response()->view('checkout-success');
            } catch (\Throwable $e) {
                return redirect('/cart')->with('error', 'Ошибка при создании заказа: ' . $e->getMessage());
            }
        }

        if ($paymentStatus['status'] === 'canceled') {
            session()->forget('pending_payment');
            return redirect('/cart')->with('error', 'Оплата отменена');
        }

        return redirect('/cart')->with('error', 'Платеж еще не завершен');
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

        return (bool) preg_match('/^г\.\s*[^,]+,\s*ул\.\s*[^,]+,\s*д\.\s*\d+[а-яёa-z]?$/ui', $pickupPoint);
    }

    protected function createYooKassaPayment(int $totalAmount, int $userId, string $pictureIds, string $pickupPoint, string $recipientName): array
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
                'return_url' => route('checkout.callback'),
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

    protected function finalizeSucceededPayment(array $pendingPayment): void
    {
        DB::transaction(function () use ($pendingPayment) {
            $paymentId = (string) $pendingPayment['payment_id'];

            if (Order::where('payment_id', $paymentId)->where('payment_status', 'succeeded')->exists()) {
                return;
            }

            $userId = (int) session('user_id');
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

                NotificationService::push(
                    (int) $item['seller_id'],
                    'picture_sold',
                    'Картина продана',
                    'Картина "' . $picture->name . '" успешно оплачена покупателем.',
                    url('/orders'),
                    $pictureId,
                    $order->id
                );

                if (
                    Schema::hasColumn('pictures', 'show_sold_badge')
                    && Schema::hasColumn('pictures', 'hidden_after_sale')
                ) {
                    $picture->update([
                        'show_sold_badge' => $keepSoldInGallery,
                        'hidden_after_sale' => !$keepSoldInGallery,
                    ]);
                }

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
        });
    }

    protected function generateUniqueCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (Order::where('unique_code', $code)->exists());

        return $code;
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
