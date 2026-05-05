<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Picture;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $auctionPicture = null;
        $auctionPictureId = (int) ($request->auction ?? session('auction_buyout_picture_id', 0));

        if ($auctionPictureId > 0) {
            $auctionPicture = Picture::where('id', $auctionPictureId)
                ->where('status', 'approved')
                ->where('listing_type', 'auction')
                ->with('user')
                ->first();
        }

        return view('checkout', compact('auctionPicture'));
    }

    public function process(Request $request)
    {
        $user_id = session('user_id');

        $picture_ids = $request->picture_ids;
        $pickup_point = trim($request->pickup_point ?? '');
        $recipient_name = trim($request->recipient_name ?? '');
        $auction_picture_id = (int) ($request->auction_picture_id ?? 0);

        if ($auction_picture_id > 0) {
            if (empty($pickup_point) || empty($recipient_name)) {
                return redirect('/checkout?auction=' . $auction_picture_id)->with('error', 'Заполните все поля');
            }

            return DB::transaction(function () use ($auction_picture_id, $user_id, $pickup_point, $recipient_name) {
                $picture = Picture::where('id', $auction_picture_id)
                    ->where('status', 'approved')
                    ->where('listing_type', 'auction')
                    ->lockForUpdate()
                    ->first();

                if (!$picture || !$picture->auction_buyout_price) {
                    return redirect('/auction')->with('error', 'Аукцион не найден');
                }

                if ($picture->user_id == $user_id) {
                    return redirect('/auction')->with('error', 'Нельзя купить свою картину');
                }

                if ($picture->auction_ends_at && $picture->auction_ends_at->isPast()) {
                    return redirect('/auction')->with('error', 'Аукцион уже завершен');
                }

                if ($picture->orders()->where('payment_status', 'succeeded')->exists()) {
                    return redirect('/auction')->with('error', 'Картина уже куплена');
                }

                $price = (int) $picture->auction_buyout_price;
                $unique_code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

                Order::create([
                    'buyer_id' => $user_id,
                    'seller_id' => $picture->user_id,
                    'picture_id' => $picture->id,
                    'price' => $price,
                    'pickup_point' => $pickup_point,
                    'recipient_name' => $recipient_name,
                    'unique_code' => $unique_code,
                    'status' => 'waiting_shipment',
                    'payment_status' => 'succeeded',
                ]);

                $picture->update([
                    'auction_current_price' => $price,
                    'price' => $price,
                    'auction_ends_at' => now(),
                ]);

                session()->forget('auction_buyout_picture_id');

                return redirect('/orders')->with('success', 'Заказ оформлен!');
            });
        }

        if (empty($picture_ids) || empty($pickup_point) || empty($recipient_name)) {
            return redirect('/cart')->with('error', 'Заполните все поля');
        }

        $picture_ids_array = explode(',', $picture_ids);

        $cartItems = Cart::where('user_id', $user_id)
            ->whereIn('picture_id', $picture_ids_array)
            ->with('picture.user')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect('/cart')->with('error', 'Товары не найдены');
        }

        // Создаем заказы для каждой картины
        foreach ($cartItems as $item) {
            $unique_code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            Order::create([
                'buyer_id' => $user_id,
                'seller_id' => $item->picture->user_id,
                'picture_id' => $item->picture_id,
                'price' => $item->picture->price,
                'pickup_point' => $pickup_point,
                'recipient_name' => $recipient_name,
                'unique_code' => $unique_code,
                'status' => 'waiting_shipment',
                'payment_status' => 'succeeded',
            ]);

            // Удаляем из корзины
            $item->delete();
        }

        return redirect('/orders')->with('success', 'Заказ оформлен!');
    }
}
