<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class LandlordDashboardController extends Controller
{
    public function index()
    {
        $landlordId = Auth::id();

        // latest rooms for this landlord
        $latestRooms = Room::where('landlord_id', $landlordId)
            ->latest()
            ->take(5)
            ->get();

        // room counts
        $totalRooms = Room::where('landlord_id', $landlordId)->count();

        $draftRooms = Room::where('landlord_id', $landlordId)
            ->where('status', 'draft')
            ->count();

        $activeRooms = Room::where('landlord_id', $landlordId)
            ->where('status', 'active')
            ->where('available_slots', '>', 0)
            ->count();

        $inactiveRooms = Room::where('landlord_id', $landlordId)
            ->where(function ($q) {
                $q->where('status', 'inactive')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'active')
                         ->where('available_slots', '<=', 0);
                  });
            })
            ->count();

        // booking summary
        $totalBookings = Booking::where('landlord_id', $landlordId)->count();

        $pendingBookings = Booking::where('landlord_id', $landlordId)
            ->where('status', 'pending')
            ->count();

        $paymentSubmittedBookings = Booking::where('landlord_id', $landlordId)
            ->where('status', 'payment_submitted')
            ->count();

        $paidBookings = Booking::where('landlord_id', $landlordId)
            ->where('status', 'paid')
            ->count();

        $cancelRequestedBookings = Booking::where('landlord_id', $landlordId)
            ->where('status', 'cancel_requested')
            ->count();

        // unread chat messages for landlord
        $unreadMessages = Message::where('receiver_id', $landlordId)
            ->where('is_read', false)
            ->count();

        // unread notifications
        $unreadNotifications = auth()->user()?->unreadNotifications()->count() ?? 0;

        // potential monthly income from active rooms
        $potentialMonthlyIncome = Room::where('landlord_id', $landlordId)
            ->where('status', 'active')
            ->sum('price_monthly');

        // simple insights
        $highestRentRoom = Room::where('landlord_id', $landlordId)
            ->orderByDesc('price_monthly')
            ->first();

        $lowestRentRoom = Room::where('landlord_id', $landlordId)
            ->orderBy('price_monthly')
            ->first();

        return view('landlord.dashboard', compact(
            'latestRooms',
            'totalRooms',
            'activeRooms',
            'draftRooms',
            'inactiveRooms',
            'totalBookings',
            'pendingBookings',
            'paymentSubmittedBookings',
            'paidBookings',
            'cancelRequestedBookings',
            'unreadMessages',
            'unreadNotifications',
            'potentialMonthlyIncome',
            'highestRentRoom',
            'lowestRentRoom'
        ));
    }
}