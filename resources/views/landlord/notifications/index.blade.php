@extends('layouts.landlord')

@section('title', 'Notifications • Landlord')
@section('page_title', 'Notifications')
@section('page_subtitle', 'Announcements, booking updates, and payment updates.')

@section('content')
@php
    $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y, h:i A') : '';
@endphp

<div class="rounded-[28px] overflow-hidden shadow-sm"
     style="background:#FFFFFF; border:1px solid #E5E7EB;">

    <div class="p-6 md:p-8 border-b" style="border-color:#E5E7EB; background:#F9FAFB;">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <div class="text-xs font-black tracking-wider" style="color:#9CA3AF;">INBOX</div>
                <h1 class="text-3xl md:text-4xl font-extrabold mt-1" style="color:#111827;">Notifications</h1>
                <p class="mt-2" style="color:#6B7280;">
                    Announcements, booking updates, and payment updates will appear here.
                </p>

                <div class="mt-3 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold"
                     style="background:#F3F4F6; color:#4B5563;">
                    Unread:
                    <span class="px-2 py-0.5 rounded-full text-white" style="background:#1D4ED8;">
                        {{ $unreadCount ?? 0 }}
                    </span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <form method="POST" action="{{ route('landlord.notifications.read_all') }}">
                    @csrf
                    <button class="rounded-2xl px-5 py-3 font-extrabold transition"
                            style="background:#FFFFFF; color:#111827; border:1px solid #D1D5DB;">
                        Mark all as read
                    </button>
                </form>

                <form method="POST" action="{{ route('landlord.notifications.clear_all') }}"
                      onsubmit="return confirm('Delete ALL notifications?')">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-2xl px-5 py-3 font-extrabold text-white transition hover:brightness-95"
                            style="background:#111827;">
                        Clear all
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="p-6 md:p-8">
        @if(session('success'))
            <div class="mb-5 rounded-2xl px-5 py-4 font-semibold"
                 style="background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(($notifications ?? collect())->count() === 0)
            <div class="rounded-[28px] p-8 text-center"
                 style="background:#F9FAFB; border:1px solid #E5E7EB;">
                <div class="text-5xl">🔔</div>
                <div class="mt-3 text-xl font-extrabold" style="color:#111827;">No notifications yet</div>
                <div class="mt-2" style="color:#6B7280;">
                    Announcements, bookings, and payment updates will appear here.
                </div>
            </div>
        @else
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
                <div class="flex flex-wrap items-center gap-3">
                    <label class="inline-flex items-center gap-2 font-bold" style="color:#4B5563;">
                        <input id="selectAll" type="checkbox" class="h-5 w-5 rounded border-gray-300">
                        Select all
                    </label>

                    <button type="button" id="btnDeleteSelected"
                            class="rounded-2xl px-5 py-3 font-extrabold text-white transition hover:brightness-95"
                            style="background:#111827;">
                        Delete selected
                    </button>

                    <button type="button" id="btnMarkReadSelected"
                            class="rounded-2xl px-5 py-3 font-extrabold transition"
                            style="background:#FFFFFF; color:#111827; border:1px solid #D1D5DB;">
                        Mark selected as read
                    </button>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($notifications as $n)
                    @php
                        $data = (array)($n->data ?? []);
                        $title = $data['title'] ?? 'Notification';
                        $msg = $data['message'] ?? '';
                        $kind = $data['type'] ?? ($data['kind'] ?? 'system');
                        $isUnread = is_null($n->read_at);

                        $icon = $kind === 'announcement'
                            ? '📢'
                            : ($kind === 'booking'
                                ? '📌'
                                : ($kind === 'payment'
                                    ? '💳'
                                    : '🔔'));
                    @endphp

                    <div class="rounded-[24px] overflow-hidden"
                         style="background:#FFFFFF; border:1px solid #E5E7EB;">
                        <div class="p-5 md:p-6 flex items-start gap-4">
                            <div class="pt-1">
                                <input type="checkbox" value="{{ $n->id }}"
                                       class="rowCheck h-5 w-5 rounded border-gray-300">
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <div class="text-xl">{{ $icon }}</div>

                                            <div class="font-extrabold truncate" style="color:#111827;">
                                                {{ $title }}
                                            </div>

                                            @if($isUnread)
                                                <span class="inline-flex h-2.5 w-2.5 rounded-full" style="background:#1D4ED8;"></span>
                                            @endif
                                        </div>

                                        <div class="mt-1 text-xs font-semibold" style="color:#9CA3AF;">
                                            {{ $fmtDate($n->created_at) }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        @if($isUnread)
                                            <form method="POST" action="{{ route('landlord.notifications.read') }}">
                                                @csrf
                                                <input type="hidden" name="ids[]" value="{{ $n->id }}">
                                                <button class="rounded-xl px-4 py-2 text-xs font-extrabold transition"
                                                        style="background:#FFFFFF; color:#111827; border:1px solid #D1D5DB;">
                                                    Mark read
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('landlord.notifications.destroy', $n->id) }}"
                                              onsubmit="return confirm('Delete this notification?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-xl px-4 py-2 text-xs font-extrabold text-white transition hover:brightness-95"
                                                    style="background:#1D4ED8;">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if($msg)
                                    <div class="mt-3 leading-relaxed" style="color:#4B5563;">
                                        {{ $msg }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<script>
(function () {
    const selectAll = document.getElementById('selectAll');
    const checks = () => Array.from(document.querySelectorAll('.rowCheck'));

    function selectedIds() {
        return checks().filter(cb => cb.checked).map(cb => cb.value);
    }

    function setAll(val) {
        checks().forEach(cb => cb.checked = val);
    }

    if (selectAll) {
        selectAll.addEventListener('change', () => setAll(selectAll.checked));
    }

    function submitBulk(url, method, ids) {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = url;

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = "{{ csrf_token() }}";
        f.appendChild(csrf);

        if (method === 'DELETE') {
            const m = document.createElement('input');
            m.type = 'hidden';
            m.name = '_method';
            m.value = 'DELETE';
            f.appendChild(m);
        }

        ids.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'ids[]';
            inp.value = id;
            f.appendChild(inp);
        });

        document.body.appendChild(f);
        f.submit();
    }

    const btnDelete = document.getElementById('btnDeleteSelected');
    if (btnDelete) {
        btnDelete.addEventListener('click', () => {
            const ids = selectedIds();
            if (!ids.length) return alert('Please select at least 1 notification.');
            if (!confirm('Delete selected notifications?')) return;
            submitBulk("{{ route('landlord.notifications.delete_selected') }}", "DELETE", ids);
        });
    }

    const btnRead = document.getElementById('btnMarkReadSelected');
    if (btnRead) {
        btnRead.addEventListener('click', () => {
            const ids = selectedIds();
            if (!ids.length) return alert('Please select at least 1 notification.');
            submitBulk("{{ route('landlord.notifications.read') }}", "POST", ids);
        });
    }
})();
</script>
@endsection