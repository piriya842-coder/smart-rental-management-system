<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        abort_unless((int)$booking->student_id === (int)Auth::id(), 403);

        if (strtolower((string)$booking->status) !== 'paid') {
            return back()->with('error', 'You can only leave a review for a paid booking.');
        }

        if ($booking->review()->exists()) {
            return back()->with('error', 'You have already submitted a review for this booking.');
        }

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'booking_id'  => $booking->id,
            'student_id'  => $booking->student_id,
            'landlord_id' => $booking->landlord_id,
            'room_id'     => $booking->room_id,
            'rating'      => $data['rating'],
            'comment'     => $data['comment'] ?? null,
        ]);

        return back()->with('success', 'Review submitted successfully.');
    }
}