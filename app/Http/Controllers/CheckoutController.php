<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $auctionPicture = null;
        return view('checkout', compact('auctionPicture'));
    }

    public function process(Request $request)
    {
        $userId = session('user_id');
        $pictureIds = $request->picture_ids;
        $pickupPoint = trim($request->pickup_point ?? '');
        $recipientName = trim($request->recipient_name ?? '');

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

            Order::create([
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

            $item->delete();
        }

        return redirect('/orders')->with('success', 'Заказ оформлен!');
    }
}
