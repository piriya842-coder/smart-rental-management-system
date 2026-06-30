{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin • Smart Rental')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $cream = '#FFF8FA';
    $brandText = '#451A1A';
    $brandSub  = '#A35F5F';

    $sidebarTop = '#160707';
    $sidebarMid = '#2A0D0D';
    $sidebarBot = '#4B1212';

    $sidebarInner = 'rgba(255,255,255,.035)';
    $cardDefault  = 'rgba(255,255,255,.045)';
    $cardBorder   = 'rgba(255,255,255,.08)';
    $cardHint     = 'rgba(255,238,238,.68)';

    $activeBorder = 'rgba(239,68,68,.95)';
    $activeBg     = 'linear-gradient(135deg, rgba(220,38,38,.26), rgba(127,29,29,.34), rgba(255,255,255,.05))';

    $tipBg        = 'rgba(255,255,255,.06)';
    $tipBorder    = 'rgba(255,255,255,.10)';
    $tipTitle     = '#FFE0E0';

    $is = fn($name) => request()->routeIs($name);

    $href = function($routeName) {
        return \Illuminate\Support\Facades\Route::has($routeName) ? route($routeName) : '#';
    };

    $menu = [
        [
            'title' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'hint' => 'KPIs & recent activity', 'emoji' => '📊'],
            ],
        ],
        [
            'title' => 'Landlords',
            'items' => [
                ['label' => 'Landlord Approvals', 'route' => 'admin.landlords.index', 'hint' => 'Pending actions', 'emoji' => '✅'],
                ['label' => 'Landlords (All Status)', 'route' => 'admin.landlords.all', 'hint' => 'Pending / Approved / Rejected', 'emoji' => '👥'],
            ],
        ],
        [
            'title' => 'Listings',
            'items' => [
                ['label' => 'Verify Listings', 'route' => 'admin.listings.verify', 'hint' => 'Approve / reject rooms', 'emoji' => '🏷️'],
            ],
        ],
        [
            'title' => 'Users & Support',
            'items' => [
                ['label' => 'Manage Users', 'route' => 'admin.users.index', 'hint' => 'Students & landlords', 'emoji' => '🧑‍💼'],
                ['label' => 'Resolve Disputes', 'route' => 'admin.disputes.index', 'hint' => 'Tickets & outcomes', 'emoji' => '🛟'],
            ],
        ],
        [
            'title' => 'Analytics & System',
            'items' => [
                ['label' => 'Reports', 'route' => 'admin.reports.index', 'hint' => 'Bookings, revenue, occupancy', 'emoji' => '📈'],
                ['label' => 'Announcements', 'route' => 'admin.announcements.index', 'hint' => 'Send to all users', 'emoji' => '📣'],
            ],
        ],
    ];
@endphp

<body class="h-screen overflow-hidden" style="background: {{ $cream }}; color: #1C120C;">

<style>
    .sr-topbar{
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,.85), transparent 18%),
            radial-gradient(circle at 85% 25%, rgba(255,220,220,.55), transparent 22%),
            linear-gradient(135deg, #fffefe 0%, #fff5f5 42%, #fff9f9 100%);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(220,38,38,.10);
        box-shadow:
            0 14px 34px rgba(120,20,20,.06),
            inset 0 -1px 0 rgba(255,255,255,.70);
    }

    .sr-topbar::after{
        content:"";
        position:absolute;
        inset:0;
        pointer-events:none;
        background:
            linear-gradient(115deg,
                transparent 0%,
                transparent 45%,
                rgba(255,255,255,.35) 50%,
                transparent 55%,
                transparent 100%);
        opacity:.35;
    }

    .sr-logo-wrap{
        width: 62px;
        height: 62px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,243,243,.94));
        border: 1px solid rgba(220,38,38,.12);
        box-shadow:
            0 12px 26px rgba(160,24,24,.10),
            inset 0 1px 0 rgba(255,255,255,.95);
        overflow: hidden;
        flex-shrink: 0;
    }

    .sr-logo-wrap img{
        height: 38px;
        width: auto;
        object-fit: contain;
        display: block;
    }

    .sr-brand-block{
        display:flex;
        flex-direction:column;
        justify-content:center;
    }

    .sr-brand-title{
        font-size: 20px;
        font-weight: 900;
        line-height: 1.03;
        letter-spacing: -.02em;
        color: {{ $brandText }};
    }

    .sr-brand-sub{
        margin-top: 4px;
        font-size: 12px;
        font-weight: 800;
        color: {{ $brandSub }};
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .sr-user-chip{
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 18px;
        background:
            linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,244,244,.82));
        border: 1px solid rgba(220,38,38,.10);
        box-shadow:
            0 10px 20px rgba(170,24,24,.05),
            inset 0 1px 0 rgba(255,255,255,.75);
    }

    .sr-user-icon{
        width: 36px;
        height: 36px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(220,38,38,.12);
        color: {{ $brandText }};
        flex-shrink: 0;
    }

    .sr-user-icon svg{
        width: 16px;
        height: 16px;
    }

    .sr-user-label{
        font-size: 11px;
        font-weight: 800;
        color: rgba(69,26,26,.62);
        line-height: 1;
    }

    .sr-user-name{
        margin-top: 3px;
        font-size: 14px;
        font-weight: 900;
        color: #451A1A;
        line-height: 1;
    }

    .sr-logout-btn{
        border-radius: 18px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 900;
        border: 1px solid rgba(220,38,38,.12);
        background:
            linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,244,244,.84));
        color: #4A1E1E;
        box-shadow:
            0 10px 20px rgba(170,24,24,.05),
            inset 0 1px 0 rgba(255,255,255,.70);
        transition: all .22s ease;
    }

    .sr-logout-btn:hover{
        background: rgba(255,255,255,.98);
        transform: translateY(-1px);
        box-shadow:
            0 14px 24px rgba(170,24,24,.08),
            inset 0 1px 0 rgba(255,255,255,.84);
    }

    .sr-sidebar-scroll,
    .sr-main-scroll{
        scrollbar-width: thin;
        scrollbar-color: rgba(220,38,38,.38) transparent;
    }

    .sr-sidebar-scroll::-webkit-scrollbar,
    .sr-main-scroll::-webkit-scrollbar{
        width: 8px;
    }

    .sr-sidebar-scroll::-webkit-scrollbar-thumb,
    .sr-main-scroll::-webkit-scrollbar-thumb{
        background: rgba(220,38,38,.38);
        border-radius: 999px;
    }

    .sr-side-shell{
        position: relative;
        border-radius: 34px;
        padding: 1px;
        background:
            linear-gradient(180deg, {{ $sidebarTop }}, {{ $sidebarMid }}, {{ $sidebarBot }});
        box-shadow:
            0 30px 65px rgba(28,8,8,.38),
            0 14px 28px rgba(32,10,10,.24);
        overflow: hidden;
    }

    .sr-side-shell::before{
        content:"";
        position:absolute;
        inset:0;
        pointer-events:none;
        background:
            radial-gradient(circle at 85% 12%, rgba(255,120,120,.18), transparent 18%),
            radial-gradient(circle at 20% 90%, rgba(220,38,38,.18), transparent 22%),
            linear-gradient(160deg, transparent 0%, rgba(255,255,255,.03) 48%, transparent 60%);
        opacity:1;
    }

    .sr-side-inner{
        position: relative;
        z-index: 1;
        border-radius: 34px;
        padding: 16px;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.05), transparent 20%),
            radial-gradient(circle at bottom left, rgba(220,38,38,.12), transparent 24%),
            {{ $sidebarInner }};
        backdrop-filter: blur(12px);
    }

    .sr-admin-badge{
        width: 42px;
        height: 42px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 900;
        background:
            linear-gradient(135deg, rgba(239,68,68,.34), rgba(153,27,27,.28));
        border: 1px solid rgba(255,255,255,.12);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.08),
            0 12px 22px rgba(80,20,20,.24);
    }

    .sr-menu-card{
        display: block;
        border-radius: 22px;
        padding: 12px;
        border: 1px solid {{ $cardBorder }};
        background: {{ $cardDefault }};
        transition: all .25s ease;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.02);
    }

    .sr-menu-card:hover{
        transform: translateY(-1px);
        background: rgba(255,255,255,.07);
        border-color: rgba(255,255,255,.14);
        box-shadow:
            0 12px 20px rgba(45,12,12,.20),
            0 0 0 1px rgba(255,255,255,.02);
    }

    .sr-menu-card.active{
        border-color: {{ $activeBorder }};
        background: {!! json_encode($activeBg) !!};
        box-shadow:
            0 0 0 1px rgba(239,68,68,.18),
            0 16px 28px rgba(70,16,16,.28);
    }

    .sr-menu-icon{
        width: 40px;
        height: 40px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        background: rgba(255,255,255,.07);
        border: 1px solid rgba(255,255,255,.08);
        flex-shrink: 0;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
    }

    .sr-menu-card.active .sr-menu-icon{
        background: rgba(255,255,255,.10);
        border-color: rgba(255,255,255,.14);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.06),
            0 8px 16px rgba(90,14,14,.18);
    }

    .sr-tip-box{
        margin-top: 16px;
        border-radius: 22px;
        padding: 14px;
        border: 1px solid {{ $tipBorder }};
        background: {{ $tipBg }};
        box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
    }
</style>

<header class="sticky top-0 z-50 sr-topbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">

        <div class="flex items-center gap-4">
            <div class="sr-logo-wrap">
                <img src="{{ asset('images/logo.png') }}" alt="Smart Rental Logo">
            </div>

            <div class="sr-brand-block">
                <div class="sr-brand-title">Smart Rental</div>
                <div class="sr-brand-sub">Admin Portal</div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="sr-user-chip">
                <div class="sr-user-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 12a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"
                              stroke="currentColor"
                              stroke-width="1.9"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                        <path d="M5.5 19a6.5 6.5 0 0 1 13 0"
                              stroke="currentColor"
                              stroke-width="1.9"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </div>

                <div>
                    <div class="sr-user-label">Signed in</div>
                    <div class="sr-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="sr-logout-btn">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

<div class="h-[calc(100vh-88px)] max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6">

    <aside class="lg:col-span-3 h-full overflow-y-auto pr-2 sr-sidebar-scroll">
        <div class="sr-side-shell">
            <div class="sr-side-inner">

                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-xs font-black tracking-wider text-white/60">ADMIN MENU</div>
                        <div class="text-sm font-extrabold text-white mt-0.5">Control Panel</div>
                    </div>

                    <div class="sr-admin-badge">⚡</div>
                </div>

                <div class="space-y-4">
                    @foreach($menu as $group)
                        <div>
                            <div class="px-2 pb-2 text-[11px] font-black tracking-wider uppercase text-white/45">
                                {{ $group['title'] }}
                            </div>

                            <div class="space-y-2">
                                @foreach($group['items'] as $item)
                                    @php $active = $is($item['route']); @endphp
                                    <a href="{{ $href($item['route']) }}"
                                       class="sr-menu-card {{ $active ? 'active' : '' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="sr-menu-icon">
                                                {{ $item['emoji'] ?? '•' }}
                                            </div>

                                            <div class="min-w-0">
                                                <div class="font-extrabold text-sm text-white truncate">
                                                    {{ $item['label'] }}
                                                </div>
                                                <div class="text-xs font-semibold truncate" style="color: {{ $cardHint }};">
                                                    {{ $item['hint'] ?? '' }}
                                                </div>
                                            </div>

                                            <div class="ml-auto text-white/30 font-black">›</div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="sr-tip-box">
                    <div class="font-extrabold" style="color: {{ $tipTitle }};">Tip</div>
                    <div class="text-white/70 mt-1 text-sm">
                        Verify rooms so only trusted listings appear to students.
                    </div>
                </div>

                <div class="mt-4 text-xs text-white/35 px-1">
                    © {{ date('Y') }} Smart Rental Admin
                </div>
            </div>
        </div>
    </aside>

    <main class="lg:col-span-9 h-full overflow-y-auto pr-2 sr-main-scroll">
        @if(session('success'))
            <div class="mb-4 rounded-2xl px-4 py-3 bg-green-50 border border-green-200 text-green-800 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-2xl px-4 py-3 bg-red-50 border border-red-200 text-red-800 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>