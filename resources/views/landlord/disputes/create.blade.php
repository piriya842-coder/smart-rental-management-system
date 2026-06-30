@extends('layouts.landlord')

@section('title', 'Open Dispute • Landlord')
@section('page_title', 'Open Dispute')
@section('page_subtitle', 'Submit a formal dispute for admin review.')

@section('content')
    @if ($errors->any())
        <div class="mb-4 rounded-2xl px-4 py-3 text-sm"
             style="background:#FEF2F2; border:1px solid #FECACA; color:#991B1B;">
            <b>Fix these:</b>
            <ul class="list-disc ml-5 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-4xl">
        <div class="sr-card rounded-3xl p-6 md:p-8">
            <div class="text-2xl font-extrabold">Open Dispute</div>
            <p class="sr-muted mt-2">Submit a serious issue related to this booking. Admin will review the case.</p>

            <div class="mt-5 rounded-2xl p-4" style="background:#F9FAFB; border:1px solid #E5E7EB;">
                <div class="font-extrabold">{{ $booking->room?->title ?? 'Room' }}</div>
                <div class="mt-1 text-sm sr-muted">Student: {{ $booking->student?->name ?? 'Student' }}</div>
                <div class="mt-1 text-sm sr-muted">Booking ID: #{{ $booking->id }}</div>
            </div>

            <form method="POST"
                  action="{{ route('landlord.disputes.store', $booking->id) }}"
                  enctype="multipart/form-data"
                  class="mt-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-extrabold mb-2">Category</label>
                        <select name="category" class="sr-select" required>
                            <option value="">Select category</option>
                            <option value="payment" {{ old('category') === 'payment' ? 'selected' : '' }}>Payment</option>
                            <option value="booking" {{ old('category') === 'booking' ? 'selected' : '' }}>Booking</option>
                            <option value="listing" {{ old('category') === 'listing' ? 'selected' : '' }}>Listing</option>
                            <option value="behavior" {{ old('category') === 'behavior' ? 'selected' : '' }}>Behaviour</option>
                            <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-extrabold mb-2">Priority</label>
                        <select name="priority" class="sr-select" required>
                            <option value="">Select priority</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-extrabold mb-2">Title</label>
                    <input type="text"
                           name="title"
                           value="{{ old('title') }}"
                           maxlength="255"
                           required
                           class="sr-input"
                           placeholder="Example: Student submitted invalid payment proof">
                </div>

                <div>
                    <label class="block text-sm font-extrabold mb-2">Description</label>
                    <textarea name="description"
                              rows="6"
                              maxlength="5000"
                              required
                              class="sr-textarea"
                              placeholder="Explain clearly what happened...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-extrabold mb-2">Evidence (Optional)</label>
                    <input type="file"
                           name="evidence"
                           accept=".jpg,.jpeg,.png,.pdf"
                           class="sr-input">
                    <div class="text-xs sr-muted mt-1">Accepted files: JPG, JPEG, PNG, PDF (max 5MB)</div>
                </div>

                <button type="submit" class="rounded-2xl px-6 py-3 font-extrabold text-white"
                        style="background:#DC2626;">
                    Submit Dispute
                </button>
            </form>
        </div>
    </div>
@endsection