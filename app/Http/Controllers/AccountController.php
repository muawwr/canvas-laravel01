<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Picture;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\AuctionBid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');

        $viewed_user_id = $request->user_id ? intval($request->user_id) : session('user_id');
        $is_own_profile = ($viewed_user_id == session('user_id'));

        $user_data = User::findOrFail($viewed_user_id);

        // Актуальное количество картин
        $picturesCountQuery = Picture::where('user_id', $viewed_user_id)
            ->where('status', 'approved');

        if (Schema::hasColumn('pictures', 'hidden_after_sale')) {
            $picturesCountQuery->where('hidden_after_sale', false);
        }

        $user_data->pictures_count = $picturesCountQuery->count();

        // Актуальное количество заказов
        $user_data->orders_count = Order::where(function ($q) use ($viewed_user_id) {
            $q->where('buyer_id', $viewed_user_id)
                ->orWhere('seller_id', $viewed_user_id);
        })->where('payment_status', 'succeeded')
            ->distinct('id')->count();

        // Количество проданных картин
        $sold_count = DB::table('orders')
            ->join('pictures', 'orders.picture_id', '=', 'pictures.id')
            ->where('pictures.user_id', $viewed_user_id)
            ->where('orders.payment_status', 'succeeded')
            ->distinct('orders.picture_id')
            ->count('orders.picture_id');

        // Определяем ранг
        if ($sold_count < 5) {
            $rank_label = 'НОВИЧОК';
            $rank_icon = 'newbie.svg';
            $rank_class = 'account_badge_newbie';
        }
        elseif ($sold_count < 10) {
            $rank_label = 'ОПЫТНЫЙ';
            $rank_icon = 'experienced.svg';
            $rank_class = 'account_badge_experienced';
        }
        else {
            $rank_label = 'ЭКСПЕРТ';
            $rank_icon = 'expert.svg';
            $rank_class = 'account_badge_expert';
        }

        // Увеличиваем счетчик просмотров профиля
        if (!$is_own_profile) {
            $viewed_profiles = session('viewed_profiles', []);
            if (!in_array($viewed_user_id, $viewed_profiles)) {
                $user_data->increment('profile_views');
                $viewed_profiles[] = $viewed_user_id;
                session(['viewed_profiles' => $viewed_profiles]);
            }
        }

        // Получаем картины
        $userPicturesQuery = Picture::where('user_id', $viewed_user_id)
            ->where('status', 'approved');

        if (Schema::hasColumn('pictures', 'hidden_after_sale')) {
            $userPicturesQuery->where('hidden_after_sale', false);
        }

        $user_pictures = $userPicturesQuery
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($picture) {
            $picture->is_sold = ($picture->show_sold_badge ?? false)
                || Order::where('picture_id', $picture->id)
                    ->where('payment_status', 'succeeded')
                    ->exists();

            $picture->has_failed_auction = $picture->listing_type === 'auction'
                && $picture->is_sold == 0
                && $picture->auction_ends_at
                && $picture->auction_ends_at->isPast()
                && Order::where('picture_id', $picture->id)
                    ->where('payment_status', 'canceled')
                    ->exists();

            $picture->failed_auction_reason = $picture->has_failed_auction
                ? 'Победитель аукциона не оплатил картину в течение 24 часов'
                : null;

            return $picture;
        });

        return view('account', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'user_data', 'is_own_profile', 'user_pictures',
            'rank_label', 'rank_icon', 'rank_class'
        ));
    }
}
