<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout');
    }

    public function process(Request $request)
    {
        $user_id = session('user_id');

        $picture_ids = $request->picture_ids;
        $pickup_point = trim($request->pickup_point ?? '');
        $recipient_name = trim($request->recipient_name ?? '');

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
