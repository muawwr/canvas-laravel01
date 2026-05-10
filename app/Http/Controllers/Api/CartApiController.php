<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Picture;
use App\Models\User;
use App\Services\UserBanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CartApiController extends Controller
{
    public function handle(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 401);
        }

        $user_id = session('user_id');
        $user = User::find($user_id);
        $action = $request->action;
        $picture_id = intval($request->picture_id ?? 0);

        if (!in_array($action, ['add', 'remove', 'get', 'clear'])) {
            return response()->json(['success' => false, 'message' => 'Некорректное действие'], 400);
        }

        if ($action === 'add') {
            $restrictionMessage = $user ? UserBanService::getRestrictionMessage($user) : null;
            if ($restrictionMessage) {
                return response()->json(['success' => false, 'message' => $restrictionMessage], 403);
            }

            if ($picture_id <= 0) {
                return response()->json(['success' => false, 'message' => 'Некорректный ID картины'], 400);
            }

            $picture = Picture::where('id', $picture_id)
                ->where('status', 'approved')
                ->where('listing_type', 'gallery')
                ->first();
            if (!$picture) {
                return response()->json(['success' => false, 'message' => 'Картина не найдена'], 404);
            }

            if (
                (Schema::hasColumn('pictures', 'hidden_after_sale') && $picture->hidden_after_sale)
                || (Schema::hasColumn('pictures', 'show_sold_badge') && $picture->show_sold_badge)
                || $picture->orders()->where('payment_status', 'succeeded')->exists()
            ) {
                return response()->json(['success' => false, 'message' => 'Эта картина уже продана']);
            }

            if ($picture->user_id == $user_id) {
                return response()->json(['success' => false, 'message' => 'Нельзя добавить в корзину свою картину']);
            }

            $exists = Cart::where('user_id', $user_id)->where('picture_id', $picture_id)->exists();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Товар уже в корзине']);
            }

            Cart::create(['user_id' => $user_id, 'picture_id' => $picture_id]);
            $cart_count = Cart::where('user_id', $user_id)->count();

            return response()->json(['success' => true, 'message' => 'Добавлено в корзину', 'cart_count' => $cart_count]);
        }

        if ($action === 'remove') {
            $lockedAuctionCartItem = Cart::where('user_id', $user_id)
                ->where('picture_id', $picture_id)
                ->whereHas('picture', function ($query) use ($user_id) {
                    $query->where('listing_type', 'auction')
                        ->whereNotNull('auction_ends_at')
                        ->where('auction_ends_at', '<=', now())
                        ->whereHas('latestAuctionBid', function ($bidQuery) use ($user_id) {
                            $bidQuery->where('user_id', $user_id);
                        })
                        ->whereDoesntHave('orders', function ($orderQuery) {
                            $orderQuery->where('payment_status', 'succeeded');
                        });
                })
                ->exists();

            if ($lockedAuctionCartItem) {
                return response()->json(['success' => false, 'message' => 'Эту аукционную картину нельзя удалить из корзины до оплаты']);
            }

            $deleted = Cart::where('user_id', $user_id)->where('picture_id', $picture_id)->delete();

            if ($deleted) {
                $cart_count = Cart::where('user_id', $user_id)->count();
                return response()->json(['success' => true, 'message' => 'Удалено из корзины', 'cart_count' => $cart_count]);
            }

            return response()->json(['success' => false, 'message' => 'Товар не найден в корзине']);
        }

        if ($action === 'get') {
            $items = Cart::where('user_id', $user_id)
                ->with(['picture.user'])
                ->orderByDesc('added_at')
                ->get()
                ->map(function ($item) {
                    return [
                        'picture_id' => $item->picture_id,
                        'name' => $item->picture->name,
                        'img' => $item->picture->img,
                        'price' => $item->picture->price,
                        'seller_id' => $item->picture->user_id,
                        'author_name' => $item->picture->user->name,
                    ];
                });

            $total = $items->sum('price');

            return response()->json(['success' => true, 'items' => $items, 'count' => $items->count(), 'total' => $total]);
        }

        Cart::where('user_id', $user_id)->delete();
        return response()->json(['success' => true, 'message' => 'Корзина очищена']);
    }
}
