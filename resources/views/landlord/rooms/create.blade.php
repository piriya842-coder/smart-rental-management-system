@extends('layouts.landlord')

@section('title', 'Add Room • Smart Rental')

@section('page_title', 'Add Room')
@section('page_subtitle', 'Save draft first. Publish when ready. A good cover photo increases trust.')

@section('top_actions')
    <a href="{{ route('landlord.rooms.index') }}"
       class="sr-outline rounded-2xl px-5 py-3 text-sm font-extrabold inline-flex items-center gap-2"
       style="background:#FFFFFF; color:#8F1721; border:1px solid #F1D4D8;">
        ← Back to My Rooms
    </a>
@endsection

@section('content')
    <div class="sr-card rounded-3xl p-6 md:p-8"
         style="background:linear-gradient(135deg,#FFFFFF 0%,#FFF7F8 100%); border:1px solid #F1D4D8; box-shadow:0 18px 40px rgba(127,16,27,.08);">

        <form id="roomCreateForm" method="POST" action="{{ route('landlord.rooms.store') }}" enctype="multipart/form-data" class="space-y-7">
            @csrf

            <input type="hidden" name="action" id="action" value="draft">

            @if ($errors->any())
                <div class="rounded-2xl p-4 border text-sm font-semibold"
                     style="border-color:#FCA5A5; background:#FFF1F2; color:#991B1B;">
                    <div class="font-extrabold mb-2">Please fix these errors:</div>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- BASIC -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="font-extrabold" style="color:#2A0709;">Title</label>
                    <input name="title" value="{{ old('title') }}"
                           class="sr-input mt-2"
                           style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;"
                           placeholder="e.g. Single Room near MSU Gate">
                </div>

                <div>
                    <label class="font-extrabold" style="color:#2A0709;">Price / Month (RM)</label>
                    <input name="price_monthly" value="{{ old('price_monthly') }}"
                           class="sr-input mt-2"
                           style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;"
                           placeholder="550">
                </div>

                <div>
                    <label class="font-extrabold" style="color:#2A0709;">Distance (km)</label>
                    <input name="distance_km" value="{{ old('distance_km') }}"
                           class="sr-input mt-2"
                           style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;"
                           placeholder="1.2">
                </div>

                <div>
                    <label class="font-extrabold" style="color:#2A0709;">Room Type</label>
                    <select name="room_type" id="room_type" class="sr-select mt-2"
                            style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
                        @foreach(['single'=>'Single','shared'=>'Shared','master'=>'Master','studio'=>'Studio'] as $k=>$v)
                            <option value="{{ $k }}" {{ old('room_type', 'single')===$k ? 'selected' : '' }}>
                                {{ $v }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="capacity_wrap" style="display:none;">
                    <label class="font-extrabold" style="color:#2A0709;">Capacity (persons) — Shared only</label>
                    <input name="capacity" id="capacity"
                           value="{{ old('capacity', 4) }}"
                           type="number" min="2" max="10"
                           class="sr-input mt-2"
                           style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;"
                           placeholder="e.g. 4">
                    <div class="text-xs sr-muted mt-2" style="color:#8A5B63;">
                        Example: if capacity is 4, available slots will start at 4 and decrease when students book.
                    </div>
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div>
                <label class="font-extrabold" style="color:#2A0709;">Description</label>
                <textarea name="description" rows="4"
                          class="sr-textarea mt-2"
                          style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;"
                          placeholder="Fully furnished, walking distance to MSU, includes WiFi & aircond...">{{ old('description') }}</textarea>
            </div>

            <!-- LOCATION -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div class="md:col-span-2">
                    <label class="font-extrabold" style="color:#2A0709;">Address</label>
                    <input name="address" value="{{ old('address') }}"
                           class="sr-input mt-2"
                           style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;"
                           placeholder="Taman Sri Mewah">
                </div>

                <div>
                    <label class="font-extrabold" style="color:#2A0709;">City</label>
                    <input name="city" value="{{ old('city') }}"
                           class="sr-input mt-2"
                           style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;"
                           placeholder="Shah Alam">
                </div>

                <div>
                    <label class="font-extrabold" style="color:#2A0709;">State</label>
                    <input name="state" value="{{ old('state') }}"
                           class="sr-input mt-2"
                           style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;"
                           placeholder="Selangor">
                </div>

                <div>
                    <label class="font-extrabold" style="color:#2A0709;">Postcode</label>
                    <input name="postcode" value="{{ old('postcode') }}"
                           class="sr-input mt-2"
                           style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;"
                           placeholder="40000">
                </div>

                <div>
                    <label class="font-extrabold" style="color:#2A0709;">Gender Preference</label>
                    <select name="gender_preference" class="sr-select mt-2"
                            style="background:#FFFFFF; border:1px solid #F1D4D8; color:#2A0709;">
                        @foreach(['any'=>'Any','male'=>'Male','female'=>'Female'] as $k=>$v)
                            <option value="{{ $k }}" {{ old('gender_preference','any')===$k ? 'selected' : '' }}>
                                {{ $v }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- FACILITIES -->
            <div>
                <label class="font-extrabold" style="color:#2A0709;">Facilities</label>
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        $opts = ['WiFi','Air Conditioning','Laundry','Security','Kitchen','Parking','Water Heater','Study Area'];
                        $oldFac = old('facilities', []);
                        if (!is_array($oldFac)) $oldFac = [];
                    @endphp

                    @foreach($opts as $f)
                        <label class="sr-pill rounded-2xl px-3 py-2 text-sm font-extrabold inline-flex items-center gap-2 cursor-pointer"
                               style="background:#FFFFFF; color:#8F1721; border:1px solid #F1D4D8;">
                            <input type="checkbox" name="facilities[]" value="{{ $f }}" class="sr-check"
                                   {{ in_array($f, $oldFac) ? 'checked' : '' }}>
                            {{ $f }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- AVAILABILITY + COVER -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="sr-pill rounded-3xl p-5"
                     style="background:#FFFFFF; border:1px solid #F1D4D8;">
                    <label class="font-extrabold" style="color:#2A0709;">Availability</label>
                    <div class="mt-3 flex items-start gap-3">
                        <input type="checkbox" name="is_available" value="1" class="sr-check mt-1"
                               {{ old('is_available') ? 'checked' : '' }}>
                        <div>
                            <div class="font-semibold" style="color:#8F1721;">Available now</div>
                            <div class="text-sm sr-muted" style="color:#8A5B63;">Students can request booking if listing is Active.</div>
                        </div>
                    </div>
                </div>

                <div class="sr-pill rounded-3xl p-5"
                     style="background:#FFFFFF; border:1px solid #F1D4D8;">
                    <label class="font-extrabold" style="color:#2A0709;">Cover Image</label>
                    <div class="mt-3">
                        <input type="file" name="cover_image" accept="image/*"
                               class="block w-full text-sm"
                               style="color:#8A5B63;">
                    </div>
                    <div class="text-xs sr-muted mt-2" style="color:#8A5B63;">
                        JPG/PNG recommended. This photo appears on dashboard + listing.
                    </div>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="flex flex-wrap gap-3 pt-1">
                <button type="button"
                        class="sr-outline rounded-2xl px-6 py-3 font-extrabold"
                        style="background:#FFFFFF; color:#8F1721; border:1px solid #F1D4D8;"
                        onclick="document.getElementById('action').value='draft'; document.getElementById('roomCreateForm').submit();">
                    Save Draft
                </button>

                <button type="button"
                        class="sr-btn rounded-2xl px-6 py-3 font-extrabold"
                        style="background:linear-gradient(135deg,#B91C1C 0%,#DC2626 100%); color:#FFFFFF; border:1px solid #C81E2A;"
                        onclick="document.getElementById('action').value='publish'; document.getElementById('roomCreateForm').submit();">
                    Publish
                </button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const type = document.getElementById('room_type');
            const wrap = document.getElementById('capacity_wrap');
            const cap  = document.getElementById('capacity');

            function toggleCapacity() {
                const isShared = (type.value || '').toLowerCase() === 'shared';
                wrap.style.display = isShared ? 'block' : 'none';

                if (!isShared) {
                    if (cap) cap.value = 1;
                } else {
                    if (cap && (!cap.value || parseInt(cap.value, 10) < 2)) cap.value = 4;
                }
            }

            toggleCapacity();
            type.addEventListener('change', toggleCapacity);
        })();
    </script>
@endsection