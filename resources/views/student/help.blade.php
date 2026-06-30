{{-- resources/views/student/help.blade.php --}}
@extends('layouts.student')

@section('title', 'Help Center • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $cream   = '#fffafa';
  $choco   = '#4a2c2a';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';
@endphp

<div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm overflow-hidden">

  <!-- HEADER -->
  <div class="p-7 md:p-9 border-b border-[rgba(201,42,42,.08)] bg-gradient-to-r from-white via-white to-[#fffafa]">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
      <div>
        <div class="text-xs font-black tracking-wider text-black/50">SUPPORT</div>
        <h1 class="text-3xl md:text-4xl font-extrabold mt-1" style="color: {{ $choco }};">
          Student Help Center
        </h1>
        <p class="mt-2 text-black/60 leading-relaxed max-w-2xl">
          Get quick guidance on booking, payments, contracts, and notifications. If you still need help,
          you can contact the Smart Rental support team below.
        </p>
      </div>

      <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('student.rooms.index') }}"
           class="rounded-2xl px-6 py-3 font-extrabold text-white shadow-sm hover:brightness-95 transition text-center"
           style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
          Browse Rooms
        </a>

        <a href="{{ route('student.dashboard') }}"
           class="rounded-2xl px-6 py-3 font-extrabold border border-[rgba(201,42,42,.12)] hover:bg-[#fff5f5] transition text-center"
           style="color: {{ $choco }};">
          Back to Dashboard
        </a>
      </div>
    </div>
  </div>

  <!-- BODY -->
  <div class="p-7 md:p-9">

    <!-- QUICK ACTIONS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
      <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white p-6">
        <div class="text-3xl">📌</div>
        <div class="mt-2 font-extrabold text-lg" style="color: {{ $choco }};">Booking Help</div>
        <p class="mt-2 text-black/60">
          Learn how to book a room, view booking status, and handle cancellations.
        </p>
      </div>

      <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white p-6">
        <div class="text-3xl">💳</div>
        <div class="mt-2 font-extrabold text-lg" style="color: {{ $choco }};">Payment Help</div>
        <p class="mt-2 text-black/60">
          Upload proof of payment and check your transaction history.
        </p>
      </div>

      <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white p-6">
        <div class="text-3xl">📢</div>
        <div class="mt-2 font-extrabold text-lg" style="color: {{ $choco }};">Announcements</div>
        <p class="mt-2 text-black/60">
          Admin announcements appear as popup + notification badge.
        </p>
      </div>
    </div>

    <!-- FAQ -->
    <div class="mt-8 rounded-3xl border border-[rgba(201,42,42,.10)] bg-white overflow-hidden">
      <div class="p-6 md:p-7 border-b border-[rgba(201,42,42,.08)] bg-[#fffafa]">
        <div class="text-sm font-black tracking-wider text-black/50">FAQ</div>
        <div class="text-2xl font-extrabold mt-1" style="color: {{ $choco }};">Frequently Asked Questions</div>
      </div>

      <div class="p-6 md:p-7 space-y-4">

        <details class="group rounded-2xl border border-[rgba(201,42,42,.10)] bg-white p-5">
          <summary class="cursor-pointer flex items-center justify-between gap-3">
            <span class="font-extrabold" style="color: {{ $choco }};">How do I book a room?</span>
            <span class="text-black/40 group-open:rotate-180 transition">▾</span>
          </summary>
          <div class="mt-3 text-black/70 leading-relaxed">
            Go to <b>Rooms</b> → open a listing → click <b>Book</b>. Your booking will appear in
            <b>Booking History</b> with a status (pending/approved/rejected depending on your system flow).
          </div>
        </details>

        <details class="group rounded-2xl border border-[rgba(201,42,42,.10)] bg-white p-5">
          <summary class="cursor-pointer flex items-center justify-between gap-3">
            <span class="font-extrabold" style="color: {{ $choco }};">How do I upload payment proof?</span>
            <span class="text-black/40 group-open:rotate-180 transition">▾</span>
          </summary>
          <div class="mt-3 text-black/70 leading-relaxed">
            Open <b>Transaction History</b> → choose your booking/payment → upload proof (receipt screenshot).
            After admin/landlord verifies, your payment status will update.
          </div>
        </details>

        <details class="group rounded-2xl border border-[rgba(201,42,42,.10)] bg-white p-5">
          <summary class="cursor-pointer flex items-center justify-between gap-3">
            <span class="font-extrabold" style="color: {{ $choco }};">Where can I see announcements?</span>
            <span class="text-black/40 group-open:rotate-180 transition">▾</span>
          </summary>
          <div class="mt-3 text-black/70 leading-relaxed">
            New announcements show as a popup on the dashboard, and also inside the
            <b>Notifications</b> page. The bell icon shows an unread badge count (1,2,99+).
          </div>
        </details>

        <details class="group rounded-2xl border border-[rgba(201,42,42,.10)] bg-white p-5">
          <summary class="cursor-pointer flex items-center justify-between gap-3">
            <span class="font-extrabold" style="color: {{ $choco }};">What if I face a system issue?</span>
            <span class="text-black/40 group-open:rotate-180 transition">▾</span>
          </summary>
          <div class="mt-3 text-black/70 leading-relaxed">
            Contact support using the details below. Include your <b>Student ID</b> and a screenshot of the error.
          </div>
        </details>

      </div>
    </div>

    <!-- CONTACT -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-5">

      <div class="lg:col-span-2 rounded-3xl border border-[rgba(201,42,42,.10)] bg-white p-7">
        <div class="text-sm font-black tracking-wider text-black/50">CONTACT</div>
        <div class="text-2xl font-extrabold mt-1" style="color: {{ $choco }};">Need more help?</div>

        <p class="mt-3 text-black/60 leading-relaxed">
          If you have booking/payment issues or questions about listings, you can reach the Smart Rental support team.
          (For FYP demo, you can use dummy contact details.)
        </p>

        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] p-5">
            <div class="font-extrabold" style="color: {{ $choco }};">📧 Email</div>
            <div class="mt-1 text-black/70">support@smartrental.com</div>
          </div>

          <div class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] p-5">
            <div class="font-extrabold" style="color: {{ $choco }};">📞 Phone / WhatsApp</div>
            <div class="mt-1 text-black/70">+60 12-345 6789</div>
          </div>
        </div>

        <div class="mt-5 rounded-2xl border border-[rgba(201,42,42,.10)] bg-white p-5">
          <div class="font-extrabold" style="color: {{ $choco }};">🕘 Support Hours</div>
          <div class="mt-1 text-black/70">Mon – Fri, 9:00 AM – 5:00 PM</div>
          <div class="mt-1 text-black/50 text-sm">*For demo purposes, responses may be instant.</div>
        </div>
      </div>

      <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white p-7">
        <div class="text-sm font-black tracking-wider text-black/50">TIPS</div>
        <div class="text-2xl font-extrabold mt-1" style="color: {{ $choco }};">Before you contact</div>

        <ul class="mt-4 space-y-3 text-black/70">
          <li class="flex gap-2"><span>✅</span><span>Check your booking status in <b>Booking History</b>.</span></li>
          <li class="flex gap-2"><span>✅</span><span>Confirm your payment in <b>Transaction History</b>.</span></li>
          <li class="flex gap-2"><span>✅</span><span>Look at <b>Notifications</b> for announcements.</span></li>
          <li class="flex gap-2"><span>✅</span><span>Prepare screenshot + Student ID if error happens.</span></li>
        </ul>

        <div class="mt-6">
          <a href="{{ route('student.notifications.index') }}"
             class="block w-full rounded-2xl px-6 py-3 font-extrabold text-white text-center hover:brightness-95 transition"
             style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
            Open Notifications
          </a>
        </div>
      </div>

    </div>

  </div>
</div>
@endsection