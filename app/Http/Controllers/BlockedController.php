<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class BlockedController extends Controller
{
    public function index()
    {
        $userId = session('user_id') ?: session('blocked_user_id');
        $user = $userId ? User::find($userId) : null;

        if (!$user || !$user->banned_until || $user->banned_until->isPast()) {
            session()->forget('blocked_user_id');
            return redirect('/auth');
        }

        return view('blocked', compact('user'));
    }
}
