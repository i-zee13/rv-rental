<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['super_admin','admin','manager'])) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
