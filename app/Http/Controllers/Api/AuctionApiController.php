<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuctionBid;
use App\Models\Cart;
use App\Models\Picture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuctionApiController extends Controller
{
    public function bid(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 403);
        }

        $validator = Validator::make($request->all(), [
            'picture_id' => ['required', 'integer', 'exists:pictures,id'],
            'amount' => ['required', 'integer', 'min:1'],
        ], [
            'amount.required' => 'Укажите сумму ставки',
            'amount.integer' => 'Ставка должна быть числом',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $userId = session('user_id');
        $pictureId = (int) $request->picture_id;
        $amount = (int) $request->amount;

        try {
            $result = DB::transaction(function () use ($pictureId, $userId, $amount) {
                $picture = Picture::where('id', $pictureId)
                    ->where('status', 'approved')
                    ->where('listing_type', 'auction')
                    ->lockForUpdate()
                    ->first();

                if (!$picture) {
                    return ['success' => false, 'message' => 'Аукцион не найден'];
                }

                if ($picture->user_id == $userId) {
                    return ['success' => false, 'message' => 'Нельзя делать ставку на свою картину'];
                }

                if ($picture->auction_ends_at && $picture->auction_ends_at->isPast()) {
                    return ['success' => false, 'message' => 'Аукцион уже завершен'];
                }

                if ($picture->orders()->where('payment_status', 'succeeded')->exists()) {
                    return ['success' => false, 'message' => 'Картина уже куплена'];
                }

                $currentPrice = $picture->auction_current_price ?? $picture->auction_start_price ?? $picture->price;
                $minStep = $picture->auction_min_step ?? 50;
                $minBid = $currentPrice + $minStep;

                if ($amount < $minBid) {
                    return [
                        'success' => false,
                        'message' => 'Минимальная ставка: ' . number_format($minBid, 0, '.', ' ') . ' ₽',
                    ];
                }

                AuctionBid::create([
                    'picture_id' => $picture->id,
                    'user_id' => $userId,
                    'amount' => $amount,
                ]);

                $picture->update([
                    'auction_current_price' => $amount,
                    'price' => $amount,
                ]);

                return [
                    'success' => true,
                    'message' => 'Ставка принята',
                    'current_price' => $amount,
                    'min_next_bid' => $amount + $minStep,
                    'bids_count' => $picture->auctionBids()->count(),
                    'leader_name' => session('user_name', 'Вы'),
                    'user_status' => 'Вы лидируете',
                ];
            });

            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()], 500);
        }
    }

    public function buyout(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 403);
        }

        $validator = Validator::make($request->all(), [
            'picture_id' => ['required', 'integer', 'exists:pictures,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $userId = session('user_id');
        $pictureId = (int) $request->picture_id;

        try {
            $result = DB::transaction(function () use ($pictureId, $userId, $request) {
                $picture = Picture::where('id', $pictureId)
                    ->where('status', 'approved')
                    ->where('listing_type', 'auction')
                    ->lockForUpdate()
                    ->first();

                if (!$picture) {
                    return ['success' => false, 'message' => 'Аукцион не найден'];
                }

                if ($picture->user_id == $userId) {
                    return ['success' => false, 'message' => 'Нельзя купить свою картину'];
                }

                if (!$picture->auction_buyout_price) {
                    return ['success' => false, 'message' => 'Для этой картины не указана блиц-цена'];
                }

                $currentPrice = $picture->auction_current_price ?? $picture->auction_start_price ?? $picture->price;
                if ($currentPrice > $picture->auction_buyout_price) {
                    return ['success' => false, 'message' => 'Блиц-цена уже недоступна'];
                }

                if ($picture->auction_ends_at && $picture->auction_ends_at->isPast()) {
                    return ['success' => false, 'message' => 'Аукцион уже завершен'];
                }

                if ($picture->orders()->where('payment_status', 'succeeded')->exists()) {
                    return ['success' => false, 'message' => 'Картина уже куплена'];
                }

                $price = (int) $picture->auction_buyout_price;

                AuctionBid::create([
                    'picture_id' => $picture->id,
                    'user_id' => $userId,
                    'amount' => $price,
                ]);

                $picture->update([
                    'auction_current_price' => $price,
                    'price' => $price,
                    'auction_ends_at' => now(),
                ]);

                Cart::updateOrCreate([
                    'user_id' => $userId,
                    'picture_id' => $picture->id,
                ]);

                return [
                    'success' => true,
                    'message' => 'Картина добавлена в корзину',
                    'redirect_url' => url('/cart'),
                ];
            });

            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()], 500);
        }
    }
}
