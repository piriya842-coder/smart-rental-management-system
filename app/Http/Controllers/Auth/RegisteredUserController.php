<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // if your register form has role dropdown:
            // 'role' => ['required', 'in:student,landlord,admin'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

            // IMPORTANT:
            // If no role field in form, default student:
            'role' => $request->role ?? 'student',
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Redirect by role (NO route('dashboard') error anymore)
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'landlord' => redirect()->route('landlord.dashboard'),
            default => redirect()->route('student.dashboard'),
        };
    }
}
