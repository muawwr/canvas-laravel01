<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\UserBanService;
use Closure;
use Illuminate\Http\Request;

class CheckUserBan
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('user_id')) {
            $user = User::find(session('user_id'));

            if ($user) {
                UserBanService::processAuctionPaymentViolations($user);
            }
        }

        return $next($request);
    }
}
