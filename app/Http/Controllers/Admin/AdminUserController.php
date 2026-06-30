<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'students');
        $tab = in_array($tab, ['students', 'landlords'], true) ? $tab : 'students';

        $q = trim((string) $request->get('q', ''));

        $User = \App\Models\User::class;
        $Room = class_exists(\App\Models\Room::class) ? \App\Models\Room::class : null;

        $query = $User::query();

        // filter role
        $query->where('role', $tab === 'landlords' ? 'landlord' : 'student');

        // search
        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $users = $query->orderByDesc('id')->paginate(10)->withQueryString();

        // top cards counts
        $studentsCount  = $User::where('role', 'student')->count();
        $landlordsCount = $User::where('role', 'landlord')->count();

        // pending landlords count based on your real column landlord_status
        $pendingCount = 0;
        if (Schema::hasColumn('users', 'landlord_status')) {
            $pendingCount = $User::where('role', 'landlord')
                ->where(function ($x) {
                    $x->whereNull('landlord_status')
                      ->orWhere('landlord_status', 'pending');
                })
                ->count();
        }

        /**
         * ✅ IMPORTANT FIX:
         * We DO NOT use $user->rooms() relationship (you don't have it)
         * We compute rooms_count manually using rooms.landlord_id
         */
        if ($tab === 'landlords' && $Room && Schema::hasTable('rooms') && Schema::hasColumn('rooms', 'landlord_id')) {

            $ids = $users->pluck('id')->all();

            $map = $Room::query()
                ->selectRaw('landlord_id, COUNT(*) as cnt')
                ->whereIn('landlord_id', $ids)
                ->groupBy('landlord_id')
                ->pluck('cnt', 'landlord_id')
                ->toArray();

            foreach ($users as $u) {
                $u->rooms_count = (int)($map[$u->id] ?? 0);
            }
        }

        return view('admin.users.index', compact(
            'users',
            'tab',
            'q',
            'studentsCount',
            'landlordsCount',
            'pendingCount'
        ));
    }

    public function show($user)
    {
        $User = \App\Models\User::class;
        $Room = class_exists(\App\Models\Room::class) ? \App\Models\Room::class : null;

        $user = $User::findOrFail($user);

        $role = ($user->role === 'landlord') ? 'landlord' : 'student';

        // Rooms count (landlord only)
        $roomsCount = 0;
        if ($role === 'landlord' && $Room && Schema::hasTable('rooms') && Schema::hasColumn('rooms', 'landlord_id')) {
            $roomsCount = $Room::where('landlord_id', $user->id)->count();
        }

        // Bookings count (based on your bookings table columns)
        $bookingsCount = 0;
        if (Schema::hasTable('bookings') && class_exists(\App\Models\Booking::class)) {
            $Booking = \App\Models\Booking::class;
            $b = $Booking::query();

            if ($role === 'landlord' && Schema::hasColumn('bookings', 'landlord_id')) {
                $b->where('landlord_id', $user->id);
            } elseif ($role === 'student' && Schema::hasColumn('bookings', 'student_id')) {
                $b->where('student_id', $user->id);
            }

            $bookingsCount = $b->count();
        }

        // Payments count (NOT overall)
        $paymentsCount = 0;
        if (Schema::hasTable('payments') && class_exists(\App\Models\Payment::class)) {
            $Payment = \App\Models\Payment::class;
            $p = $Payment::query();

            if ($role === 'student' && Schema::hasColumn('payments', 'student_id')) {
                $paymentsCount = $p->where('student_id', $user->id)->count();
            } elseif ($role === 'landlord' && Schema::hasColumn('payments', 'landlord_id')) {
                $paymentsCount = $p->where('landlord_id', $user->id)->count();
            }
            // if payments only has booking_id, count via bookings
            elseif (Schema::hasColumn('payments', 'booking_id') && class_exists(\App\Models\Booking::class)) {
                $Booking = \App\Models\Booking::class;

                $bookingIds = $Booking::query()
                    ->when($role === 'student' && Schema::hasColumn('bookings', 'student_id'), fn($x) => $x->where('student_id', $user->id))
                    ->when($role === 'landlord' && Schema::hasColumn('bookings', 'landlord_id'), fn($x) => $x->where('landlord_id', $user->id))
                    ->pluck('id');

                $paymentsCount = $p->whereIn('booking_id', $bookingIds)->count();
            }
        }

        return view('admin.users.show', compact(
            'user',
            'role',
            'roomsCount',
            'bookingsCount',
            'paymentsCount'
        ));
    }
}