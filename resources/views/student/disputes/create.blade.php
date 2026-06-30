@extends('layouts.student')

@section('title', 'Open Dispute • Smart Rental')

@section('content')
@php
  $gold  = '#B08401';
  $choco = '#683B2B';
@endphp

<div class="max-w-4xl mx-auto">

  <div class="mb-4">
    <a href="{{ route('student.bookings.show', $booking->id) }}"
       class="font-semibold hover:opacity-80"
       style="color: {{ $choco }};">
      ← Back to Booking Details
    </a>
  </div>

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

  <div class="rounded-3xl border border-black/5 bg-white/90 shadow-sm p-6 md:p-8">
    <div class="text-2xl font-extrabold" style="color: {{ $choco }};">Open Dispute</div>
    <div class="mt-2 text-sm text-gray-600">
      Submit a formal issue related to this booking. Admin will review your case.
    </div>

    <div class="mt-5 rounded-2xl border border-black/10 bg-[#FAF6F2] p-4">
      <div class="font-extrabold text-gray-900">{{ $booking->room?->title ?? 'Room' }}</div>
      <div class="mt-1 text-sm text-gray-700">Landlord: {{ $booking->landlord?->name ?? 'Landlord' }}</div>
      <div class="mt-1 text-sm text-gray-700">Booking ID: #{{ $booking->id }}</div>
    </div>

    <form method="POST"
          action="{{ route('student.disputes.store', $booking->id) }}"
          enctype="multipart/form-data"
          class="mt-6 space-y-4">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-extrabold mb-2" style="color: {{ $choco }};">Category</label>
          <select name="category"
                  class="w-full rounded-2xl border border-black/10 bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/10"
                  required>
            <option value="">Select category</option>
            <option value="payment" {{ old('category') === 'payment' ? 'selected' : '' }}>Payment</option>
            <option value="booking" {{ old('category') === 'booking' ? 'selected' : '' }}>Booking</option>
            <option value="listing" {{ old('category') === 'listing' ? 'selected' : '' }}>Listing</option>
            <option value="behavior" {{ old('category') === 'behavior' ? 'selected' : '' }}>Behaviour</option>
            <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-extrabold mb-2" style="color: {{ $choco }};">Priority</label>
          <select name="priority"
                  class="w-full rounded-2xl border border-black/10 bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/10"
                  required>
            <option value="">Select priority</option>
            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
            <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-sm font-extrabold mb-2" style="color: {{ $choco }};">Title</label>
        <input type="text"
               name="title"
               value="{{ old('title') }}"
               maxlength="255"
               required
               placeholder="Example: Incorrect payment verification"
               class="w-full rounded-2xl border border-black/10 bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/10">
      </div>

      <div>
        <label class="block text-sm font-extrabold mb-2" style="color: {{ $choco }};">Description</label>
        <textarea name="description"
                  rows="6"
                  maxlength="5000"
                  required
                  placeholder="Explain clearly what happened..."
                  class="w-full rounded-2xl border border-black/10 bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/10">{{ old('description') }}</textarea>
      </div>

      <div>
        <label class="block text-sm font-extrabold mb-2" style="color: {{ $choco }};">Evidence (Optional)</label>
        <input type="file"
               name="evidence"
               accept=".jpg,.jpeg,.png,.pdf"
               class="w-full rounded-2xl border border-black/10 bg-white px-4 py-3">
        <div class="mt-1 text-xs text-gray-500">Accepted files: JPG, JPEG, PNG, PDF (max 5MB)</div>
      </div>

      <button type="submit"
              class="rounded-2xl px-6 py-3 text-sm font-extrabold text-white shadow-sm"
              style="background: #DC2626;">
        Submit Dispute
      </button>
    </form>
  </div>
</div>
@endsection