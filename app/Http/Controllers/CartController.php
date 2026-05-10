<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Picture;
use App\Services\NotificationService;

class CartController extends Controller
{
    public function index()
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');
        $user_id = session('user_id');

        if ($is_logged_in) {
            $wonAuctionIds = Picture::where('status', 'approved')
                ->where('listing_type', 'auction')
                ->whereNotNull('auction_ends_at')
                ->where('auction_ends_at', '<=', now())
                ->where('user_id', '!=', $user_id)
                ->whereDoesntHave('orders', function ($query) {
                    $query->where('payment_status', 'succeeded');
                })
                ->whereHas('latestAuctionBid', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })
                ->whereDoesntHave('cartEntries', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })
                ->pluck('id');

            foreach ($wonAuctionIds as $pictureId) {
                Cart::create([
                    'user_id' => $user_id,
                    'picture_id' => $pictureId,
                ]);

                $picture = Picture::find($pictureId);
                if ($picture) {
                    NotificationService::pushOnce(
                        $user_id,
                        'auction_winner',
                        'Аукцион завершен',
                        'Вы победили в аукционе по картине "' . $picture->name . '". Перейдите в корзину для оформления заказа. В случае неуплаты в течение 24 часов доступ к покупкам будет ограничен на 7 дней.',
                        url('/cart'),
                        $picture->id
                    );
                }
            }
        }

        $cartItems = Cart::where('user_id', $user_id)
            ->with(['picture.user', 'picture.latestAuctionBid'])
            ->orderByDesc('added_at')
            ->get();

        $totalPrice = $cartItems->sum(function ($item) {
            return $item->picture->price;
        });

        return view('cart', compact(
            'is_logged_in',
            'user_avatar',
            'user_name',
            'cartItems',
            'totalPrice'
        ));
    }
}
