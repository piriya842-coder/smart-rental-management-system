<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->role ?? null) !== 'admin') {
            abort(403, 'Admin access only.');
        }

        return $next($request);
    }
}
