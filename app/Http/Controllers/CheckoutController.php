<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\UserBanService;
use Illuminate\Http\Request;
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
        $userId = session('user_id');
        $user = User::find($userId);
        $restrictionMessage = $user ? UserBanService::getRestrictionMessage($user) : null;

        if ($restrictionMessage) {
            return redirect('/cart')->with('error', $restrictionMessage);
        }

        $pictureIds = $request->picture_ids;
        $pickupPoint = trim($request->pickup_point ?? '');
        $recipientName = trim($request->recipient_name ?? '');
        $keepSoldInGallery = $request->boolean('keep_sold_in_gallery');

        if (empty($pictureIds) || empty($pickupPoint) || empty($recipientName)) {
            return redirect('/cart')->with('error', 'Заполните все поля');
        }

        $pictureIdsArray = explode(',', $pictureIds);

        $cartItems = Cart::where('user_id', $userId)
            ->whereIn('picture_id', $pictureIdsArray)
            ->with(['picture.user', 'picture.latestAuctionBid'])
            ->get();

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

        foreach ($cartItems as $item) {
            $uniqueCode = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'buyer_id' => $userId,
                'seller_id' => $item->picture->user_id,
                'picture_id' => $item->picture_id,
                'price' => $item->picture->price,
                'pickup_point' => $pickupPoint,
                'recipient_name' => $recipientName,
                'unique_code' => $uniqueCode,
                'status' => 'waiting_shipment',
                'payment_status' => 'succeeded',
            ]);

            NotificationService::push(
                $item->picture->user_id,
                'picture_sold',
                'Картина продана',
                'Картина "' . $item->picture->name . '" успешно оплачена покупателем.',
                url('/orders'),
                $item->picture_id,
                $order->id
            );

            if (
                Schema::hasColumn('pictures', 'show_sold_badge')
                && Schema::hasColumn('pictures', 'hidden_after_sale')
            ) {
                $item->picture->update([
                    'show_sold_badge' => $keepSoldInGallery,
                    'hidden_after_sale' => !$keepSoldInGallery,
                ]);
            }

            if (!$keepSoldInGallery) {
                $item->picture->cartEntries()->delete();
                $item->picture->favoriteEntries()->delete();
            }

            $item->delete();
        }

        return redirect('/orders')->with('success', 'Заказ оформлен!');
    }
}
