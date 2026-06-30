<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Dispute;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        // ---- date filter (optional)
        $from = $request->query('from');
        $to   = $request->query('to');

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : null;
        $toDate   = $to   ? Carbon::parse($to)->endOfDay()   : null;

        $rangeActive = (bool)($fromDate && $toDate);

        $between = function ($query, $column = 'created_at') use ($fromDate, $toDate, $rangeActive) {
            if ($rangeActive) {
                $query->whereBetween($column, [$fromDate, $toDate]);
            }
            return $query;
        };

        // ---- USERS
        $usersQ = User::query();
        $between($usersQ);

        $totalUsers     = (clone $usersQ)->count();
        $studentsCount  = (clone $usersQ)->where('role', 'student')->count();
        $landlordsCount = (clone $usersQ)->where('role', 'landlord')->count();

        // ---- ROOMS
        $roomsQ = Room::query();
        $between($roomsQ);

        $totalRooms = (clone $roomsQ)->count();

        // "verified" support (safe: checks which column exists)
        $roomVerifiedCount = null;
        $roomPendingCount  = null;

        if (DB::getSchemaBuilder()->hasColumn('rooms', 'verification_status')) {
            $roomVerifiedCount = (clone $roomsQ)->where('verification_status', 'approved')->count();
            $roomPendingCount  = (clone $roomsQ)->where('verification_status', 'pending')->count();
        } elseif (DB::getSchemaBuilder()->hasColumn('rooms', 'is_verified')) {
            $roomVerifiedCount = (clone $roomsQ)->where('is_verified', 1)->count();
            $roomPendingCount  = (clone $roomsQ)->where('is_verified', 0)->count();
        }

        // ---- BOOKINGS
        $bookingsQ = Booking::query();
        $between($bookingsQ);

        $totalBookings = (clone $bookingsQ)->count();

        // if your bookings table has "status", show breakdown
        $bookingPending = null;
        $bookingActive  = null;
        $bookingDone    = null;
        $bookingCancel  = null;

        if (DB::getSchemaBuilder()->hasColumn('bookings', 'status')) {
            $bookingPending = (clone $bookingsQ)->where('status', 'pending')->count();
            $bookingActive  = (clone $bookingsQ)->whereIn('status', ['active', 'approved', 'payment_submitted', 'paid'])->count();
            $bookingDone    = (clone $bookingsQ)->whereIn('status', ['completed'])->count();
            $bookingCancel  = (clone $bookingsQ)->whereIn('status', ['cancelled', 'canceled'])->count();
        }

        // ---- PAYMENTS (IMPORTANT: revenue uses payments.created_at)
        $paymentsRangeQ = Payment::query();
        $between($paymentsRangeQ, 'created_at'); // filter uses payment date

        $paidCountRange    = (clone $paymentsRangeQ)->where('status', 'paid')->count();
        $pendingCountRange = (clone $paymentsRangeQ)->where('status', '!=', 'paid')->count();

        $revenuePaidRange = (float) (clone $paymentsRangeQ)
            ->where('status', 'paid')
            ->sum('amount');

        // all-time revenue (for “real system feel”)
        $revenuePaidAll = (float) Payment::where('status', 'paid')->sum('amount');
        $paidCountAll   = (int) Payment::where('status', 'paid')->count();

        // ---- DISPUTES (if you already created disputes table)
        $disputesQ = Dispute::query();
        $between($disputesQ);

        $totalDisputes = (clone $disputesQ)->count();

        $openDisputes = null;
        $closedDisputes = null;
        $highPriorityDisputes = null;

        if (DB::getSchemaBuilder()->hasColumn('disputes', 'status')) {
            $openDisputes   = (clone $disputesQ)->whereIn('status', ['open', 'in_review'])->count();
            $closedDisputes = (clone $disputesQ)->where('status', 'resolved')->count();
        }
        if (DB::getSchemaBuilder()->hasColumn('disputes', 'priority')) {
            $highPriorityDisputes = (clone $disputesQ)->where('priority', 'high')->count();
        }

        // ---- LEADERBOARD (Top landlords by rooms) - NO User::rooms() needed
        $topLandlords = User::query()
            ->where('role', 'landlord')
            ->select([
                'users.id', 'users.name', 'users.email', 'users.landlord_status',
                DB::raw('COUNT(rooms.id) as rooms_count'),
            ])
            ->leftJoin('rooms', 'rooms.landlord_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.landlord_status')
            ->orderByDesc('rooms_count')
            ->orderByDesc('users.id')
            ->limit(6)
            ->get();

        // ---- send to view (NO undefined variables)
        return view('admin.reports.index', [
            'from' => $fromDate?->toDateString(),
            'to' => $toDate?->toDateString(),
            'rangeActive' => $rangeActive,

            'totalUsers' => $totalUsers,
            'studentsCount' => $studentsCount,
            'landlordsCount' => $landlordsCount,

            'totalRooms' => $totalRooms,
            'roomVerifiedCount' => $roomVerifiedCount,
            'roomPendingCount' => $roomPendingCount,

            'totalBookings' => $totalBookings,
            'bookingPending' => $bookingPending,
            'bookingActive' => $bookingActive,
            'bookingDone' => $bookingDone,
            'bookingCancel' => $bookingCancel,

            'revenuePaidRange' => $revenuePaidRange,
            'paidCountRange' => $paidCountRange,
            'pendingCountRange' => $pendingCountRange,

            'revenuePaidAll' => $revenuePaidAll,
            'paidCountAll' => $paidCountAll,

            'totalDisputes' => $totalDisputes,
            'openDisputes' => $openDisputes,
            'closedDisputes' => $closedDisputes,
            'highPriorityDisputes' => $highPriorityDisputes,

            'topLandlords' => $topLandlords,
        ]);
    }
}