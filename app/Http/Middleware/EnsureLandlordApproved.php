<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureLandlordApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'landlord') {
            if ($user->landlord_status === 'pending') {
                return redirect()->route('landlord.pending');
            }

            if ($user->landlord_status === 'rejected') {
                return redirect()->route('landlord.rejected');
            }
        }

        return $next($request);
    }
}
