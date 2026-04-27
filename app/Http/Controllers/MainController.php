<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Picture;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');

        // Получаем картины для галереи на главной (одобренные)
        $pictures = Picture::where('status', 'approved')
            ->with('user')
            ->inRandomOrder()
            ->limit(12)
            ->get();

        // Получаем топ художников по продажам
        $topArtists = User::select('users.*')
            ->join('pictures', 'pictures.user_id', '=', 'users.id')
            ->join('orders', 'orders.picture_id', '=', 'pictures.id')
            ->where('orders.payment_status', 'succeeded')
            ->groupBy('users.id')
            ->orderByRaw('COUNT(DISTINCT orders.id) DESC')
            ->limit(4)
            ->get();

        return view('main', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'pictures', 'topArtists'
        ));
    }
}
