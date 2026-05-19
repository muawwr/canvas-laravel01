<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictAdminRoutes
{
    public function handle(Request $request, Closure $next)
    {
        if (session('user_role') != 2) {
            return $next($request);
        }

        $allowedPaths = [
            '/',
            'main',
            'admin',
            'logout',
            'api/picture/moderate',
            'api/categories',
            'api/orders/status',
        ];

        foreach ($allowedPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        return redirect('/admin');
    }
}
