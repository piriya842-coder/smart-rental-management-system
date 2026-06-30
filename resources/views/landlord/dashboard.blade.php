@extends('layouts.landlord')

@section('title', 'Landlord Dashboard • Smart Rental')
@section('page_title', 'Landlord Dashboard')
@section('page_subtitle', 'Luxury panel to manage listings, bookings, payments, and tenant activity.')

@section('top_actions')
    <a href="{{ route('landlord.rooms.create') }}"
       class="sr-top-btn sr-top-btn-gold rounded-2xl px-6 py-3 text-sm font-extrabold inline-flex items-center justify-center gap-2">
        <span class="text-lg -mt-[1px]">+</span> Add Room
    </a>
@endsection

@section('content')
    <style>
        .sr-panel{
            border: 1px solid #F1D4D8;
            box-shadow: 0 16px 40px rgba(127,16,27,.08);
        }

        .sr-hover-lift{
            transition: all .25s ease;
        }
        .sr-hover-lift:hover{
            transform: translateY(-3px);
            box-shadow: 0 18px 36px rgba(127,16,27,.14);
        }

        .sr-hero{
            background:
                radial-gradient(circle at top right, rgba(220,38,38,.16), transparent 26%),
                radial-gradient(circle at left bottom, rgba(127,16,27,.10), transparent 24%),
                linear-gradient(135deg, #FFF8F8 0%, #FFF1F2 100%);
        }

        .sr-top-btn{
            min-height: 54px;
            width: 100%;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            transition: all .25s ease;
            box-shadow: 0 10px 24px rgba(127,16,27,.10);
        }
        .sr-top-btn:hover{
            transform: translateY(-2px);
        }
        .sr-top-btn-rooms{
            background: #FFFFFF;
            color: #7F101B;
            border: 1px solid #F1D4D8;
        }
        .sr-top-btn-bookings{
            background: linear-gradient(135deg, #991B1B 0%, #B91C1C 100%);
            color: #FFFFFF;
            border: 1px solid #991B1B;
        }
        .sr-top-btn-payments{
            background: linear-gradient(135deg, #2A0709 0%, #4D0B12 100%);
            color: #FFFFFF;
            border: 1px solid #2A0709;
        }
        .sr-top-btn-gold{
            background: linear-gradient(135deg, #B91C1C 0%, #DC2626 100%);
            color: #FFFFFF;
            border: 1px solid #C81E2A;
            box-shadow: 0 14px 28px rgba(185,28,28,.28);
        }

        .sr-alert-blue{
            background:
                radial-gradient(circle at top right, rgba(220,38,38,.14), transparent 30%),
                linear-gradient(135deg, #FFFFFF 0%, #FFF5F5 100%);
            border: 1px solid #F3C6CB;
        }

        .sr-alert-orange{
            background:
                radial-gradient(circle at top right, rgba(185,28,28,.14), transparent 30%),
                linear-gradient(135deg, #FFFFFF 0%, #FFF3F4 100%);
            border: 1px solid #F2B8C0;
        }

        .sr-alert-purple{
            background:
                radial-gradient(circle at top right, rgba(127,16,27,.14), transparent 30%),
                linear-gradient(135deg, #FFFFFF 0%, #FFF1F2 100%);
            border: 1px solid #F0C7CE;
        }

        .sr-metric-card{
            background: linear-gradient(135deg, #FFFFFF 0%, #FFF8F8 100%);
            border: 1px solid #F1D4D8;
        }

        .sr-metric-accent-green{
            background:
                radial-gradient(circle at top right, rgba(220,38,38,.14), transparent 30%),
                linear-gradient(135deg, #FFFFFF 0%, #FFF5F5 100%);
            border: 1px solid #F3C6CB;
        }

        .sr-metric-accent-gold{
            background:
                radial-gradient(circle at top right, rgba(185,28,28,.14), transparent 30%),
                linear-gradient(135deg, #FFFFFF 0%, #FFF1F2 100%);
            border: 1px solid #F2B8C0;
        }

        .sr-metric-accent-slate{
            background:
                radial-gradient(circle at top right, rgba(127,16,27,.10), transparent 30%),
                linear-gradient(135deg, #FFFFFF 0%, #FFF8F8 100%);
            border: 1px solid #F1D4D8;
        }

        .sr-booking-shell{
            background:
                radial-gradient(circle at top right, rgba(220,38,38,.10), transparent 26%),
                linear-gradient(135deg, #FFF5F5 0%, #FFFFFF 100%);
            border: 1px solid #F3D7DB;
        }

        .sr-booking-stat{
            background: rgba(255,255,255,.96);
            border: 1px solid #F1D4D8;
            box-shadow: 0 8px 18px rgba(127,16,27,.06);
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .sr-mini-btn{
            min-height: 48px;
            border-radius: 16px;
            padding: 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            transition: all .25s ease;
            border: 1px solid transparent;
            width: 100%;
        }
        .sr-mini-btn:hover{
            transform: translateY(-2px);
        }
        .sr-mini-btn-light{
            background: #FFFFFF;
            color: #7F101B;
            border-color: #F1D4D8;
            box-shadow: 0 8px 18px rgba(127,16,27,.08);
        }
        .sr-mini-btn-blue{
            background: linear-gradient(135deg, #B91C1C 0%, #DC2626 100%);
            color: #FFFFFF;
            box-shadow: 0 12px 24px rgba(185,28,28,.24);
        }
        .sr-mini-btn-purple{
            background: linear-gradient(135deg, #7F101B 0%, #991B1B 100%);
            color: #FFFFFF;
            box-shadow: 0 12px 24px rgba(127,16,27,.24);
        }

        .sr-room-row{
            transition: all .2s ease;
        }
        .sr-room-row:hover{
            background: #FFF7F7;
        }

        .sr-badge{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 14px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 13px;
            white-space: nowrap;
        }
    </style>

    {{-- HERO --}}
    <div class="rounded-[34px] p-7 md:p-10 mb-6 sr-panel sr-hero overflow-hidden">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-8">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-black tracking-wider"
                     style="background:#fff; color:#991B1B; border:1px solid #F3C6CB;">
                    ✨ LANDLORD CONTROL CENTER
                </div>

                <div class="text-3xl md:text-5xl font-extrabold mt-4 tracking-tight leading-tight"
                     style="color:#2A0709;">
                    Manage your rooms with confidence
                </div>

                <div class="text-sm md:text-base mt-4 max-w-2xl leading-relaxed"
                     style="color:#7C4A52;">
                    View listing performance, handle bookings, verify payments, and stay updated with tenant communication from one premium dashboard.
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <div class="inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-extrabold"
                         style="background:#FFFFFF; color:#991B1B; border:1px solid #F3C6CB;">
                        <span class="h-2.5 w-2.5 rounded-full" style="background:#DC2626;"></span>
                        {{ $activeRooms ?? 0 }} active room(s)
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-extrabold"
                         style="background:#FFFFFF; color:#7F101B; border:1px solid #F1D4D8;">
                        📌 {{ $totalBookings ?? 0 }} booking(s)
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-extrabold"
                         style="background:#FFFFFF; color:#B91C1C; border:1px solid #F2B8C0;">
                        💬 {{ $unreadMessages ?? 0 }} unread message(s)
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 xl:w-[460px]">
                <a href="{{ route('landlord.rooms.index') }}"
                   class="sr-top-btn sr-top-btn-rooms">
                    View My Rooms
                </a>

                <a href="{{ route('landlord.bookings.index') }}"
                   class="sr-top-btn sr-top-btn-bookings">
                    Manage Bookings
                </a>

                <a href="{{ route('landlord.payments.index') }}"
                   class="sr-top-btn sr-top-btn-payments">
                    Verify Payments
                </a>

                <a href="{{ route('landlord.rooms.create') }}"
                   class="sr-top-btn sr-top-btn-gold">
                    + Create Listing
                </a>
            </div>
        </div>
    </div>

    {{-- TOP ALERTS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <a href="{{ route('landlord.payments.index') }}"
           class="rounded-[30px] p-6 sr-panel sr-hover-lift sr-alert-blue block">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-bold" style="color:#991B1B;">Payment Verification Needed</div>
                    <div class="text-4xl font-extrabold mt-3" style="color:#B91C1C;">{{ $paymentSubmittedBookings ?? 0 }}</div>
                    <div class="text-sm mt-3 leading-relaxed" style="color:#7F101B;">
                        Submitted payment receipts waiting for your review.
                    </div>
                </div>
                <div class="h-14 w-14 rounded-2xl grid place-items-center text-2xl"
                     style="background:#FFFFFF; border:1px solid #F3C6CB;">
                    💳
                </div>
            </div>
        </a>

        <a href="{{ route('landlord.bookings.index') }}"
           class="rounded-[30px] p-6 sr-panel sr-hover-lift sr-alert-orange block">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-bold" style="color:#991B1B;">Cancel / Refund Requests</div>
                    <div class="text-4xl font-extrabold mt-3" style="color:#DC2626;">{{ $cancelRequestedBookings ?? 0 }}</div>
                    <div class="text-sm mt-3 leading-relaxed" style="color:#7F101B;">
                        Requests that may require quick landlord action.
                    </div>
                </div>
                <div class="h-14 w-14 rounded-2xl grid place-items-center text-2xl"
                     style="background:#FFFFFF; border:1px solid #F2B8C0;">
                    📩
                </div>
            </div>
        </a>

        <a href="{{ route('landlord.messages.index') }}"
           class="rounded-[30px] p-6 sr-panel sr-hover-lift sr-alert-purple block">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-bold" style="color:#7F101B;">Unread Conversations</div>
                    <div class="text-4xl font-extrabold mt-3" style="color:#991B1B;">{{ $unreadMessages ?? 0 }}</div>
                    <div class="text-sm mt-3 leading-relaxed" style="color:#7F101B;">
                        Tenant messages that are waiting for your reply.
                    </div>
                </div>
                <div class="h-14 w-14 rounded-2xl grid place-items-center text-2xl"
                     style="background:#FFFFFF; border:1px solid #F0C7CE;">
                    💬
                </div>
            </div>
        </a>
    </div>

    {{-- ROOM METRICS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <div class="rounded-[30px] p-6 sr-panel sr-hover-lift sr-metric-card">
            <div class="text-sm font-semibold" style="color:#7C4A52;">Total Rooms</div>
            <div class="text-5xl font-extrabold mt-3" style="color:#2A0709;">{{ $totalRooms ?? 0 }}</div>
            <div class="text-xs mt-4 leading-relaxed" style="color:#B08990;">All listings under your account</div>
        </div>

        <div class="rounded-[30px] p-6 sr-panel sr-hover-lift sr-metric-accent-green">
            <div class="text-sm font-semibold" style="color:#991B1B;">Active</div>
            <div class="text-5xl font-extrabold mt-3" style="color:#B91C1C;">{{ $activeRooms ?? 0 }}</div>
            <div class="mt-4 inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-extrabold"
                 style="background:#FFFFFF; color:#991B1B; border:1px solid #F3C6CB;">
                <span class="h-2.5 w-2.5 rounded-full" style="background:#DC2626;"></span>
                Visible to students
            </div>
        </div>

        <div class="rounded-[30px] p-6 sr-panel sr-hover-lift sr-metric-accent-gold">
            <div class="text-sm font-semibold" style="color:#991B1B;">Draft</div>
            <div class="text-5xl font-extrabold mt-3" style="color:#DC2626;">{{ $draftRooms ?? 0 }}</div>
            <div class="mt-4 inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-extrabold"
                 style="background:#FFFFFF; color:#991B1B; border:1px solid #F2B8C0;">
                <span class="h-2.5 w-2.5 rounded-full" style="background:#B91C1C;"></span>
                Private before publish
            </div>
        </div>

        <div class="rounded-[30px] p-6 sr-panel sr-hover-lift sr-metric-accent-slate">
            <div class="text-sm font-semibold" style="color:#7C4A52;">Inactive</div>
            <div class="text-5xl font-extrabold mt-3" style="color:#2A0709;">{{ $inactiveRooms ?? 0 }}</div>
            <div class="mt-4 inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-extrabold"
                 style="background:#FFFFFF; color:#7C4A52; border:1px solid #F1D4D8;">
                <span class="h-2.5 w-2.5 rounded-full" style="background:#A1626B;"></span>
                Hidden / unavailable
            </div>
        </div>
    </div>

    {{-- BOOKING OVERVIEW + INSIGHTS --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mb-8">
        <div class="xl:col-span-8 rounded-[32px] overflow-hidden sr-panel"
             style="background:#FFFFFF;">
            <div class="px-6 py-5 border-b"
                 style="border-color:#F1D4D8; background:linear-gradient(135deg, #FFFFFF 0%, #FFF3F4 100%);">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="text-2xl font-extrabold" style="color:#2A0709;">Booking Overview</div>
                        <div class="text-sm mt-1" style="color:#7C4A52;">
                            Live tenant and booking activity across your rooms
                        </div>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-black tracking-wide"
                         style="background:linear-gradient(135deg, #B91C1C 0%, #DC2626 100%); color:#FFFFFF;">
                        REAL-TIME SNAPSHOT
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="sr-booking-shell rounded-[30px] p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 items-stretch">
                        <div class="rounded-[22px] p-5 sr-booking-stat">
                            <div class="text-[11px] font-black uppercase tracking-wide" style="color:#B08990;">Total Bookings</div>
                            <div class="text-4xl font-extrabold mt-3" style="color:#2A0709;">{{ $totalBookings ?? 0 }}</div>
                        </div>

                        <div class="rounded-[22px] p-5 sr-booking-stat">
                            <div class="text-[11px] font-black uppercase tracking-wide" style="color:#991B1B;">Pending</div>
                            <div class="text-4xl font-extrabold mt-3" style="color:#B91C1C;">{{ $pendingBookings ?? 0 }}</div>
                        </div>

                        <div class="rounded-[22px] p-5 sr-booking-stat">
                            <div class="text-[11px] font-black uppercase tracking-wide" style="color:#991B1B;">Submitted</div>
                            <div class="text-4xl font-extrabold mt-3" style="color:#DC2626;">{{ $paymentSubmittedBookings ?? 0 }}</div>
                        </div>

                        <div class="rounded-[22px] p-5 sr-booking-stat">
                            <div class="text-[11px] font-black uppercase tracking-wide" style="color:#7F101B;">Active Tenants</div>
                            <div class="text-4xl font-extrabold mt-3" style="color:#991B1B;">{{ $paidBookings ?? 0 }}</div>
                        </div>

                        <div class="rounded-[22px] p-5 sr-booking-stat">
                            <div class="text-[11px] font-black uppercase tracking-wide" style="color:#991B1B;">Cancel Requests</div>
                            <div class="text-4xl font-extrabold mt-3" style="color:#DC2626;">{{ $cancelRequestedBookings ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-3">
                        <a href="{{ route('landlord.bookings.index') }}"
                           class="sr-mini-btn sr-mini-btn-light">
                            Open Bookings
                        </a>

                        <a href="{{ route('landlord.payments.index') }}"
                           class="sr-mini-btn sr-mini-btn-blue">
                            Payment Review
                        </a>

                        <a href="{{ route('landlord.messages.index') }}"
                           class="sr-mini-btn sr-mini-btn-purple">
                            Reply Students
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-4 rounded-[32px] overflow-hidden sr-panel"
             style="background:#FFFFFF;">
            <div class="px-6 py-5 border-b"
                 style="border-color:#F1D4D8; background:linear-gradient(135deg, #FFFFFF 0%, #FFF3F4 100%);">
                <div class="text-2xl font-extrabold" style="color:#2A0709;">Landlord Insights</div>
                <div class="text-sm mt-1" style="color:#7C4A52;">
                    Smart business indicators from your listings
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="rounded-[24px] p-5 sr-panel sr-hover-lift"
                     style="background:
                        radial-gradient(circle at top right, rgba(220,38,38,.14), transparent 30%),
                        linear-gradient(135deg, #FFFFFF 0%, #FFF5F5 100%);">
                    <div class="text-xs font-black uppercase tracking-wide" style="color:#B08990;">Potential Monthly Income</div>
                    <div class="text-3xl font-extrabold mt-3" style="color:#2A0709;">
                        RM {{ number_format((float) ($potentialMonthlyIncome ?? 0), 2) }}
                    </div>
                </div>

                <div class="rounded-[24px] p-5 sr-panel sr-hover-lift">
                    <div class="text-xs font-black uppercase tracking-wide" style="color:#B08990;">Highest Rent Room</div>
                    <div class="text-lg font-extrabold mt-3 leading-snug" style="color:#2A0709;">
                        {{ $highestRentRoom?->title ?? 'No room yet' }}
                    </div>
                    <div class="text-sm mt-2" style="color:#7C4A52;">
                        @if($highestRentRoom)
                            RM {{ number_format((float) $highestRentRoom->price_monthly, 2) }}
                        @else
                            —
                        @endif
                    </div>
                </div>

                <div class="rounded-[24px] p-5 sr-panel sr-hover-lift">
                    <div class="text-xs font-black uppercase tracking-wide" style="color:#B08990;">Lowest Rent Room</div>
                    <div class="text-lg font-extrabold mt-3 leading-snug" style="color:#2A0709;">
                        {{ $lowestRentRoom?->title ?? 'No room yet' }}
                    </div>
                    <div class="text-sm mt-2" style="color:#7C4A52;">
                        @if($lowestRentRoom)
                            RM {{ number_format((float) $lowestRentRoom->price_monthly, 2) }}
                        @else
                            —
                        @endif
                    </div>
                </div>

                <div class="rounded-[24px] p-5 sr-panel sr-hover-lift"
                     style="background:
                        radial-gradient(circle at top right, rgba(185,28,28,.12), transparent 30%),
                        linear-gradient(135deg, #FFFFFF 0%, #FFF3F4 100%);">
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-semibold" style="color:#5B1E25;">Unread Notifications</span>
                        <span class="text-2xl font-extrabold" style="color:#B91C1C;">{{ $unreadNotifications ?? 0 }}</span>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('landlord.notifications.index') }}"
                           class="sr-top-btn sr-top-btn-rooms rounded-xl px-4 py-2 text-sm font-extrabold inline-flex items-center justify-center gap-2"
                           style="min-width:auto; min-height:44px; width:auto;">
                            🔔 Open notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="rounded-[32px] overflow-hidden sr-panel mb-8"
         style="background:#FFFFFF;">
        <div class="px-6 py-5 border-b"
             style="border-color:#F1D4D8; background:linear-gradient(135deg, #FFFFFF 0%, #FFF3F4 100%);">
            <div class="text-2xl font-extrabold" style="color:#2A0709;">Quick Actions</div>
            <div class="text-sm mt-1" style="color:#7C4A52;">
                Jump straight to the most important landlord tasks
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <a href="{{ route('landlord.rooms.create') }}"
               class="rounded-[26px] p-6 sr-panel sr-hover-lift block"
               style="background:
                    radial-gradient(circle at top right, rgba(220,38,38,.16), transparent 28%),
                    linear-gradient(135deg, #FFFFFF 0%, #FFF1F2 100%);">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl grid place-items-center text-2xl"
                         style="background:#FFFFFF; border:1px solid #F3C6CB;">🏠</div>
                    <div class="text-lg font-extrabold" style="color:#991B1B;">Add New Room</div>
                </div>
                <div class="text-sm mt-4 leading-relaxed" style="color:#7F101B;">
                    Create a new room listing and publish when ready.
                </div>
            </a>

            <a href="{{ route('landlord.bookings.index') }}"
               class="rounded-[26px] p-6 sr-panel sr-hover-lift block"
               style="background:
                    radial-gradient(circle at top right, rgba(185,28,28,.14), transparent 28%),
                    linear-gradient(135deg, #FFFFFF 0%, #FFF5F5 100%);">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl grid place-items-center text-2xl"
                         style="background:#FFFFFF; border:1px solid #F2B8C0;">📌</div>
                    <div class="text-lg font-extrabold" style="color:#B91C1C;">Review Bookings</div>
                </div>
                <div class="text-sm mt-4 leading-relaxed" style="color:#7F101B;">
                    Monitor tenant bookings from pending to active.
                </div>
            </a>

            <a href="{{ route('landlord.payments.index') }}"
               class="rounded-[26px] p-6 sr-panel sr-hover-lift block"
               style="background:
                    radial-gradient(circle at top right, rgba(127,16,27,.14), transparent 28%),
                    linear-gradient(135deg, #FFFFFF 0%, #FFF3F4 100%);">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl grid place-items-center text-2xl"
                         style="background:#FFFFFF; border:1px solid #F0C7CE;">💳</div>
                    <div class="text-lg font-extrabold" style="color:#7F101B;">Verify Payments</div>
                </div>
                <div class="text-sm mt-4 leading-relaxed" style="color:#7F101B;">
                    Approve or reject uploaded student receipts.
                </div>
            </a>

            <a href="{{ route('landlord.messages.index') }}"
               class="rounded-[26px] p-6 sr-panel sr-hover-lift block"
               style="background:
                    radial-gradient(circle at top right, rgba(220,38,38,.14), transparent 28%),
                    linear-gradient(135deg, #FFFFFF 0%, #FFF5F5 100%);">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl grid place-items-center text-2xl"
                         style="background:#FFFFFF; border:1px solid #F3C6CB;">💬</div>
                    <div class="text-lg font-extrabold" style="color:#991B1B;">Open Messages</div>
                </div>
                <div class="text-sm mt-4 leading-relaxed" style="color:#7F101B;">
                    Reply to students and manage live conversations.
                </div>
            </a>
        </div>
    </div>

    {{-- LATEST ROOMS --}}
    <div class="rounded-[32px] overflow-hidden sr-panel"
         style="background:#FFFFFF;">
        <div class="px-6 py-5 flex items-center justify-between border-b"
             style="border-color:#F1D4D8; background:linear-gradient(135deg, #FFFFFF 0%, #FFF3F4 100%);">
            <div>
                <div class="text-2xl font-extrabold" style="color:#2A0709;">Latest Rooms</div>
                <div class="text-sm mt-1" style="color:#7C4A52;">
                    Recent listings with cover photo, status, and quick edit access
                </div>
            </div>

            <a href="{{ route('landlord.rooms.index') }}"
               class="sr-top-btn sr-top-btn-rooms rounded-xl px-4 py-2 text-sm font-extrabold inline-flex items-center justify-center"
               style="min-width:auto; min-height:44px; width:auto;">
                View all
            </a>
        </div>

        <div>
            @forelse($latestRooms as $room)
                @php
                    $img = $room->cover_image
                        ? asset('storage/'.$room->cover_image)
                        : 'https://placehold.co/120x90?text=Room';
                @endphp

                <div class="px-6 py-5 flex items-center justify-between gap-4 border-b last:border-b-0 sr-room-row"
                     style="border-color:#F1D4D8;">
                    <div class="flex items-center gap-4 min-w-0">
                        <img src="{{ $img }}"
                             alt="cover"
                             class="h-[76px] w-[112px] rounded-[20px] object-cover"
                             style="border:1px solid #F1D4D8; background:#FFF8F8; box-shadow:0 10px 18px rgba(127,16,27,.08);" />

                        <div class="min-w-0">
                            <div class="font-extrabold text-lg truncate" style="color:#2A0709;">
                                {{ $room->title }}
                            </div>
                            <div class="text-sm mt-1" style="color:#7C4A52;">
                                {{ ucfirst($room->room_type) }}
                                • {{ $room->city }}
                                • RM {{ number_format($room->price_monthly, 2) }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        @if($room->status === 'active')
                            <span class="sr-badge"
                                  style="background:#FFF5F5; color:#991B1B; border:1px solid #F3C6CB;">
                                ACTIVE
                            </span>
                        @elseif($room->status === 'draft')
                            <span class="sr-badge"
                                  style="background:#FFF1F2; color:#B91C1C; border:1px solid #F2B8C0;">
                                DRAFT
                            </span>
                        @else
                            <span class="sr-badge"
                                  style="background:#FFF8F8; color:#7C4A52; border:1px solid #F1D4D8;">
                                INACTIVE
                            </span>
                        @endif

                        <a href="{{ route('landlord.rooms.edit', $room->id) }}"
                           class="sr-top-btn sr-top-btn-rooms rounded-xl px-4 py-2 text-sm font-extrabold inline-flex items-center justify-center"
                           style="min-width:auto; min-height:44px; width:auto;">
                            Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center" style="color:#7C4A52;">
                    No rooms yet. Create your first listing.
                    <div class="mt-5">
                        <a href="{{ route('landlord.rooms.create') }}"
                           class="sr-top-btn sr-top-btn-gold rounded-2xl px-6 py-3 text-sm font-extrabold inline-flex items-center justify-center"
                           style="width:auto;">
                            + Add Room
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection