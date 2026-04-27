<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user_id') || session('user_role') != 2) {
            abort(404);
        }

        return $next($request);
    }
}
