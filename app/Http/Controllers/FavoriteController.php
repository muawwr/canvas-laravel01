<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function index()
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');
        $user_id = session('user_id');

        $favorites = Favorite::where('user_id', $user_id)
            ->with(['picture.user'])
            ->orderByDesc('added_at')
            ->get();

        return view('favorites', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'favorites'
        ));
    }
}
