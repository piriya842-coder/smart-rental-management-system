@extends('layouts.landlord')

@section('title', 'Profile • Landlord')
@section('page_title', 'Profile')
@section('page_subtitle', 'Manage your landlord profile and business information.')

@section('content')
@php
    $redMain  = '#B91C1C';
    $redSoft  = '#FDECEC';
    $redBorder = '#F3C6CB';
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    @if ($errors->any())
        <div class="rounded-2xl px-5 py-4 font-semibold shadow-sm"
             style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;">
            <div class="font-extrabold mb-2">Please fix these errors:</div>
            <ul class="list-disc ml-5 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('landlord.profile.update') }}">
        @csrf

        <div class="rounded-[30px] overflow-hidden shadow-sm"
             style="background:#FFFFFF; border:1px solid #E5E7EB;">

            <!-- HEADER -->
            <div class="px-6 py-5 border-b"
                 style="border-color:#F3C6CB;
                        background:
                        radial-gradient(circle at top right, rgba(185,28,28,.08), transparent 25%),
                        linear-gradient(135deg,#FFFFFF 0%,#FFF3F5 100%);">

                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl grid place-items-center text-xl"
                         style="background:#FFF1F2; color:#B91C1C; border:1px solid #F3C6CB;">
                        👤
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold" style="color:#2A0709;">Landlord Profile</div>
                        <div class="text-sm mt-1" style="color:#8A5B63;">
                            Important identity details are locked. Other information can be updated anytime.
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- LOCKED --}}
                <div>
                    <label class="block text-sm font-extrabold mb-2" style="color:#2A0709;">Account ID</label>
                    <div class="flex gap-2">
                        <input type="text"
                               value="{{ $profile->account_id }}"
                               readonly
                               class="w-full rounded-2xl px-4 py-3 bg-[#FFF7F8] font-semibold"
                               style="border:1px solid #F3C6CB;">
                        <button type="button"
                                onclick="navigator.clipboard.writeText('{{ $profile->account_id }}')"
                                class="rounded-2xl px-4 py-3 text-sm font-extrabold"
                                style="background:#FFF1F2; color:#B91C1C; border:1px solid #F3C6CB;">
                            Copy
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-extrabold mb-2" style="color:#2A0709;">Name</label>
                    <input type="text"
                           value="{{ $user->name }}"
                           readonly
                           class="w-full rounded-2xl px-4 py-3 bg-[#FFF7F8] font-semibold"
                           style="border:1px solid #F3C6CB;">
                </div>

                <div>
                    <label class="block text-sm font-extrabold mb-2" style="color:#2A0709;">Email</label>
                    <input type="email"
                           value="{{ $user->email }}"
                           readonly
                           class="w-full rounded-2xl px-4 py-3 bg-[#FFF7F8] font-semibold"
                           style="border:1px solid #F3C6CB;">
                </div>

                {{-- EDITABLE --}}
                @foreach([
                    'phone'=>'Contact Number',
                    'nric'=>'NRIC / Passport No.',
                    'company_name'=>'Company Name',
                    'business_registration_no'=>'Business Registration No.',
                    'postcode'=>'Postcode',
                    'city'=>'City',
                    'state'=>'State',
                    'country'=>'Country'
                ] as $field => $label)

                <div>
                    <label class="block text-sm font-extrabold mb-2" style="color:#2A0709;">{{ $label }}</label>
                    <input type="text" name="{{ $field }}" value="{{ old($field, $profile->$field) }}"
                           class="w-full rounded-2xl px-4 py-3 font-semibold"
                           style="border:1px solid #F3C6CB;">
                </div>

                @endforeach

                <div>
                    <label class="block text-sm font-extrabold mb-2" style="color:#2A0709;">Gender</label>
                    <select name="gender"
                            class="w-full rounded-2xl px-4 py-3 font-semibold"
                            style="border:1px solid #F3C6CB;">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $profile->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $profile->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $profile->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-extrabold mb-2" style="color:#2A0709;">Date of Birth</label>
                    <input type="date" name="date_of_birth"
                           value="{{ old('date_of_birth', optional($profile->date_of_birth)->format('Y-m-d')) }}"
                           class="w-full rounded-2xl px-4 py-3 font-semibold"
                           style="border:1px solid #F3C6CB;">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-extrabold mb-2" style="color:#2A0709;">Address Line 1</label>
                    <input type="text" name="address_line1" value="{{ old('address_line1', $profile->address_line1) }}"
                           class="w-full rounded-2xl px-4 py-3 font-semibold"
                           style="border:1px solid #F3C6CB;">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-extrabold mb-2" style="color:#2A0709;">Address Line 2</label>
                    <input type="text" name="address_line2" value="{{ old('address_line2', $profile->address_line2) }}"
                           class="w-full rounded-2xl px-4 py-3 font-semibold"
                           style="border:1px solid #F3C6CB;">
                </div>

            </div>

            <!-- BUTTONS -->
            <div class="px-6 pb-6 flex flex-col sm:flex-row gap-3">

                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center justify-center rounded-2xl px-6 py-3 font-extrabold text-sm"
                   style="background:#FFF5F6; color:#8F1721; border:1px solid #F3C6CB;">
                    Back
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl px-6 py-3 font-extrabold text-sm text-white"
                        style="background:linear-gradient(135deg,#B91C1C 0%,#DC2626 100%);
                               box-shadow:0 10px 22px rgba(185,28,28,.25);">
                    Save Changes
                </button>

            </div>

        </div>
    </form>
</div>
@endsection