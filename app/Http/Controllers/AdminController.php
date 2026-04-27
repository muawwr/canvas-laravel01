<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use App\Models\Style;
use App\Models\Era;
use App\Models\User;
use App\Models\Picture;
use App\Models\Order;

class AdminController extends Controller
{
    public function index()
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');

        $genres = Genre::orderBy('name')->get();
        $styles = Style::orderBy('name')->get();
        $eras = Era::orderBy('name')->get();

        $users = User::orderByDesc('date_of_reg')->get();

        $pending_pictures = Picture::where('status', 'pending')
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        $deals = Order::where('payment_status', 'succeeded')
            ->with(['seller', 'buyer', 'picture'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'genres', 'styles', 'eras', 'users',
            'pending_pictures', 'deals'
        ));
    }
}
