<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Picture;

class CartController extends Controller
{
    public function index()
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');
        $user_id = session('user_id');

        $cartItems = Cart::where('user_id', $user_id)
            ->with(['picture.user'])
            ->orderByDesc('added_at')
            ->get();

        $totalPrice = $cartItems->sum(function ($item) {
            return $item->picture->price;
        });

        return view('cart', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'cartItems', 'totalPrice'
        ));
    }
}
