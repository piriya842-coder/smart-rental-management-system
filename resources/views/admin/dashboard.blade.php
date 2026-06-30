@extends('layouts.admin')

@section('title', 'Admin Dashboard • Smart Rental')

@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\Route;

    // Premium red admin palette
    $dark      = '#2B0A0A';
    $dark2     = '#4A1212';
    $panel     = '#FFFFFF';
    $soft      = '#FFF8F8';
    $text      = '#2D1414';
    $muted     = '#7B5B5B';

    $primary   = '#A61E1E';
    $primary2  = '#C92A2A';
    $accent    = '#E45B5B';
    $green     = '#10B981';
    $amber     = '#F59E0B';
    $red       = '#EF4444';
    $pink      = '#EC4899';

    $href = function(string $routeName, array $params = []) {
        return Route::has($routeName) ? route($routeName, $params) : '#';
    };

    $hasUsers       = Schema::hasTable('users');
    $hasRooms       = Schema::hasTable('rooms');
    $hasBookings    = Schema::hasTable('bookings');
    $hasPayments    = Schema::hasTable('payments');
    $hasSavedRooms  = Schema::hasTable('saved_rooms');

    $totalStudents  = $hasUsers ? DB::table('users')->where('role', 'student')->count() : 0;
    $totalLandlords = $hasUsers ? DB::table('users')->where('role', 'landlord')->count() : 0;

    $pendingLandlords  = $hasUsers ? DB::table('users')->where('role', 'landlord')->where('landlord_status', 'pending')->count() : 0;
    $approvedLandlords = $hasUsers ? DB::table('users')->where('role', 'landlord')->where('landlord_status', 'approved')->count() : 0;
    $rejectedLandlords = $hasUsers ? DB::table('users')->where('role', 'landlord')->where('landlord_status', 'rejected')->count() : 0;

    $totalRooms    = $hasRooms ? DB::table('rooms')->count() : 0;
    $totalBookings = $hasBookings ? DB::table('bookings')->count() : 0;

    $pendingPayments = $hasPayments
        ? DB::table('payments')->whereIn('status', ['pending', 'submitted'])->count()
        : 0;

    $approvedPayments = $hasPayments
        ? DB::table('payments')->where('status', 'paid')->count()
        : 0;

    $totalRevenue = $hasPayments
        ? (float) DB::table('payments')->where('status', 'paid')->sum('amount')
        : 0;

    $latestPendingLandlords = $hasUsers
        ? DB::table('users')
            ->select('id', 'name', 'email', 'created_at')
            ->where('role', 'landlord')
            ->where('landlord_status', 'pending')
            ->latest()
            ->limit(5)
            ->get()
        : collect();

    $latestBookings = $hasBookings
        ? DB::table('bookings')->select('id', 'created_at')->latest()->limit(5)->get()
        : collect();

    $latestPayments = $hasPayments
        ? DB::table('payments')->select('id', 'status', 'created_at')->latest()->limit(5)->get()
        : collect();

    $den = max($totalLandlords, 1);
    $pctPending  = min(100, round(($pendingLandlords / $den) * 100));
    $pctApproved = min(100, round(($approvedLandlords / $den) * 100));
    $pctRejected = min(100, round(($rejectedLandlords / $den) * 100));

    // Booking trend (last 6 months)
    $bookingTrend = collect();
    if ($hasBookings) {
        $bookingTrend = DB::table('bookings')
            ->selectRaw("DATE_FORMAT(created_at, '%b') as mon, COUNT(*) as total, MIN(created_at) as first_date")
            ->where('created_at', '>=', now()->copy()->subMonths(5)->startOfMonth())
            ->groupByRaw("YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, '%b')")
            ->orderByRaw("MIN(created_at)")
            ->get();
    }
    $maxBookingTrend = max((int) ($bookingTrend->max('total') ?? 0), 1);

    // Room types
    $roomTypeStats = collect();
    if ($hasRooms && Schema::hasColumn('rooms', 'room_type')) {
        $roomTypeStats = DB::table('rooms')
            ->select('room_type', DB::raw('COUNT(*) as total'))
            ->groupBy('room_type')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }
    $maxRoomType = max((int) ($roomTypeStats->max('total') ?? 0), 1);

    // Locations
    $locationStats = collect();
    if ($hasRooms) {
        if (Schema::hasColumn('rooms', 'city')) {
            $locationStats = DB::table('rooms')
                ->select('city', DB::raw('COUNT(*) as total'))
                ->whereNotNull('city')
                ->where('city', '<>', '')
                ->groupBy('city')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        } elseif (Schema::hasColumn('rooms', 'address')) {
            $locationStats = DB::table('rooms')
                ->select('address as city', DB::raw('COUNT(*) as total'))
                ->whereNotNull('address')
                ->where('address', '<>', '')
                ->groupBy('address')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }
    }
    $maxLocation = max((int) ($locationStats->max('total') ?? 0), 1);

    // Budget ranges
    $budgetRanges = collect([
        (object)['label' => 'Under RM300', 'total' => 0],
        (object)['label' => 'RM301–500', 'total' => 0],
        (object)['label' => 'RM501–800', 'total' => 0],
        (object)['label' => 'Above RM800', 'total' => 0],
    ]);

    if ($hasRooms && Schema::hasColumn('rooms', 'price_monthly')) {
        $prices = DB::table('rooms')->pluck('price_monthly');

        $budgetRanges = collect([
            (object)['label' => 'Under RM300', 'total' => $prices->filter(fn($p) => $p < 300)->count()],
            (object)['label' => 'RM301–500', 'total' => $prices->filter(fn($p) => $p >= 300 && $p <= 500)->count()],
            (object)['label' => 'RM501–800', 'total' => $prices->filter(fn($p) => $p > 500 && $p <= 800)->count()],
            (object)['label' => 'Above RM800', 'total' => $prices->filter(fn($p) => $p > 800)->count()],
        ]);
    }
    $maxBudget = max((int) ($budgetRanges->max('total') ?? 0), 1);

    $mostSavedType = '-';
    $mostSavedArea = '-';

    if ($hasSavedRooms && $hasRooms) {
        if (Schema::hasColumn('rooms', 'room_type')) {
            $savedTypeRow = DB::table('saved_rooms')
                ->join('rooms', 'saved_rooms.room_id', '=', 'rooms.id')
                ->select('rooms.room_type', DB::raw('COUNT(*) as total'))
                ->groupBy('rooms.room_type')
                ->orderByDesc('total')
                ->first();

            if ($savedTypeRow && !empty($savedTypeRow->room_type)) {
                $mostSavedType = ucfirst((string) $savedTypeRow->room_type);
            }
        }

        if (Schema::hasColumn('rooms', 'city')) {
            $savedAreaRow = DB::table('saved_rooms')
                ->join('rooms', 'saved_rooms.room_id', '=', 'rooms.id')
                ->select('rooms.city', DB::raw('COUNT(*) as total'))
                ->whereNotNull('rooms.city')
                ->where('rooms.city', '<>', '')
                ->groupBy('rooms.city')
                ->orderByDesc('total')
                ->first();

            if ($savedAreaRow && !empty($savedAreaRow->city)) {
                $mostSavedArea = (string) $savedAreaRow->city;
            }
        }
    }

    $topRoomType = $roomTypeStats->first();
    $topLocation = $locationStats->first();
    $topBudget   = $budgetRanges->sortByDesc('total')->first();
    $bookingPerRoom = $totalRooms > 0 ? round($totalBookings / max($totalRooms, 1), 1) : 0;
@endphp

@section('content')

<style>
    .sr-admin-wrap{
        display:flex;
        flex-direction:column;
        gap:24px;
    }

    .sr-card{
        background:
            linear-gradient(145deg, rgba(255,248,248,.96) 0%, rgba(255,239,239,.94) 100%);
        border:1px solid rgba(225,164,164,.34);
        border-radius:28px;
        box-shadow:
            0 20px 45px rgba(82,22,22,.08),
            inset 0 1px 0 rgba(255,255,255,.72);
        backdrop-filter: blur(10px);
    }

    .sr-card-soft{
        background:
            linear-gradient(145deg, rgba(255,252,252,.94) 0%, rgba(255,241,241,.92) 50%, rgba(255,233,233,.90) 100%);
        border:1px solid rgba(228,170,170,.30);
        border-radius:26px;
        box-shadow:
            0 14px 32px rgba(82,22,22,.06),
            inset 0 1px 0 rgba(255,255,255,.78);
        backdrop-filter: blur(10px);
    }

    .sr-hover{
        transition:all .28s ease;
    }

    .sr-hover:hover{
        transform:translateY(-4px);
        box-shadow:
            0 26px 48px rgba(82,22,22,.12),
            inset 0 1px 0 rgba(255,255,255,.82);
    }

    .sr-hero{
        position:relative;
        overflow:hidden;
        border-radius:34px;
        border:1px solid rgba(255,255,255,.10);
        box-shadow:0 22px 60px rgba(40,12,12,.24);
        background:
            radial-gradient(circle at 12% 18%, rgba(255,255,255,.08), transparent 20%),
            radial-gradient(circle at 88% 12%, rgba(255,157,157,.28), transparent 18%),
            radial-gradient(circle at 82% 80%, rgba(255,94,94,.22), transparent 22%),
            radial-gradient(circle at 24% 82%, rgba(255,121,121,.14), transparent 18%),
            radial-gradient(circle at 55% 45%, rgba(255,255,255,.05), transparent 28%),
            linear-gradient(135deg, #160707 0%, #230909 18%, #3B0E12 38%, #611217 60%, #8E1C24 82%, #A61E1E 100%);
    }

    .sr-hero::after{
        content:"";
        position:absolute;
        inset:-20% -10%;
        background:
            radial-gradient(circle at 30% 30%, rgba(255,255,255,.10), transparent 14%),
            radial-gradient(circle at 70% 60%, rgba(255,170,170,.10), transparent 18%),
            radial-gradient(circle at 60% 20%, rgba(255,120,120,.08), transparent 16%);
        mix-blend-mode: screen;
        pointer-events:none;
        animation: srFloatGalaxy 14s linear infinite;
        opacity:.9;
    }

    @keyframes srFloatGalaxy{
        0%{ transform: translate3d(0,0,0) scale(1); }
        50%{ transform: translate3d(-10px,8px,0) scale(1.03); }
        100%{ transform: translate3d(0,0,0) scale(1); }
    }

    .sr-hero-galaxy{
    position:absolute;
    inset:0;
    pointer-events:none;
    z-index:0;
    overflow:hidden;
    background:
        radial-gradient(circle at 8% 22%, rgba(255,255,255,.70) 0 1px, transparent 2px),
        radial-gradient(circle at 14% 68%, rgba(255,255,255,.46) 0 1px, transparent 2px),
        radial-gradient(circle at 20% 48%, rgba(255,255,255,.55) 0 1.1px, transparent 2px),
        radial-gradient(circle at 26% 84%, rgba(255,255,255,.38) 0 1px, transparent 2px),
        radial-gradient(circle at 34% 20%, rgba(255,255,255,.48) 0 1px, transparent 2px),
        radial-gradient(circle at 40% 62%, rgba(255,255,255,.40) 0 1.2px, transparent 2px),
        radial-gradient(circle at 48% 28%, rgba(255,255,255,.58) 0 1px, transparent 2px),
        radial-gradient(circle at 56% 74%, rgba(255,255,255,.42) 0 1px, transparent 2px),
        radial-gradient(circle at 63% 18%, rgba(255,255,255,.45) 0 1px, transparent 2px),
        radial-gradient(circle at 71% 43%, rgba(255,255,255,.50) 0 1.1px, transparent 2px),
        radial-gradient(circle at 79% 30%, rgba(255,255,255,.42) 0 1px, transparent 2px),
        radial-gradient(circle at 85% 72%, rgba(255,255,255,.44) 0 1px, transparent 2px),
        radial-gradient(circle at 92% 48%, rgba(255,255,255,.32) 0 1px, transparent 2px),

        radial-gradient(ellipse at 58% 38%, rgba(255,120,120,.18), transparent 24%),
        radial-gradient(ellipse at 72% 58%, rgba(255,80,80,.14), transparent 18%),
        radial-gradient(ellipse at 38% 72%, rgba(255,170,170,.10), transparent 22%),
        radial-gradient(ellipse at 18% 38%, rgba(255,110,110,.07), transparent 18%),

        linear-gradient(112deg,
            transparent 0%,
            transparent 24%,
            rgba(120,10,10,0) 30%,
            rgba(140,18,18,.06) 36%,
            rgba(170,35,35,.12) 42%,
            rgba(210,70,70,.18) 47%,
            rgba(255,170,170,.10) 50%,
            rgba(210,70,70,.18) 53%,
            rgba(170,35,35,.12) 58%,
            rgba(140,18,18,.06) 64%,
            rgba(120,10,10,0) 70%,
            transparent 76%,
            transparent 100%
        ),

        linear-gradient(112deg,
            transparent 0%,
            transparent 28%,
            rgba(255,255,255,0) 40%,
            rgba(255,240,240,.05) 46%,
            rgba(255,220,220,.10) 50%,
            rgba(255,240,240,.05) 54%,
            rgba(255,255,255,0) 60%,
            transparent 72%,
            transparent 100%
        ),

        linear-gradient(112deg,
            transparent 0%,
            transparent 18%,
            rgba(255,120,120,0) 34%,
            rgba(255,90,90,.05) 44%,
            rgba(255,150,150,.09) 50%,
            rgba(255,90,90,.05) 56%,
            rgba(255,120,120,0) 66%,
            transparent 82%,
            transparent 100%
        );
    opacity:1;
    filter: blur(.2px);
    
}



    .sr-badge{
        display:inline-flex;
        align-items:center;
        gap:.45rem;
        border-radius:999px;
        padding:.5rem .95rem;
        font-size:12px;
        font-weight:800;
    }

    .sr-icon-box{
        width:48px;
        height:48px;
        border-radius:18px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:20px;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.28),
            0 8px 18px rgba(100,28,28,.10);
    }

    .sr-kpi-title{
        font-size:11px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.14em;
        color:#AA8A8A;
    }

    .sr-kpi-value{
        font-size:34px;
        line-height:1;
        font-weight:900;
        color:#2D1414;
    }

    .sr-title{
        font-size:24px;
        line-height:1.1;
        font-weight:900;
        color:#2D1414;
    }

    .sr-sub{
        margin-top:6px;
        font-size:14px;
        color:#7B5B5B;
        font-weight:600;
    }

    .sr-action{
        min-height:104px;
        border-radius:24px;
        padding:18px;
        display:block;
        transition:all .28s ease;
        box-shadow:
            0 10px 22px rgba(98,26,26,.08),
            inset 0 1px 0 rgba(255,255,255,.74);
    }

    .sr-action:hover{
        transform:translateY(-4px);
        box-shadow:
            0 18px 32px rgba(98,26,26,.14),
            inset 0 1px 0 rgba(255,255,255,.82);
    }

    .sr-grid-lines{
        position:absolute;
        inset:18px 18px 44px 18px;
        display:flex;
        flex-direction:column;
        justify-content:space-between;
        pointer-events:none;
    }

    .sr-grid-lines span{
        border-top:1px dashed rgba(115,64,64,.14);
    }

    .sr-chart-shell{
        position:relative;
        height:290px;
        border-radius:28px;
        overflow:hidden;
        border:1px solid rgba(228,170,170,.28);
        background:
            radial-gradient(circle at top right, rgba(201,42,42,.10), transparent 28%),
            radial-gradient(circle at bottom left, rgba(228,91,91,.09), transparent 24%),
            linear-gradient(180deg, rgba(255,251,251,.95), rgba(255,240,240,.92));
            
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.72),
            0 10px 24px rgba(82,22,22,.05);
    }

    .sr-chart-bars{
        position:relative;
        height:100%;
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:14px;
        padding:18px 18px 16px;
    }

    .sr-bar-col{
        flex:1;
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:flex-end;
        gap:8px;
    }

    .sr-bar{
        width:100%;
        max-width:56px;
        border-radius:18px 18px 8px 8px;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.18),
            0 10px 20px rgba(166,30,30,.18);
    }

    .sr-stat-list{
        display:flex;
        flex-direction:column;
        gap:16px;
    }

    .sr-stat-row{
        display:flex;
        flex-direction:column;
        gap:8px;
    }

    .sr-mini-bar{
        height:12px;
        border-radius:999px;
        overflow:hidden;
        background:linear-gradient(90deg, rgba(243,220,220,.82), rgba(248,232,232,.96));
        border:1px solid rgba(234,196,196,.42);
    }

    .sr-pill{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:999px;
        padding:.32rem .7rem;
        font-size:11px;
        font-weight:800;
        border:1px solid rgba(231,186,186,.38);
        background:linear-gradient(135deg, rgba(255,248,248,.95), rgba(255,237,237,.95));
        color:#7B5B5B;
    }

    .sr-list-card{
        border:1px solid rgba(230,184,184,.32);
        border-radius:20px;
        background:linear-gradient(145deg, rgba(255,252,252,.95), rgba(255,239,239,.92));
        padding:14px;
        transition:all .24s ease;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.72);
    }

    .sr-list-card:hover{
        transform:translateY(-2px);
        box-shadow:
            0 12px 24px rgba(45,20,20,.08),
            inset 0 1px 0 rgba(255,255,255,.82);
    }

    .sr-glass{
        background:linear-gradient(135deg, rgba(255,255,255,.11), rgba(255,255,255,.05));
        backdrop-filter:blur(12px);
        border:1px solid rgba(255,255,255,.12);
        box-shadow:0 10px 24px rgba(35,10,10,.14);
    }

    .sr-insight{
        border-radius:24px;
        padding:18px;
        border:1px solid rgba(228,176,176,.28);
        background:linear-gradient(145deg, rgba(255,252,252,.96), rgba(255,238,238,.92));
        box-shadow:
            0 8px 20px rgba(45,20,20,.05),
            inset 0 1px 0 rgba(255,255,255,.72);
    }

    .sr-rose-panel{
        background:linear-gradient(145deg, rgba(255,250,250,.97), rgba(255,238,238,.92));
        border:1px solid rgba(229,179,179,.34);
        box-shadow:
            0 12px 26px rgba(90,24,24,.06),
            inset 0 1px 0 rgba(255,255,255,.78);
    }

    .sr-rose-panel-2{
        background:linear-gradient(145deg, rgba(255,247,247,.96), rgba(255,233,233,.91));
        border:1px solid rgba(229,179,179,.30);
        box-shadow:
            0 12px 26px rgba(90,24,24,.05),
            inset 0 1px 0 rgba(255,255,255,.74);
    }
</style>

<div class="sr-admin-wrap">

    {{-- HERO --}}
   <div class="sr-hero">
    <div class="sr-hero-galaxy"></div>
    <div class="relative p-7 sm:p-9 z-10">
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

                <div class="xl:col-span-7">
                    <div class="sr-badge sr-glass text-white">
                        ✨ Smart Rental Premium Command Center
                    </div>

                    <h1 class="mt-5 text-4xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                        Admin Analytics Dashboard
                    </h1>

                    <p class="mt-4 max-w-3xl text-[15px] leading-7 font-semibold text-white/80">
                        Oversee platform growth, trust, room demand, payment flow, and landlord verification with a dashboard designed for real operational control.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="sr-badge bg-white/10 border border-white/12 text-white">
                            🕒 {{ now()->format('d M Y, h:i A') }}
                        </span>

                        <span class="sr-badge border text-white"
                              style="background:rgba(16,185,129,.14); border-color:rgba(16,185,129,.22);">
                            ✅ System Healthy
                        </span>

                        <span class="sr-badge border text-white"
                              style="background:rgba(201,42,42,.18); border-color:rgba(255,255,255,.10);">
                            ⚡ {{ $pendingLandlords }} Pending Approvals
                        </span>
                    </div>
                </div>

                <div class="xl:col-span-5">
                    <div class="sr-glass rounded-[30px] p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-black text-white">Quick Actions</div>
                                <div class="text-xs font-semibold text-white/65 mt-1">Fast admin shortcuts</div>
                            </div>
                            <div class="sr-icon-box" style="background:linear-gradient(135deg, rgba(255,255,255,.18), rgba(255,255,255,.08)); color:#fff;">
                                ⚙️
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <a href="{{ $href('admin.landlords.index') }}"
                               class="sr-action"
                               style="background:linear-gradient(145deg, rgba(255,249,249,.96) 0%, rgba(255,232,232,.92) 100%); border:1px solid rgba(255,255,255,.28);">
                                <div class="font-black text-lg" style="color:{{ $primary }};">✅ Review</div>
                                <div class="font-black text-lg -mt-1" style="color:{{ $primary }};">Approvals</div>
                                <div class="text-sm font-semibold mt-2 text-[#7B5B5B]">Approve / reject landlords</div>
                            </a>

                            <a href="{{ $href('admin.landlords.all') }}"
                               class="sr-action"
                               style="background:linear-gradient(145deg, rgba(255,246,246,.95) 0%, rgba(255,228,228,.90) 100%); border:1px solid rgba(255,255,255,.28);">
                                <div class="font-black text-lg text-[#2D1414]">👥 Landlords</div>
                                <div class="font-black text-lg -mt-1 text-[#2D1414]">(All)</div>
                                <div class="text-sm font-semibold mt-2 text-[#7B5B5B]">Pending / approved / rejected</div>
                            </a>

                            <a href="{{ $href('admin.listings.verify') }}"
                               class="sr-action"
                               style="background:linear-gradient(145deg, rgba(255,244,244,.95) 0%, rgba(255,226,226,.90) 100%); border:1px solid rgba(255,255,255,.28);">
                                <div class="font-black text-lg" style="color:{{ $accent }};">🏷️ Verify</div>
                                <div class="font-black text-lg -mt-1" style="color:{{ $accent }};">Listings</div>
                                <div class="text-sm font-semibold mt-2 text-[#7B5B5B]">Review room trust & quality</div>
                            </a>

                            <a href="{{ $href('admin.reports.index') }}"
                               class="sr-action"
                               style="background:linear-gradient(145deg, rgba(255,250,250,.96) 0%, rgba(255,234,234,.91) 100%); border:1px solid rgba(255,255,255,.28);">
                                <div class="font-black text-lg" style="color:{{ $primary2 }};">📈 Reports</div>
                                <div class="text-sm font-semibold mt-2 text-[#7B5B5B]">Operational records & summaries</div>
                            </a>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-3">
                            <div class="rounded-2xl bg-white/10 border border-white/10 p-4 text-white">
                                <div class="text-[11px] font-black uppercase tracking-wider text-white/55">Bookings</div>
                                <div class="mt-2 text-2xl font-black">{{ $totalBookings }}</div>
                            </div>

                            <div class="rounded-2xl bg-white/10 border border-white/10 p-4 text-white">
                                <div class="text-[11px] font-black uppercase tracking-wider text-white/55">Revenue</div>
                                <div class="mt-2 text-2xl font-black">RM {{ number_format($totalRevenue, 0) }}</div>
                            </div>

                            <div class="rounded-2xl bg-white/10 border border-white/10 p-4 text-white">
                                <div class="text-[11px] font-black uppercase tracking-wider text-white/55">Pending Pay</div>
                                <div class="mt-2 text-2xl font-black">{{ $pendingPayments }}</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="sr-card-soft p-5 sr-hover">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="sr-kpi-title">Total Students</div>
                    <div class="sr-kpi-value mt-3">{{ $totalStudents }}</div>
                    <div class="text-xs font-semibold text-[#7B5B5B] mt-3">Registered student accounts</div>
                </div>
                <div class="sr-icon-box" style="background:linear-gradient(135deg,#FFF0F0,#FFDCDC); color:{{ $primary2 }};">🎓</div>
            </div>
        </div>

        <div class="sr-card-soft p-5 sr-hover">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="sr-kpi-title">Total Landlords</div>
                    <div class="sr-kpi-value mt-3">{{ $totalLandlords }}</div>
                    <div class="text-xs font-semibold text-[#7B5B5B] mt-3">All application statuses</div>
                </div>
                <div class="sr-icon-box" style="background:linear-gradient(135deg,#FFE9E9,#FFD7D7); color:{{ $primary }};">👤</div>
            </div>
        </div>

        <div class="sr-card-soft p-5 sr-hover">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="sr-kpi-title">Total Rooms</div>
                    <div class="sr-kpi-value mt-3">{{ $totalRooms }}</div>
                    <div class="text-xs font-semibold text-[#7B5B5B] mt-3">Accommodation listings</div>
                </div>
                <div class="sr-icon-box" style="background:linear-gradient(135deg,#FFF0F0,#FFDCDC); color:{{ $accent }};">🏠</div>
            </div>
        </div>

        <div class="sr-card-soft p-5 sr-hover">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="sr-kpi-title">Total Bookings</div>
                    <div class="sr-kpi-value mt-3">{{ $totalBookings }}</div>
                    <div class="text-xs font-semibold text-[#7B5B5B] mt-3">Overall booking activity</div>
                </div>
                <div class="sr-icon-box" style="background:linear-gradient(135deg,#FFF0F0,#FFDCDC); color:{{ $primary2 }};">📌</div>
            </div>
        </div>
    </div>

    {{-- MAIN ANALYTICS --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- BIG CHART --}}
        <div class="xl:col-span-8 sr-card p-6" style="background:linear-gradient(145deg, rgba(255,251,251,.96) 0%, rgba(255,240,240,.93) 55%, rgba(255,247,247,.96) 100%); border-color:rgba(228,176,176,.30);">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="text-2xl font-black" style="color:#2D1414;">Bookings Performance</div>
                    <div class="mt-1 text-sm font-semibold" style="color:#7B5B5B;">Platform activity trend over the last 6 months</div>
                </div>
                <div class="sr-badge" style="background:linear-gradient(135deg,#FFF3F3,#FFE8E8); color:{{ $primary2 }}; border:1px solid rgba(228,176,176,.34);">
                    <span class="h-2.5 w-2.5 rounded-full" style="background:{{ $primary2 }};"></span>
                    Live Trend
                </div>
            </div>

            <div class="mt-6 sr-chart-shell">
                @if($bookingTrend->count())
                    <div class="sr-grid-lines">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <div class="sr-chart-bars">
                        @foreach($bookingTrend as $point)
                            @php
                                $height = max(18, round(($point->total / $maxBookingTrend) * 195));
                            @endphp
                            <div class="sr-bar-col">
                                <div class="text-xs font-black text-[#7B5B5B]">{{ $point->total }}</div>
                                <div class="sr-bar"
                                     style="height: {{ $height }}px; background:linear-gradient(180deg, #F3A0A0, {{ $primary2 }}, {{ $primary }});"></div>
                                <div class="text-xs font-extrabold text-[#B19393]">{{ $point->mon }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex items-center justify-center text-sm font-semibold text-[#B19393]">
                        No booking trend data available yet.
                    </div>
                @endif
            </div>
        </div>

        {{-- SMART INSIGHTS --}}
        <div class="xl:col-span-4 flex flex-col gap-4">
            <div class="sr-card p-5 sr-rose-panel">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-black text-[#2D1414]">Most Common Room Type</div>
                        <div class="text-xs font-semibold text-[#7B5B5B] mt-1">Highest listing category</div>
                    </div>
                    <div class="sr-icon-box" style="background:linear-gradient(135deg,#FFF0F0,#FFDCDC); color:{{ $primary }};">🏠</div>
                </div>
                <div class="mt-5 text-3xl font-black text-[#2D1414]">
                    {{ $topRoomType?->room_type ? ucfirst($topRoomType->room_type) : '-' }}
                </div>
                <div class="mt-2 text-sm font-semibold text-[#7B5B5B]">
                    {{ $topRoomType?->total ?? 0 }} listing(s) dominate this category.
                </div>
            </div>

            <div class="sr-card p-5 sr-rose-panel-2">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-black text-[#2D1414]">Top Demand Area</div>
                        <div class="text-xs font-semibold text-[#7B5B5B] mt-1">Strongest location activity</div>
                    </div>
                    <div class="sr-icon-box" style="background:linear-gradient(135deg,#FFF0F0,#FFDCDC); color:{{ $primary2 }};">📍</div>
                </div>
                <div class="mt-5 text-3xl font-black text-[#2D1414]">
                    {{ $topLocation?->city ?? '-' }}
                </div>
                <div class="mt-2 text-sm font-semibold text-[#7B5B5B]">
                    {{ $topLocation?->total ?? 0 }} room listing(s) recorded in this area.
                </div>
            </div>

            <div class="sr-card p-5 sr-rose-panel">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-black text-[#2D1414]">Most Common Budget Range</div>
                        <div class="text-xs font-semibold text-[#7B5B5B] mt-1">Price preference pattern</div>
                    </div>
                    <div class="sr-icon-box" style="background:linear-gradient(135deg,#FFF0F0,#FFDCDC); color:{{ $accent }};">💰</div>
                </div>
                <div class="mt-5 text-2xl font-black text-[#2D1414]">
                    {{ $topBudget?->label ?? '-' }}
                </div>
                <div class="mt-2 text-sm font-semibold text-[#7B5B5B]">
                    {{ $topBudget?->total ?? 0 }} listing(s) fall within this budget bracket.
                </div>
            </div>
        </div>
    </div>

    {{-- DISTRIBUTION ROW --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-4 sr-card p-6 sr-hover">
            <div class="sr-title">Room Type Distribution</div>
            <div class="sr-sub">Listing category breakdown</div>

            <div class="mt-6 sr-stat-list">
                @forelse($roomTypeStats as $row)
                    @php $width = max(10, round(($row->total / $maxRoomType) * 100)); @endphp
                    <div class="sr-stat-row">
                        <div class="flex items-center justify-between text-sm font-black">
                            <span class="text-[#5A3A3A]">{{ ucfirst((string)$row->room_type) }}</span>
                            <span style="color:{{ $primary }};">{{ $row->total }}</span>
                        </div>
                        <div class="sr-mini-bar">
                            <div class="h-full rounded-full"
                                 style="width:{{ $width }}%; background:linear-gradient(90deg, {{ $primary }}, {{ $accent }});"></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-[#EBCFCF] bg-[#FFF1F1] px-5 py-8 text-center text-sm font-semibold text-[#B19393]">
                        No room type data available.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="xl:col-span-4 sr-card p-6 sr-hover">
            <div class="sr-title">Location Demand</div>
            <div class="sr-sub">Most active room areas</div>

            <div class="mt-6 sr-stat-list">
                @forelse($locationStats as $row)
                    @php $width = max(10, round(($row->total / $maxLocation) * 100)); @endphp
                    <div class="sr-stat-row">
                        <div class="flex items-center justify-between text-sm font-black">
                            <span class="text-[#5A3A3A] truncate">{{ $row->city }}</span>
                            <span style="color:{{ $primary2 }};">{{ $row->total }}</span>
                        </div>
                        <div class="sr-mini-bar">
                            <div class="h-full rounded-full"
                                 style="width:{{ $width }}%; background:linear-gradient(90deg, {{ $primary2 }}, {{ $accent }});"></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-[#EBCFCF] bg-[#FFF1F1] px-5 py-8 text-center text-sm font-semibold text-[#B19393]">
                        No location data available.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="xl:col-span-4 sr-card p-6 sr-hover">
            <div class="sr-title">Budget Distribution</div>
            <div class="sr-sub">Listing prices by rental range</div>

            <div class="mt-6 sr-stat-list">
                @foreach($budgetRanges as $row)
                    @php $width = max(10, round(($row->total / $maxBudget) * 100)); @endphp
                    <div class="sr-stat-row">
                        <div class="flex items-center justify-between text-sm font-black">
                            <span class="text-[#5A3A3A]">{{ $row->label }}</span>
                            <span style="color:{{ $amber }};">{{ $row->total }}</span>
                        </div>
                        <div class="sr-mini-bar">
                            <div class="h-full rounded-full"
                                 style="width:{{ $width }}%; background:linear-gradient(90deg, {{ $amber }}, #FCD34D);"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- PIPELINE + ACTIVITY --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-5 sr-card p-6 sr-hover">
            <div class="flex items-center justify-between">
                <div>
                    <div class="sr-title">Approval Pipeline</div>
                    <div class="sr-sub">Landlord verification status breakdown</div>
                </div>
                <a href="{{ $href('admin.landlords.all') }}" class="text-sm font-black" style="color:{{ $primary }};">Open →</a>
            </div>

            <div class="mt-6 space-y-4">
                <div class="sr-insight">
                    <div class="flex items-center justify-between text-sm font-black">
                        <span class="text-[#5A3A3A]">Approved</span>
                        <span class="text-emerald-600">{{ $approvedLandlords }} ({{ $pctApproved }}%)</span>
                    </div>
                    <div class="mt-3 sr-mini-bar">
                        <div class="h-full rounded-full" style="width:{{ $pctApproved }}%; background:linear-gradient(90deg, {{ $green }}, #6EE7B7);"></div>
                    </div>
                </div>

                <div class="sr-insight">
                    <div class="flex items-center justify-between text-sm font-black">
                        <span class="text-[#5A3A3A]">Pending</span>
                        <span style="color:{{ $amber }};">{{ $pendingLandlords }} ({{ $pctPending }}%)</span>
                    </div>
                    <div class="mt-3 sr-mini-bar">
                        <div class="h-full rounded-full" style="width:{{ $pctPending }}%; background:linear-gradient(90deg, {{ $amber }}, #FCD34D);"></div>
                    </div>
                </div>

                <div class="sr-insight">
                    <div class="flex items-center justify-between text-sm font-black">
                        <span class="text-[#5A3A3A]">Rejected</span>
                        <span class="text-red-600">{{ $rejectedLandlords }} ({{ $pctRejected }}%)</span>
                    </div>
                    <div class="mt-3 sr-mini-bar">
                        <div class="h-full rounded-full" style="width:{{ $pctRejected }}%; background:linear-gradient(90deg, {{ $red }}, #FCA5A5);"></div>
                    </div>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3">
                <a href="{{ $href('admin.landlords.index') }}"
                   class="rounded-2xl px-4 py-3 text-sm font-black text-center text-white sr-hover"
                   style="background:linear-gradient(135deg, {{ $primary2 }}, {{ $primary }});">
                    Review Pending
                </a>
                <a href="{{ $href('admin.landlords.all', ['tab' => 'rejected']) }}"
                   class="rounded-2xl px-4 py-3 text-sm font-black text-center border border-[#EBCFCF] bg-[#FFF3F3] hover:bg-[#FFEAEA] sr-hover">
                    View Rejected
                </a>
            </div>
        </div>

        <div class="xl:col-span-7 sr-card p-6 sr-hover">
            <div class="flex items-center justify-between">
                <div>
                    <div class="sr-title">Recent Activity</div>
                    <div class="sr-sub">Latest activity across the platform</div>
                </div>
                <div class="sr-badge" style="background:rgba(16,185,129,.10); color:{{ $green }}; border:1px solid rgba(16,185,129,.18);">
                    ● Live
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="sr-card-soft p-4">
                    <div class="font-black text-[#5A3A3A] text-[15px]">Pending Landlords</div>
                    <div class="text-xs text-[#9B7A7A] font-semibold mt-1">Newest 5</div>

                    <div class="mt-4 flex flex-col gap-3">
                        @forelse($latestPendingLandlords as $u)
                            <div class="sr-list-card">
                                <div class="font-black text-sm text-[#2D1414] truncate">{{ $u->name }}</div>
                                <div class="text-xs text-[#8A6B6B] font-semibold truncate mt-1">{{ $u->email }}</div>
                                <div class="text-xs text-[#B19393] font-semibold mt-2">
                                    {{ \Carbon\Carbon::parse($u->created_at)->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <div class="text-sm font-semibold text-[#B19393]">No pending records.</div>
                        @endforelse
                    </div>
                </div>

                <div class="sr-card-soft p-4">
                    <div class="font-black text-[#5A3A3A] text-[15px]">Latest Bookings</div>
                    <div class="text-xs text-[#9B7A7A] font-semibold mt-1">Newest 5</div>

                    <div class="mt-4 flex flex-col gap-3">
                        @forelse($latestBookings as $b)
                            <div class="sr-list-card">
                                <div class="font-black text-sm text-[#2D1414]">Booking #{{ $b->id }}</div>
                                <div class="text-xs text-[#B19393] font-semibold mt-2">
                                    {{ \Carbon\Carbon::parse($b->created_at)->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <div class="text-sm font-semibold text-[#B19393]">No booking records.</div>
                        @endforelse
                    </div>
                </div>

                <div class="sr-card-soft p-4">
                    <div class="font-black text-[#5A3A3A] text-[15px]">Latest Payments</div>
                    <div class="text-xs text-[#9B7A7A] font-semibold mt-1">Newest 5</div>

                    <div class="mt-4 flex flex-col gap-3">
                        @forelse($latestPayments as $p)
                            <div class="sr-list-card">
                                <div class="font-black text-sm text-[#2D1414]">Payment #{{ $p->id }}</div>
                                <div class="mt-2">
                                    <span class="sr-pill">
                                        {{ ucfirst($p->status ?? 'unknown') }}
                                    </span>
                                </div>
                                <div class="text-xs text-[#B19393] font-semibold mt-2">
                                    {{ \Carbon\Carbon::parse($p->created_at)->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <div class="text-sm font-semibold text-[#B19393]">No payment records.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BOTTOM KPI --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="sr-card-soft p-5 sr-hover">
            <div class="sr-kpi-title">Approved Payments</div>
            <div class="sr-kpi-value mt-3">{{ $approvedPayments }}</div>
            <div class="text-xs font-semibold text-[#7B5B5B] mt-3">Successfully processed payment records</div>
        </div>

        <div class="sr-card-soft p-5 sr-hover">
            <div class="sr-kpi-title">Total Revenue</div>
            <div class="sr-kpi-value mt-3">RM {{ number_format($totalRevenue, 0) }}</div>
            <div class="text-xs font-semibold text-[#7B5B5B] mt-3">Paid transaction value recorded</div>
        </div>

        <div class="sr-card-soft p-5 sr-hover">
            <div class="sr-kpi-title">Bookings Per Room</div>
            <div class="sr-kpi-value mt-3">{{ $bookingPerRoom }}</div>
            <div class="text-xs font-semibold text-[#7B5B5B] mt-3">Average booking ratio relative to listings</div>
        </div>

        <div class="sr-card-soft p-5 sr-hover">
            <div class="sr-kpi-title">Most Saved Area</div>
            <div class="sr-kpi-value mt-3 truncate">{{ $mostSavedArea }}</div>
            <div class="text-xs font-semibold text-[#7B5B5B] mt-3">Most bookmarked area from student behavior</div>
        </div>
    </div>

</div>

@endsection