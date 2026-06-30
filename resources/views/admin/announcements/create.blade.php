@extends('layouts.admin')

@section('title','Create Announcement')

@section('content')
@php
    $primary = '#7F1D1D';
    $primarySoft = '#9A5E5E';
    $accent = '#DC2626';
    $accentSoft = '#B91C1C';
@endphp

<div class="max-w-4xl px-6 py-2">
    <div class="rounded-3xl border border-[#F3CACA] shadow-lg overflow-hidden"
         style="background: linear-gradient(135deg, #FFF7F7, #FDEEEE);">

        <!-- Header -->
        <div class="px-6 py-7 md:px-8 md:py-8 border-b border-[#F3CACA]">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div>
                    <div class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-black tracking-[0.18em] uppercase border"
                         style="background: rgba(220,38,38,.08); border-color: rgba(220,38,38,.20); color: {{ $accent }};">
                        Admin Notice
                    </div>

                    <h1 class="mt-3 text-3xl md:text-4xl font-extrabold tracking-tight text-[#7F1D1D]">
                        Create Announcement
                    </h1>

                    <p class="mt-2 text-sm md:text-base font-semibold text-[#9A5E5E] max-w-2xl">
                        Publish an announcement to inform users about updates, reminders, or important notices.
                    </p>
                </div>

                <div class="rounded-2xl border border-[#F3CACA] bg-white px-4 py-3 text-sm font-extrabold text-[#7F1D1D] shadow-sm">
                    📣 Admin Broadcast
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="p-6 md:p-8">
            <form method="POST"
                  action="{{ route('admin.announcements.store') }}"
                  class="space-y-6">

                @csrf

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wide text-[#B98B8B] mb-2">
                        Title
                    </label>
                    <input type="text" name="title"
                           class="w-full border border-[#F3CACA] bg-white rounded-2xl px-4 py-3 font-semibold text-[#4B5563] shadow-sm focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]"
                           placeholder="Enter announcement title..."
                           required>
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wide text-[#B98B8B] mb-2">
                        Message
                    </label>
                    <textarea name="message"
                              rows="6"
                              class="w-full border border-[#F3CACA] bg-white rounded-[24px] px-4 py-4 font-semibold text-[#4B5563] shadow-sm focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]"
                              placeholder="Write the announcement message here..."
                              required></textarea>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <button class="rounded-2xl px-6 py-3 font-extrabold text-white shadow-md transition hover:scale-[1.01]"
                            style="background: linear-gradient(135deg, {{ $accent }}, {{ $accentSoft }});">
                        Publish
                    </button>

                    <div class="text-sm font-semibold text-[#B98B8B]">
                        This will be visible to users in the system.
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection