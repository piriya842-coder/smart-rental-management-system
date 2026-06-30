@extends('layouts.student')

@section('title', 'Booking Details • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $choco   = '#4a2c2a';
  $cream   = '#fffafa';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';

  $status = strtolower((string)$booking->status);

  $deposit = (int)($booking->deposit_amount ?? 100);
  $rent = (float)($booking->monthly_rent ?? 0);
  $total = (float)($booking->total_due ?? ($deposit + $rent));

  $existingReview = $booking->review;
@endphp

<div class="max-w-6xl mx-auto">

  <div class="mb-4">
    <a href="{{ route('student.bookings.index') }}"
       class="font-semibold hover:opacity-80"
       style="color: {{ $choco }};">
      ← Back to My Bookings
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-semibold">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 font-semibold">
      {{ session('error') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 font-semibold">
      <div class="font-extrabold">Please fix these:</div>
      <ul class="mt-2 list-disc ml-5 text-sm">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- LEFT --}}
    <div class="lg:col-span-7 space-y-6">
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6">
        <div class="text-2xl font-extrabold" style="color: {{ $choco }};">Booking Details</div>

        <div class="mt-4 text-sm text-gray-700 space-y-2">
          <div><span class="font-extrabold">Room:</span> {{ $booking->room?->title ?? 'Room' }}</div>
          <div><span class="font-extrabold">Landlord:</span> {{ $booking->landlord?->name ?? 'Landlord' }}</div>
          <div>
            <span class="font-extrabold">Contract:</span>
            {{ optional($booking->contract_start_date)->format('d M Y') }}
            → {{ optional($booking->contract_end_date)->format('d M Y') }}
          </div>
          <div class="flex items-center gap-2">
            <span class="font-extrabold">Status:</span>
            <span class="inline-flex rounded-full px-4 py-2 text-xs font-extrabold text-white"
                  style="background: {{ $status === 'paid' ? '#1B9A59' : ($status === 'payment_submitted' ? $gold : ($status === 'cancel_requested' ? '#9B59B6' : '#444')) }};">
              {{ strtoupper($status) }}
            </span>
          </div>
        </div>
      </div>

      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6">
        <div class="text-lg font-extrabold" style="color: {{ $choco }};">Your Note to Landlord</div>
        <div class="mt-2 text-gray-700">
          {{ $booking->student_note ?: 'Nothing to tell' }}
        </div>
      </div>

      {{-- Request cancel/refund form (only after payment submitted/paid) --}}
      @if(in_array($status, ['payment_submitted', 'paid'], true))
        <div class="rounded-3xl border border-purple-200 bg-purple-50 shadow-sm p-6">
          <div class="text-lg font-extrabold text-purple-800">Request Cancel / Refund</div>
          <div class="mt-1 text-sm text-purple-800/80">
            Your request will be reviewed by landlord/admin. Record will not be deleted.
          </div>

          <form method="POST" action="{{ route('student.bookings.request_cancel', $booking->id) }}" class="mt-4 space-y-3"
                onsubmit="return confirm('Submit cancel/refund request?');">
            @csrf
            <textarea name="reason" rows="3" maxlength="500" required
                      placeholder="Reason (required) e.g. family emergency / change of plan / wrong booking"
                      class="w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]"></textarea>

            <button type="submit"
                    class="rounded-2xl px-6 py-3 text-sm font-extrabold text-white shadow-sm"
                    style="background: #9B59B6;">
              Submit Request
            </button>
          </form>
        </div>
      @endif

      {{-- If already requested --}}
      @if($status === 'cancel_requested')
        <div class="rounded-3xl border border-purple-200 bg-purple-50 shadow-sm p-6">
          <div class="text-lg font-extrabold text-purple-800">Cancel/Refund Request Sent</div>
          <div class="mt-2 text-sm text-purple-800/80">
            Waiting landlord verification.
          </div>
          <div class="mt-3 text-sm text-purple-900">
            <span class="font-extrabold">Reason:</span>
            {{ $booking->cancel_request_reason ?: '—' }}
          </div>
        </div>
      @endif

      {{-- Leave Review --}}
      @if($status === 'paid')
        <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6">
          <div class="text-lg font-extrabold" style="color: {{ $choco }};">Leave Review</div>
          <div class="mt-1 text-sm text-gray-600">
            Share your experience for this room and landlord.
          </div>

          @if($existingReview)
            <div class="mt-4 rounded-2xl border border-green-200 bg-green-50 p-4">
              <div class="font-extrabold text-green-800">You already submitted a review.</div>
              <div class="mt-2 text-sm text-gray-700">
                <span class="font-extrabold">Rating:</span> {{ $existingReview->rating }}/5
              </div>
              <div class="mt-2 text-sm text-gray-700">
                <span class="font-extrabold">Comment:</span>
                {{ $existingReview->comment ?: 'No comment provided.' }}
              </div>
            </div>
          @else
            <form method="POST" action="{{ route('student.reviews.store', $booking->id) }}" class="mt-4 space-y-4">
              @csrf

              <div>
                <label class="block text-sm font-extrabold mb-2" style="color: {{ $choco }};">Rating</label>
                <select name="rating"
                        class="w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]"
                        required>
                  <option value="">Select rating</option>
                  <option value="5">5 - Excellent</option>
                  <option value="4">4 - Good</option>
                  <option value="3">3 - Average</option>
                  <option value="2">2 - Poor</option>
                  <option value="1">1 - Very Poor</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-extrabold mb-2" style="color: {{ $choco }};">Comment</label>
                <textarea name="comment"
                          rows="4"
                          maxlength="1000"
                          placeholder="Write your review here..."
                          class="w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]"></textarea>
              </div>

              <button type="submit"
                      class="rounded-2xl px-6 py-3 text-sm font-extrabold text-white shadow-sm"
                      style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
                Submit Review
              </button>
            </form>
          @endif
        </div>
      @endif

    </div>

    {{-- RIGHT --}}
    <div class="lg:col-span-5">
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6 lg:sticky lg:top-6 space-y-5">

        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5 bg-white">
          <div class="text-lg font-extrabold" style="color: {{ $choco }};">Payment Summary</div>

          <div class="mt-3 space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-gray-700 font-semibold">Deposit</span>
              <span class="font-extrabold" style="color: {{ $choco }};">RM {{ number_format($deposit, 0) }}</span>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-gray-700 font-semibold">1 month rent</span>
              <span class="font-extrabold" style="color: {{ $choco }};">RM {{ number_format($rent, 0) }}</span>
            </div>

            <div class="pt-3 mt-3 border-t border-[rgba(201,42,42,.10)] flex items-center justify-between">
              <span class="font-extrabold text-gray-800">Total due now</span>
              <span class="font-extrabold" style="color: {{ $gold }};">RM {{ number_format($total, 0) }}</span>
            </div>

            <div class="text-xs text-gray-600 mt-2">
              Next step: upload QR/FPX receipt (manual verification).
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-3">
          {{-- Proceed payment --}}
          <a href="{{ route('student.payments.show', $booking->id) }}"
             class="rounded-xl px-4 py-3 text-sm font-extrabold text-white text-center shadow-sm transition"
             style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);"
             onmouseover="this.style.filter='brightness(0.95)'"
             onmouseout="this.style.filter='brightness(1)'">
            Proceed to Payment
          </a>

          {{-- Cancel only when pending --}}
          @if($status === 'pending')
            <form method="POST" action="{{ route('student.bookings.cancel', $booking->id) }}"
                  onsubmit="return confirm('Cancel this booking?');">
              @csrf
              <button type="submit"
                      class="w-full rounded-xl px-4 py-3 text-sm font-extrabold text-white shadow-sm transition"
                      style="background: {{ $choco }};"
                      onmouseover="this.style.filter='brightness(0.9)'"
                      onmouseout="this.style.filter='brightness(1)'">
                Cancel Booking
              </button>
            </form>
          @else
            <button type="button"
                    class="w-full rounded-xl px-4 py-3 text-sm font-extrabold text-white/70 shadow-sm cursor-not-allowed"
                    style="background: #d1d5db;" disabled>
              Cancel Disabled
            </button>
          @endif

          {{-- Open Dispute --}}
          <a href="{{ route('student.disputes.create', $booking->id) }}"
             class="rounded-xl px-4 py-3 text-sm font-extrabold text-white text-center shadow-sm transition"
             style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);"
             onmouseover="this.style.filter='brightness(0.95)'"
             onmouseout="this.style.filter='brightness(1)'">
            Report Issue / Open Dispute
          </a>
        </div>

        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4">
          <div class="text-sm font-extrabold text-red-800">Need admin help?</div>
          <div class="mt-1 text-xs text-red-700">
            Open a dispute if there is a serious issue related to payment, booking, listing, refund, or behaviour.
          </div>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection