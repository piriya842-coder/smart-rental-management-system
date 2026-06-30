<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class LandlordBookingController extends Controller
{
    public function index()
    {
        $landlordId = Auth::id();

        $bookings = Booking::with([
                'student',
                'room',
                'payments' => function ($q) {
                    $q->latest();
                },
            ])
            ->where('landlord_id', $landlordId)
            ->latest()
            ->get();

        $pendingBookings = $bookings->where('status', 'pending')->values();
        $paymentSubmittedBookings = $bookings->where('status', 'payment_submitted')->values();
        $paidBookings = $bookings->where('status', 'paid')->values();
        $cancelRequestedBookings = $bookings->where('status', 'cancel_requested')->values();
        $cancelledBookings = $bookings->where('status', 'cancelled')->values();

        $totalBookings = $bookings->count();
        $pendingCount = $pendingBookings->count();
        $paymentSubmittedCount = $paymentSubmittedBookings->count();
        $paidCount = $paidBookings->count();
        $cancelRequestedCount = $cancelRequestedBookings->count();
        $cancelledCount = $cancelledBookings->count();

        return view('landlord.bookings.index', compact(
            'bookings',
            'pendingBookings',
            'paymentSubmittedBookings',
            'paidBookings',
            'cancelRequestedBookings',
            'cancelledBookings',
            'totalBookings',
            'pendingCount',
            'paymentSubmittedCount',
            'paidCount',
            'cancelRequestedCount',
            'cancelledCount'
        ));
    }
}