<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Picture;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Genre;
use App\Models\Style;
use App\Models\Era;

class PictureController extends Controller
{
    public function show($id)
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');

        $picture = Picture::with(['user', 'genre', 'style', 'era', 'latestAuctionBid.user'])
            ->withCount('auctionBids')
            ->when(session()->has('user_id'), function ($query) {
                $query->with(['auctionBids' => function ($bidsQuery) {
                    $bidsQuery
                        ->where('user_id', session('user_id'))
                        ->orderByDesc('created_at');
                }]);
            })
            ->where('status', 'approved')
            ->findOrFail($id);

        // Количество лайков
        $likes_count = Favorite::where('picture_id', $id)->count();

        // Проверка в избранном
        $is_in_favorites = false;
        if ($is_logged_in) {
            $is_in_favorites = Favorite::where('user_id', session('user_id'))
                ->where('picture_id', $id)->exists();
        }

        // Проверка продано
        $is_sold = Order::where('picture_id', $id)
            ->where('payment_status', 'succeeded')->exists();

        return view('picture.show', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'picture', 'likes_count', 'is_in_favorites', 'is_sold'
        ));
    }

    public function create()
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');

        $genres = Genre::orderBy('name')->get();
        $styles = Style::orderBy('name')->get();
        $eras = Era::orderBy('name')->get();

        return view('picture.create', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'genres', 'styles', 'eras'
        ));
    }

    public function edit($id)
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');

        $picture = Picture::where('user_id', session('user_id'))->findOrFail($id);

        $genres = Genre::orderBy('name')->get();
        $styles = Style::orderBy('name')->get();
        $eras = Era::orderBy('name')->get();

        return view('picture.edit', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'picture', 'genres', 'styles', 'eras'
        ));
    }
}
