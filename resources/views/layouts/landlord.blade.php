<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Landlord • Smart Rental')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            --sr-bg:#F3F4F6;
            --sr-panel:#FFFFFF;
            --sr-panel-2:#F9FAFB;
            --sr-border:#E5E7EB;

            --sr-text:#111827;
            --sr-muted:#6B7280;

            --sr-gold:#D6B36B;
            --sr-gold-2:#B88A2C;

            --sr-green:#16A34A;
            --sr-red:#DC2626;
            --sr-blue:#2563EB;

            /* landlord red theme */
            --sr-side-top:#2A0709;
            --sr-side-mid:#4D0B12;
            --sr-side-bottom:#7F101B;
            --sr-side-glow:#C81E2A;
            --sr-side-soft:#FDF2F3;
            --sr-side-border:rgba(255,255,255,.10);
            --sr-nav-border:rgba(255,255,255,.10);
            --sr-nav-bg:rgba(255,255,255,.06);
            --sr-nav-hover:rgba(255,255,255,.11);
            --sr-active-1:rgba(239,68,68,.30);
            --sr-active-2:rgba(127,16,27,.55);
            --sr-active-border:rgba(255,255,255,.18);
            --sr-active-shadow:rgba(127,16,27,.30);
        }

        html, body{
            overflow-x:hidden;
        }

        body{
            min-height:100vh;
            background:
                radial-gradient(circle at top left, rgba(214,179,107,.10), transparent 28%),
                radial-gradient(circle at bottom right, rgba(37,99,235,.06), transparent 24%),
                #F3F4F6;
            color:var(--sr-text);
        }

        .sr-muted{
            color:var(--sr-muted) !important;
        }

        .sr-glass{
            background: var(--sr-panel);
            border: 1px solid var(--sr-border);
            box-shadow: 0 10px 30px rgba(17,24,39,.06);
        }

        .sr-card{
            background:#FFFFFF;
            border:1px solid var(--sr-border);
            box-shadow: 0 8px 24px rgba(17,24,39,.05);
        }

        .sr-side{
            position: relative;
            background:
                radial-gradient(circle at top left, rgba(255,255,255,.16), transparent 22%),
                radial-gradient(circle at 85% 15%, rgba(255,120,120,.24), transparent 20%),
                radial-gradient(circle at bottom center, rgba(255,255,255,.08), transparent 18%),
                linear-gradient(180deg, var(--sr-side-top) 0%, var(--sr-side-mid) 48%, var(--sr-side-bottom) 100%);
            border-right: 1px solid var(--sr-side-border);
            color:#FFF7F7;
            box-shadow:
                inset -1px 0 0 rgba(255,255,255,.04),
                18px 0 40px rgba(127,16,27,.10);
        }

        .sr-side::before{
            content:'';
            position:absolute;
            inset:14px 12px 14px 12px;
            border-radius:28px;
            border:1px solid rgba(255,255,255,.06);
            pointer-events:none;
        }

        .sr-side::after{
            content:'';
            position:absolute;
            top:-80px;
            right:-60px;
            width:180px;
            height:180px;
            border-radius:999px;
            background:radial-gradient(circle, rgba(255,255,255,.18) 0%, rgba(255,255,255,0) 70%);
            pointer-events:none;
            filter:blur(4px);
        }

        .sr-nav{
            position: relative;
            background: var(--sr-nav-bg);
            border:1px solid var(--sr-nav-border);
            color:#FFF7F7;
            transition:.22s ease;
            backdrop-filter: blur(10px);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
        }

        .sr-nav:hover{
            background: var(--sr-nav-hover);
            border-color: rgba(255,255,255,.16);
            transform: translateY(-1px);
            box-shadow:
                0 12px 24px rgba(20,5,7,.20),
                inset 0 1px 0 rgba(255,255,255,.05);
        }

        .sr-nav-active{
            background: linear-gradient(135deg, var(--sr-active-1), var(--sr-active-2));
            border-color: var(--sr-active-border);
            box-shadow:
                0 16px 30px var(--sr-active-shadow),
                inset 0 1px 0 rgba(255,255,255,.10);
        }

        .sr-nav-active::before{
            content:'';
            position:absolute;
            left:0;
            top:12%;
            bottom:12%;
            width:4px;
            border-radius:999px;
            background:linear-gradient(180deg, #FFD5D8 0%, #FFFFFF 100%);
            box-shadow:0 0 16px rgba(255,255,255,.30);
        }

        .sr-nav-active .sr-ico{
            color:#FFFFFF;
            transform: scale(1.05);
        }

        .sr-nav .sr-ico{
            width:2.15rem;
            height:2.15rem;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:.9rem;
            background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.08);
            transition:.22s ease;
            flex-shrink:0;
        }

        .sr-nav:hover .sr-ico{
            background:rgba(255,255,255,.12);
            border-color:rgba(255,255,255,.12);
        }

        .sr-verify{
            background: linear-gradient(135deg, rgba(255,255,255,.12), rgba(255,255,255,.06));
            border:1px solid rgba(255,255,255,.14);
            color:#FFF7F7;
            box-shadow: 0 10px 24px rgba(20,5,7,.16);
            backdrop-filter: blur(8px);
        }

        .sr-verify .tick{
            color:#FFD166;
        }

        .sr-btn{
            background: linear-gradient(135deg, rgba(214,179,107,.95), rgba(184,138,44,.92));
            color:#1C120C;
            box-shadow: 0 10px 24px rgba(214,179,107,.18);
        }

        .sr-btn:hover{
            filter:brightness(1.05);
        }

        .sr-outline{
            background: linear-gradient(135deg, rgba(255,255,255,.96), rgba(255,245,245,.92));
            border:1px solid rgba(255,255,255,.16);
            color:#7F101B;
            transition:.18s ease;
            box-shadow: 0 10px 24px rgba(20,5,7,.12);
        }

        .sr-outline:hover{
            background:#FFFFFF;
            border-color:rgba(255,255,255,.25);
            transform:translateY(-1px);
        }

        .sr-input, .sr-select, .sr-textarea{
            width:100%;
            border-radius:1rem;
            padding:.85rem 1rem;
            background:#FFFFFF;
            border:1px solid #D1D5DB;
            color:#111827;
            outline:none;
        }

        .sr-textarea{
            padding:.9rem 1rem;
        }

        .sr-input::placeholder, .sr-textarea::placeholder{
            color:#9CA3AF;
        }

        .sr-input:focus, .sr-select:focus, .sr-textarea:focus{
            box-shadow: 0 0 0 4px rgba(214,179,107,.18);
            border-color: rgba(214,179,107,.55);
        }

        .sr-check{
            accent-color: var(--sr-gold-2);
        }

        .sr-pill{
            background:#FFFFFF;
            border:1px solid #E5E7EB;
            color:#374151;
        }

        .sr-pill:hover{
            border-color:#D6B36B;
        }

        .sr-chip{
            border:1px solid #E5E7EB;
        }

        .sr-chip-active{
            background:#ECFDF5;
            border-color:#A7F3D0;
        }

        .sr-chip-draft{
            background:#FFFBEB;
            border-color:#FDE68A;
        }

        .sr-chip-inactive{
            background:#F3F4F6;
            border-color:#D1D5DB;
        }

        .sr-divider-soft{
            border-color:#E5E7EB;
        }
    </style>
</head>

<body>
    @php
        $landlordAnnouncementPopup = null;

        try {
            if (auth()->check()) {
                $landlordAnnouncementPopup = auth()->user()
                    ->unreadNotifications()
                    ->where('type', \App\Notifications\AnnouncementPublished::class)
                    ->latest()
                    ->first();
            }
        } catch (\Throwable $e) {
            $landlordAnnouncementPopup = null;
        }
    @endphp

    <div class="min-h-screen flex overflow-x-hidden">

        <!-- SIDEBAR -->
        <aside class="sr-side hidden md:flex md:w-[305px] md:min-w-[305px] md:max-w-[305px] shrink-0 flex-col px-6 py-6">
            <div class="relative z-[1] flex items-center gap-3">
                <div class="h-12 w-12 rounded-2xl flex items-center justify-center overflow-hidden"
                     style="background:linear-gradient(135deg,#FFF7F7,#FDEBEC); border:1px solid rgba(255,255,255,.14); box-shadow:0 10px 24px rgba(20,5,7,.18), inset 0 1px 0 rgba(255,255,255,.65);">
                    <img src="{{ asset('images/logo.png') }}"
                         alt="Smart Rental Logo"
                         class="h-[60%] w-[60%] object-contain">
                </div>
                <div class="leading-tight">
                    <div class="text-lg font-extrabold tracking-tight text-white">Smart Rental</div>
                    <div class="text-xs text-white/70 -mt-0.5 font-semibold">Landlord Panel</div>
                </div>
            </div>

            <div class="relative z-[1] mt-5 inline-flex items-center gap-2 sr-verify rounded-2xl px-3 py-2 w-fit">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-white/10 border border-white/10">
                    <svg viewBox="0 0 24 24" class="tick h-4 w-4" fill="none" stroke="currentColor" stroke-width="3">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </span>
                <div class="text-sm font-extrabold">Verified Landlord</div>
            </div>

            <nav class="relative z-[1] mt-8 space-y-3">
                <a href="{{ route('landlord.dashboard') }}"
                   class="sr-nav flex items-center gap-3 rounded-2xl px-4 py-3 font-extrabold
                   {{ request()->routeIs('landlord.dashboard') ? 'sr-nav-active' : '' }}">
                    <span class="sr-ico">📊</span> Dashboard
                </a>

                <a href="{{ route('landlord.rooms.index') }}"
                   class="sr-nav flex items-center gap-3 rounded-2xl px-4 py-3 font-extrabold
                   {{ request()->routeIs('landlord.rooms.*') && !request()->routeIs('landlord.rooms.create') ? 'sr-nav-active' : '' }}">
                    <span class="sr-ico">🏠</span> My Rooms
                </a>

                <a href="{{ route('landlord.rooms.create') }}"
                   class="sr-nav flex items-center gap-3 rounded-2xl px-4 py-3 font-extrabold
                   {{ request()->routeIs('landlord.rooms.create') ? 'sr-nav-active' : '' }}">
                    <span class="sr-ico">➕</span> Add Room
                </a>

                <a href="{{ route('landlord.bookings.index') }}"
                   class="sr-nav flex items-center gap-3 rounded-2xl px-4 py-3 font-extrabold
                   {{ request()->routeIs('landlord.bookings.*') ? 'sr-nav-active' : '' }}">
                    <span class="sr-ico">📅</span> Bookings
                </a>

                <a href="{{ route('landlord.payments.index') }}"
                   class="sr-nav flex items-center gap-3 rounded-2xl px-4 py-3 font-extrabold
                   {{ request()->routeIs('landlord.payments.*') ? 'sr-nav-active' : '' }}">
                    <span class="sr-ico">💳</span> Payments
                </a>

                <a href="{{ route('landlord.messages.index') }}"
                   class="sr-nav flex items-center gap-3 rounded-2xl px-4 py-3 font-extrabold
                   {{ request()->routeIs('landlord.messages.*') ? 'sr-nav-active' : '' }}">
                    <span class="sr-ico">💬</span> Messages
                </a>

                <a href="{{ route('landlord.profile.edit') }}"
                   class="sr-nav flex items-center gap-3 rounded-2xl px-4 py-3 font-extrabold
                   {{ request()->routeIs('profile.*') ? 'sr-nav-active' : '' }}">
                    <span class="sr-ico">👤</span> Profile
                </a>
            </nav>

            <div class="relative z-[1] mt-auto pt-8">
                <div class="rounded-3xl p-5"
                     style="background:linear-gradient(135deg, rgba(255,255,255,.12), rgba(255,255,255,.05)); border:1px solid rgba(255,255,255,.10); box-shadow:0 14px 28px rgba(20,5,7,.18); backdrop-filter:blur(12px);">
                    <div class="text-sm text-white/70">Signed in as</div>
                    <div class="font-extrabold text-lg text-white">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-white/65">Landlord</div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button class="w-full rounded-2xl px-4 py-3 font-extrabold sr-outline">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN -->
        <main class="flex-1 min-w-0 overflow-x-hidden">
            <header class="px-6 md:px-10 py-6">
                <div class="sr-glass rounded-3xl px-6 py-5 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="text-xl md:text-3xl font-extrabold tracking-tight" style="color:#111827;">
                            @yield('page_title', 'Landlord')
                        </div>
                        <div class="text-sm sr-muted mt-1">
                            @yield('page_subtitle', 'Manage listings & availability')
                        </div>
                    </div>

                    <div class="flex items-center gap-4 shrink-0">
                        @yield('top_actions')

                        <a href="{{ route('landlord.notifications.index') }}"
                           class="relative inline-flex items-center justify-center h-12 w-12 rounded-2xl transition"
                           style="background:#FFFFFF; border:1px solid #D1D5DB; color:#111827;">
                            <span class="text-xl">🔔</span>

                            @if(auth()->user()->unreadNotifications()->count() > 0)
                                <span class="absolute -top-1 -right-1 min-w-[22px] h-[22px] px-1 rounded-full text-[11px] font-extrabold text-white flex items-center justify-center"
                                      style="background:#1D4ED8;">
                                    {{ auth()->user()->unreadNotifications()->count() > 99 ? '99+' : auth()->user()->unreadNotifications()->count() }}
                                </span>
                            @endif
                        </a>

                        <div class="text-right">
                            <div class="font-extrabold" style="color:#111827;">Hi, {{ auth()->user()->name }} ✅</div>
                            <div class="text-xs sr-muted">Landlord</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    @if(session('success'))
                        <div class="rounded-2xl px-4 py-3 text-sm"
                             style="background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46;">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-3 rounded-2xl px-4 py-3 text-sm"
                             style="background:#FEF2F2; border:1px solid #FECACA; color:#991B1B;">
                            <b>Fix these:</b>
                            <ul class="list-disc ml-5 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </header>

            <section class="px-6 md:px-10 pb-10 overflow-x-hidden">
                @yield('content')
            </section>
        </main>
    </div>

    @if($landlordAnnouncementPopup)
        @php
            $popupData = (array) ($landlordAnnouncementPopup->data ?? []);
            $popupTitle = $popupData['title'] ?? 'Announcement';
            $popupMessage = $popupData['message'] ?? '';
            $popupDate = $landlordAnnouncementPopup->created_at
                ? \Carbon\Carbon::parse($landlordAnnouncementPopup->created_at)->format('d M Y, h:i A')
                : '';
        @endphp

        <div id="landlordAnnouncementPopup"
             class="fixed inset-0 z-[9999] flex items-center justify-center px-4"
             style="background:rgba(17,24,39,.45);">

            <div class="w-full max-w-sm rounded-3xl overflow-hidden shadow-xl"
                 style="background:#FFFFFF; border:1px solid #E5E7EB;">

                <div class="px-5 py-4"
                     style="background:linear-gradient(90deg, #FAF6F2 0%, #F9FAFB 100%); border-bottom:1px solid #E5E7EB;">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-3">
                                <div class="h-11 w-11 rounded-full flex items-center justify-center text-lg"
                                     style="background:#F3E7BE; color:#B08401;">
                                    📣
                                </div>
                                <div class="text-sm font-black tracking-wide uppercase" style="color:#6B7280;">
                                    Announcement
                                </div>
                            </div>

                            <div class="mt-3 text-xl font-extrabold" style="color:#683B2B;">
                                {{ $popupTitle }}
                            </div>

                            <div class="mt-2 text-sm" style="color:#6B7280;">
                                {{ $popupDate }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('landlord.notifications.read') }}">
                            @csrf
                            <input type="hidden" name="ids[]" value="{{ $landlordAnnouncementPopup->id }}">
                            <button type="submit"
                                    class="h-10 w-10 rounded-xl text-xl leading-none transition"
                                    style="background:#F9FAFB; border:1px solid #D1D5DB; color:#4B5563;">
                                ×
                            </button>
                        </form>
                    </div>
                </div>

                <div class="px-5 py-6">
                    <div class="text-sm leading-relaxed" style="color:#4B5563;">
                        {{ $popupMessage }}
                    </div>

                    <div class="mt-6 flex justify-end">
                        <form method="POST" action="{{ route('landlord.notifications.read') }}">
                            @csrf
                            <input type="hidden" name="ids[]" value="{{ $landlordAnnouncementPopup->id }}">
                            <button type="submit"
                                    class="rounded-full px-6 py-2 text-sm font-extrabold text-white transition hover:brightness-95"
                                    style="background:#B08401;">
                                Okay
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</body>
</html>