<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Preview • Smart Rental</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-slate-950 text-white min-h-screen">
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="text-white/80 hover:text-white">← Back to Home</a>

            <div class="flex gap-2">
                @guest
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg border border-white/20 hover:bg-white/10">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-400 font-medium">Register</a>
                @endguest

                @auth
                    <a href="{{ route('rooms.index') }}" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-medium">Go to Rooms</a>
                @endauth
            </div>
        </div>

        <h1 class="mt-8 text-3xl font-semibold">Featured Rooms (Public Preview)</h1>
        <p class="mt-2 text-white/70">For full browsing, filtering, and recommendation — login is required.</p>

        <div class="mt-8 grid md:grid-cols-3 gap-4">
            @php
                $rooms = [
                    ['title' => 'Sunrise Studio', 'price' => 'RM 650/mo', 'desc' => 'WiFi • Near campus • Private bathroom'],
                    ['title' => 'City View Room', 'price' => 'RM 520/mo', 'desc' => 'Furnished • Budget friendly • Safe area'],
                    ['title' => 'Garden Shared House', 'price' => 'RM 450/mo', 'desc' => 'Shared • Utilities included • Spacious'],
                ];
            @endphp

            @foreach($rooms as $r)
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <div class="text-lg font-semibold">{{ $r['title'] }}</div>
                    <div class="text-sm text-indigo-300 mt-1">{{ $r['price'] }}</div>
                    <div class="text-sm text-white/70 mt-3">{{ $r['desc'] }}</div>

                    <a href="{{ route('rooms.index') }}"
                       class="mt-5 inline-flex w-full justify-center px-4 py-2 rounded-xl bg-white text-slate-900 font-semibold hover:bg-white/90">
                        View Details (Login Required)
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
