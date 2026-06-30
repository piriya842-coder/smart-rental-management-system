<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DisputeController extends Controller
{
    public function studentCreate(Booking $booking)
    {
        abort_unless((int)$booking->student_id === (int)Auth::id(), 403);

        $booking->load(['room', 'landlord']);

        return view('student.disputes.create', compact('booking'));
    }

    public function studentStore(Request $request, Booking $booking)
    {
        abort_unless((int)$booking->student_id === (int)Auth::id(), 403);

        $data = $request->validate([
            'category'    => ['required', 'in:payment,booking,listing,behavior,other'],
            'priority'    => ['required', 'in:low,medium,high'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'evidence'    => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $path = null;
        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('disputes', 'public');
        }

        $dispute = Dispute::create([
            'code'         => $this->generateCode(),
            'category'     => $data['category'],
            'priority'     => $data['priority'],
            'status'       => 'open',
            'submitted_by' => Auth::id(),
            'student_id'   => $booking->student_id,
            'landlord_id'  => $booking->landlord_id,
            'booking_id'   => $booking->id,
            'room_id'      => $booking->room_id,
            'title'        => $data['title'],
            'description'  => $data['description'],
            'evidence_path'=> $path,
        ]);

        return redirect()
            ->route('student.bookings.show', $booking->id)
            ->with('success', 'Dispute submitted successfully. Ticket: ' . $dispute->code);
    }

    public function landlordCreate(Booking $booking)
    {
        abort_unless((int)$booking->landlord_id === (int)Auth::id(), 403);

        $booking->load(['room', 'student']);

        return view('landlord.disputes.create', compact('booking'));
    }

    public function landlordStore(Request $request, Booking $booking)
    {
        abort_unless((int)$booking->landlord_id === (int)Auth::id(), 403);

        $data = $request->validate([
            'category'    => ['required', 'in:payment,booking,listing,behavior,other'],
            'priority'    => ['required', 'in:low,medium,high'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'evidence'    => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $path = null;
        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('disputes', 'public');
        }

        $dispute = Dispute::create([
            'code'         => $this->generateCode(),
            'category'     => $data['category'],
            'priority'     => $data['priority'],
            'status'       => 'open',
            'submitted_by' => Auth::id(),
            'student_id'   => $booking->student_id,
            'landlord_id'  => $booking->landlord_id,
            'booking_id'   => $booking->id,
            'room_id'      => $booking->room_id,
            'title'        => $data['title'],
            'description'  => $data['description'],
            'evidence_path'=> $path,
        ]);

        return redirect()
            ->route('landlord.bookings.index')
            ->with('success', 'Dispute submitted successfully. Ticket: ' . $dispute->code);
    }

    private function generateCode(): string
    {
        $lastId = \App\Models\Dispute::max('id') ?? 0;
        return 'DSP-' . str_pad((string)($lastId + 1), 6, '0', STR_PAD_LEFT);
    }
}