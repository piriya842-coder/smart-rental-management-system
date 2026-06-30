<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // If Laravel had an "intended" URL, go there first
        if (session()->has('url.intended')) {
            return redirect()->intended();
        }

        // Otherwise redirect by role
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'landlord' => redirect()->route('landlord.dashboard'),
            default => redirect()->route('student.dashboard'),
        };
    }

   public function destroy(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // ✅ Redirect to landing page instead of login
    return redirect()->route('home');
}
}
