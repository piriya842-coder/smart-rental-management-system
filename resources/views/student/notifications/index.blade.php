@extends('layouts.student')

@section('title', 'Notifications • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $cream   = '#fffafa';
  $choco   = '#4a2c2a';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';

  $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y, h:i A') : '';
@endphp

<div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm overflow-hidden">

  <!-- HEADER -->
  <div class="p-6 md:p-8 border-b border-[rgba(201,42,42,.08)] bg-gradient-to-r from-white via-white to-[#fffafa]">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
      <div>
        <div class="text-xs font-black tracking-wider text-black/50">INBOX</div>
        <h1 class="text-3xl md:text-4xl font-extrabold mt-1" style="color: {{ $choco }};">Notifications</h1>
        <p class="mt-2 text-black/60">
          Announcements, booking updates, and payment updates will appear here.
        </p>

        <div class="mt-3 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border border-[rgba(201,42,42,.10)]"
             style="background: {{ $softRed }}; color: {{ $choco }};">
          Unread:
          <span class="px-2 py-0.5 rounded-full text-white" style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
            {{ $unreadCount ?? 0 }}
          </span>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row gap-3">
        <form method="POST" action="{{ route('student.notifications.read_all') }}">
          @csrf
          <button class="rounded-2xl px-5 py-3 font-extrabold border border-[rgba(201,42,42,.12)] bg-white hover:bg-[#fff5f5] transition"
                  style="color: {{ $choco }};">
            Mark all as read
          </button>
        </form>

        <form method="POST" action="{{ route('student.notifications.clear_all') }}"
              onsubmit="return confirm('Delete ALL notifications?')">
          @csrf
          @method('DELETE')
          <button class="rounded-2xl px-5 py-3 font-extrabold text-white hover:brightness-95 transition"
                  style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
            Clear all
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- BODY -->
  <div class="p-6 md:p-8">

    @if(session('success'))
      <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 font-semibold">
        ✅ {{ session('success') }}
      </div>
    @endif

    @if(($notifications ?? collect())->count() === 0)
      <div class="rounded-3xl border border-[rgba(201,42,42,.10)] p-8 text-center"
           style="background: {{ $softRed }};">
        <div class="text-5xl">🔔</div>
        <div class="mt-3 text-xl font-extrabold" style="color: {{ $choco }};">No notifications yet</div>
        <div class="mt-2 text-black/60">When admin posts announcements or your booking/payment changes, you’ll see it here.</div>
      </div>
    @else

      <!-- ACTION BAR (NO FORM WRAP - so no nested form bug) -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
        <div class="flex flex-wrap items-center gap-3">
          <label class="inline-flex items-center gap-2 font-bold text-black/70">
            <input id="selectAll" type="checkbox" class="h-5 w-5 rounded border-black/20">
            Select all
          </label>

          <button type="button" id="btnDeleteSelected"
                  class="rounded-2xl px-5 py-3 font-extrabold text-white shadow-sm hover:brightness-95 transition"
                  style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
            Delete selected
          </button>

          <button type="button" id="btnMarkReadSelected"
                  class="rounded-2xl px-5 py-3 font-extrabold border border-[rgba(201,42,42,.12)] bg-white hover:bg-[#fff5f5] transition"
                  style="color: {{ $choco }};">
            Mark selected as read
          </button>
        </div>
      </div>

      <!-- LIST -->
      <div class="space-y-3">

        @foreach($notifications as $n)
          @php
            $data = (array)($n->data ?? []);
            $title = $data['title'] ?? 'Notification';
            $msg = $data['message'] ?? '';
            $kind = $data['kind'] ?? 'system';
            $isUnread = is_null($n->read_at);
            $icon = $kind === 'announcement' ? '📢' : ($kind === 'booking' ? '📌' : ($kind === 'payment' ? '💳' : '🔔'));
          @endphp

          <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white overflow-hidden">
            <div class="p-5 md:p-6 flex items-start gap-4">

              <div class="pt-1">
                <input type="checkbox" value="{{ $n->id }}"
                       class="rowCheck h-5 w-5 rounded border-black/20">
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="flex items-center gap-2">
                      <div class="text-xl">{{ $icon }}</div>

                      <div class="font-extrabold truncate" style="color: {{ $choco }};">
                        {{ $title }}
                      </div>

                      @if($isUnread)
                        <span class="inline-flex h-2.5 w-2.5 rounded-full" style="background: {{ $gold }};"></span>
                      @endif
                    </div>

                    <div class="mt-1 text-xs text-black/50 font-semibold">
                      {{ $fmtDate($n->created_at) }}
                    </div>
                  </div>

                  <div class="flex items-center gap-2">
                    @if($isUnread)
                      <!-- Single mark read -->
                      <form method="POST" action="{{ route('student.notifications.read') }}">
                        @csrf
                        <input type="hidden" name="ids[]" value="{{ $n->id }}">
                        <button class="rounded-xl px-4 py-2 text-xs font-extrabold border border-[rgba(201,42,42,.12)] bg-white hover:bg-[#fff5f5] transition"
                                style="color: {{ $choco }};">
                          Mark read
                        </button>
                      </form>
                    @endif

                    <!-- Delete one -->
                    <form method="POST" action="{{ route('student.notifications.destroy', $n->id) }}"
                          onsubmit="return confirm('Delete this notification?')">
                      @csrf
                      @method('DELETE')
                      <button class="rounded-xl px-4 py-2 text-xs font-extrabold text-white hover:brightness-95 transition"
                              style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
                        Delete
                      </button>
                    </form>
                  </div>
                </div>

                @if($msg)
                  <div class="mt-3 text-black/70 leading-relaxed">
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

      // CSRF
      const csrf = document.createElement('input');
      csrf.type = 'hidden';
      csrf.name = '_token';
      csrf.value = "{{ csrf_token() }}";
      f.appendChild(csrf);

      // Method spoof if DELETE
      if (method === 'DELETE') {
        const m = document.createElement('input');
        m.type = 'hidden';
        m.name = '_method';
        m.value = 'DELETE';
        f.appendChild(m);
      }

      // ids[]
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

    // Delete selected
    const btnDelete = document.getElementById('btnDeleteSelected');
    if (btnDelete) {
      btnDelete.addEventListener('click', () => {
        const ids = selectedIds();
        if (!ids.length) return alert('Please select at least 1 notification.');
        if (!confirm('Delete selected notifications?')) return;

        submitBulk("{{ route('student.notifications.delete_selected') }}", "DELETE", ids);
      });
    }

    // Mark read selected
    const btnRead = document.getElementById('btnMarkReadSelected');
    if (btnRead) {
      btnRead.addEventListener('click', () => {
        const ids = selectedIds();
        if (!ids.length) return alert('Please select at least 1 notification.');

        submitBulk("{{ route('student.notifications.read') }}", "POST", ids);
      });
    }
  })();
</script>
@endsection