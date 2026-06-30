@extends('layouts.landlord')

@section('title', 'Edit Room • Smart Rental')

@section('page_title', 'Edit Room')
@section('page_subtitle', 'Update details, capacity, and cover image.')

@section('top_actions')
    <a href="{{ route('landlord.rooms.index') }}"
       class="sr-outline rounded-2xl px-5 py-3 text-sm font-extrabold inline-flex items-center gap-2">
        ← Back
    </a>
@endsection

@section('content')
<div class="max-w-5xl">
    <form method="POST" action="{{ route('landlord.rooms.update', $room) }}" enctype="multipart/form-data"
          class="sr-card rounded-3xl p-6 md:p-8"
          style="background:linear-gradient(135deg,#FFFFFF 0%,#FFF7F8 100%); border:1px solid #F1D4D8; box-shadow:0 18px 40px rgba(127,16,27,.08);">
        @csrf
        @method('PUT')

        <!-- BASIC -->
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="text-sm font-extrabold" style="color:#2A0709;">Title</label>
                <input name="title" value="{{ old('title', $room->title) }}"
                       class="sr-input mt-2" required
                       style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
            </div>

            <div>
                <label class="text-sm font-extrabold" style="color:#2A0709;">Price / Month (RM)</label>
                <input type="number" step="0.01" name="price_monthly" value="{{ old('price_monthly', $room->price_monthly) }}"
                       class="sr-input mt-2" required
                       style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
            </div>

            <div class="md:col-span-2">
                <label class="text-sm font-extrabold" style="color:#2A0709;">Description</label>
                <textarea name="description" rows="4"
                          class="sr-textarea mt-2"
                          style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">{{ old('description', $room->description) }}</textarea>
            </div>
        </div>

        <!-- LOCATION -->
        <div class="mt-8 grid md:grid-cols-4 gap-6">
            <div class="md:col-span-2">
                <label class="text-sm font-extrabold" style="color:#2A0709;">Address</label>
                <input name="address" value="{{ old('address', $room->address) }}"
                       class="sr-input mt-2"
                       style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
            </div>

            <div>
                <label class="text-sm font-extrabold" style="color:#2A0709;">City</label>
                <input name="city" value="{{ old('city', $room->city) }}"
                       class="sr-input mt-2" required
                       style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
            </div>

            <div>
                <label class="text-sm font-extrabold" style="color:#2A0709;">State</label>
                <input name="state" value="{{ old('state', $room->state) }}"
                       class="sr-input mt-2" required
                       style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
            </div>

            <div>
                <label class="text-sm font-extrabold" style="color:#2A0709;">Postcode</label>
                <input name="postcode" value="{{ old('postcode', $room->postcode) }}"
                       class="sr-input mt-2" required
                       style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
            </div>

            <div>
                <label class="text-sm font-extrabold" style="color:#2A0709;">Distance to MSU (KM)</label>
                <input type="number" step="0.01" name="distance_km" value="{{ old('distance_km', $room->distance_km) }}"
                       class="sr-input mt-2"
                       style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
            </div>
        </div>

        <!-- TYPE / CAPACITY / GENDER -->
        <div class="mt-8 grid md:grid-cols-3 gap-6 items-end">
            <div>
                <label class="text-sm font-extrabold" style="color:#2A0709;">Room Type</label>
                <select name="room_type" id="room_type" class="sr-select mt-2" required
                        style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
                    @php $rt = old('room_type', $room->room_type); @endphp
                    <option value="single" {{ $rt==='single'?'selected':'' }}>Single</option>
                    <option value="shared" {{ $rt==='shared'?'selected':'' }}>Shared</option>
                    <option value="master" {{ $rt==='master'?'selected':'' }}>Master</option>
                    <option value="studio" {{ $rt==='studio'?'selected':'' }}>Studio</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-extrabold" style="color:#2A0709;">Capacity (persons)</label>
                <input type="number" name="capacity" id="capacity"
                       value="{{ old('capacity', $room->capacity) }}"
                       class="sr-input mt-2"
                       min="1" max="6"
                       style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
                <div class="mt-2 text-xs sr-muted" style="color:#8A5B63;">
                    Current available slots: <b style="color:#B91C1C;">{{ $room->available_slots }}</b>
                </div>
            </div>

            <div>
                <label class="text-sm font-extrabold" style="color:#2A0709;">Gender Preference</label>
                @php $gp = old('gender_preference', $room->gender_preference); @endphp
                <select name="gender_preference" class="sr-select mt-2" required
                        style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
                    <option value="any" {{ $gp==='any'?'selected':'' }}>Any</option>
                    <option value="male" {{ $gp==='male'?'selected':'' }}>Male</option>
                    <option value="female" {{ $gp==='female'?'selected':'' }}>Female</option>
                </select>
            </div>
        </div>

        <!-- FACILITIES -->
        <div class="mt-8">
            <label class="text-sm font-extrabold" style="color:#2A0709;">Facilities</label>
            @php
                $all = ['WiFi','Parking','Air Conditioning','Laundry','Kitchen','Water Heater','Security','Study Area'];
                $selected = old('facilities', $room->facilities ?? []);
                if (is_string($selected)) {
                    $decoded = json_decode($selected, true);
                    $selected = is_array($decoded) ? $decoded : [];
                }
                if (!is_array($selected)) $selected = [];
            @endphp

            <div class="mt-3 grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($all as $f)
                    <label class="sr-pill flex items-center gap-3 rounded-2xl px-4 py-3 font-semibold cursor-pointer"
                           style="background:#FFFFFF; color:#8F1721; border:1px solid #F1D4D8; box-shadow:0 8px 18px rgba(127,16,27,.05);">
                        <input type="checkbox" name="facilities[]" value="{{ $f }}" class="sr-check"
                               {{ in_array($f, $selected) ? 'checked' : '' }}>
                        {{ $f }}
                    </label>
                @endforeach
            </div>
        </div>

        <!-- COVER + AVAILABILITY -->
        <div class="mt-8 grid md:grid-cols-2 gap-6 items-start">
            <div class="sr-pill rounded-3xl p-5"
                 style="background:linear-gradient(135deg,#FFFFFF 0%,#FFF5F6 100%); border:1px solid #F1D4D8; box-shadow:0 10px 24px rgba(127,16,27,.06);">
                <label class="text-sm font-extrabold" style="color:#2A0709;">Cover Image</label>

                @if($room->cover_image)
                    <div class="mt-3 flex items-center gap-4">
                        <img src="{{ $room->coverUrl() }}" class="h-20 w-28 rounded-2xl object-cover border"
                             style="border-color:#F1D4D8;">
                        <label class="text-sm font-bold flex items-center gap-2 sr-muted" style="color:#8A5B63;">
                            <input type="checkbox" name="remove_cover" value="1" class="sr-check">
                            Remove current image
                        </label>
                    </div>
                @endif

                <input type="file" name="cover_image" accept="image/*"
                       class="mt-4 block w-full text-sm"
                       style="color:#8A5B63;"
                       file:mr-4 file:rounded-2xl file:border-0 file:px-4 file:py-2
                       file:font-extrabold file:bg-[linear-gradient(135deg,#B91C1C_0%,#DC2626_100%)] file:text-white>
            </div>

            <div class="sr-pill rounded-3xl p-5"
                 style="background:linear-gradient(135deg,#FFFFFF 0%,#FFF5F6 100%); border:1px solid #F1D4D8; box-shadow:0 10px 24px rgba(127,16,27,.06);">
                <label class="text-sm font-extrabold" style="color:#2A0709;">Availability</label>
                <div class="mt-3 flex items-start gap-3">
                    <input type="checkbox" name="is_available" value="1" class="sr-check mt-1"
                           {{ ($room->available_slots > 0) ? 'checked' : '' }}>
                    <div>
                        <div class="font-semibold" style="color:#8F1721;">Available now</div>
                        <div class="text-sm sr-muted" style="color:#8A5B63;">Auto based on slots (if slots = 0, should be not available).</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BUTTONS -->
        <div class="mt-8 flex gap-3">
            <button type="submit" class="sr-btn rounded-2xl px-7 py-3 font-extrabold"
                    style="background:linear-gradient(135deg,#B91C1C 0%,#DC2626 100%); color:#FFFFFF; border:1px solid #C81E2A; box-shadow:0 12px 24px rgba(185,28,28,.22);">
                Save Changes
            </button>

            <a href="{{ route('landlord.rooms.index') }}"
               class="sr-outline rounded-2xl px-6 py-3 font-extrabold"
               style="background:#FFFFFF; color:#8F1721; border:1px solid #F1D4D8; box-shadow:0 8px 18px rgba(127,16,27,.06);">
                Back
            </a>
        </div>
    </form>
</div>

<script>
    function applyCapacityRule() {
        const type = document.getElementById('room_type').value;
        const cap = document.getElementById('capacity');

        if (type === 'single') {
            cap.value = 1;
            cap.min = 1; cap.max = 1;
            cap.readOnly = true;
        } else if (type === 'shared') {
            cap.readOnly = false;
            cap.min = 2; cap.max = 6;
            if (+cap.value < 2) cap.value = 2;
        } else {
            cap.readOnly = false;
            cap.min = 1; cap.max = 2;
            if (+cap.value > 2) cap.value = 2;
        }
    }

    document.getElementById('room_type').addEventListener('change', applyCapacityRule);
    applyCapacityRule();
</script>
@endsection