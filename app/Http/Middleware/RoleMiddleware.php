<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['auth' => 'Silakan login terlebih dahulu']);
        }

        $user = Auth::user();

        foreach ($roles as $role) {
            if (strcasecmp($user->role, $role) === 0) {
                return $next($request);
            }
        }

        return redirect()->route('login')->withErrors(['role' => 'Anda tidak memiliki akses']);
    }
}
