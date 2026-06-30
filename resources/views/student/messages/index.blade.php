@extends('layouts.student')

@section('title', 'Messages • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $choco   = '#4a2c2a';
  $cream   = '#fffafa';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';
@endphp

<div class="mx-auto max-w-7xl">
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- LEFT PANEL --}}
    <div class="lg:col-span-4">
      <div class="rounded-[28px] border border-[rgba(201,42,42,.08)] bg-white/95 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-[rgba(201,42,42,.08)] bg-white">
          <div class="text-2xl font-extrabold" style="color: {{ $choco }};">Messages</div>
          <div class="text-sm text-gray-600 mt-1">
            Chat with landlords and keep your conversation records safely in Smart Rental.
          </div>
        </div>

        <div id="conversationList" class="max-h-[680px] overflow-y-auto divide-y divide-[rgba(201,42,42,.08)]">
          @forelse($bookings as $booking)
            @php
              $latestMessage = $booking->messages->sortByDesc('created_at')->first();
              $isActive = $selectedBooking && $selectedBooking->id === $booking->id;
            @endphp

            <a href="{{ route('student.messages.index', ['booking' => $booking->id]) }}"
               class="block px-5 py-4 transition {{ $isActive ? 'bg-[#fdf2f2]' : 'hover:bg-[#fdf2f2]' }}">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-extrabold truncate" style="color: {{ $choco }};">
                    {{ $booking->landlord?->name ?? 'Landlord' }}
                  </div>
                  <div class="text-sm text-gray-700 mt-1 truncate">
                    {{ $booking->room?->title ?? 'Room' }}
                  </div>
                  <div class="text-xs text-gray-500 mt-1">
                    Booking #{{ $booking->id }}
                  </div>
                  <div class="text-sm text-gray-600 mt-2 truncate">
                    {{ $latestMessage ? ($latestMessage->deleted_for_everyone_at ? 'This message was deleted.' : $latestMessage->message) : 'Start a conversation for this booking.' }}
                  </div>
                </div>

                <div class="text-right shrink-0">
                  @if($latestMessage)
                    <div class="text-[11px] text-gray-500">
                      {{ $latestMessage->created_at?->format('h:i A') }}
                    </div>
                  @endif

                  @if(($booking->unread_messages_count ?? 0) > 0)
                    <div class="mt-2 inline-flex min-w-[22px] h-[22px] px-1 rounded-full items-center justify-center text-[11px] font-extrabold text-white"
                         style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
                      {{ $booking->unread_messages_count > 99 ? '99+' : $booking->unread_messages_count }}
                    </div>
                  @endif
                </div>
              </div>
            </a>
          @empty
            <div class="px-5 py-10 text-center text-gray-600">
              No booking conversations yet.
            </div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="lg:col-span-8">
      <div class="rounded-[28px] border border-[rgba(201,42,42,.08)] bg-white/95 shadow-sm overflow-hidden min-h-[680px] flex flex-col">

        <div class="px-6 py-4 border-b border-[rgba(201,42,42,.08)] bg-white">
          <div class="flex items-start justify-between gap-4">
            <div>
              <div id="chatHeaderTitle" class="text-2xl font-extrabold" style="color: {{ $choco }};">
                {{ $selectedBooking?->landlord?->name ?? 'Select a conversation' }}
              </div>
              <div id="chatHeaderSubtitle" class="text-sm text-gray-600 mt-1">
                @if($selectedBooking)
                  {{ $selectedBooking->room?->title ?? 'Room' }} • Booking #{{ $selectedBooking->id }}
                @else
                  Select a booking conversation to start chatting with landlord.
                @endif
              </div>
            </div>

            <div class="hidden sm:block text-right">
              <div class="text-xs font-semibold text-gray-500">Smart Rental Chat</div>
              <div class="text-xs text-gray-500 mt-1">Message history is saved</div>
            </div>
          </div>
        </div>

        <div id="chatBody" class="flex-1 px-6 py-5 overflow-y-auto"
             style="background: linear-gradient(180deg, #fffafa 0%, #fdf2f2 100%);">
          @if($selectedBooking)
            <div class="text-center text-gray-500 py-10">Loading conversation...</div>
          @else
            <div class="h-full flex items-center justify-center">
              <div class="text-center text-gray-500">
                <div class="text-lg font-bold mb-2" style="color: {{ $choco }};">No conversation selected</div>
                <div>Select a booking from the left side to start messaging.</div>
              </div>
            </div>
          @endif
        </div>

        <div class="border-t border-[rgba(201,42,42,.08)] bg-white p-4">
          <form id="chatForm" class="flex items-end gap-3">
            @csrf
            <textarea id="chatInput"
                      rows="2"
                      maxlength="2000"
                      placeholder="Type your message to landlord..."
                      class="flex-1 rounded-[20px] border border-[rgba(201,42,42,.10)] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.12)] resize-none"></textarea>

            <button type="submit"
                    id="sendBtn"
                    class="rounded-[18px] px-6 py-3 text-sm font-extrabold text-white shadow-sm"
                    style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
              Send
            </button>
          </form>
        </div>

      </div>
    </div>

  </div>
</div>

<script>
(() => {
  const csrf = "{{ csrf_token() }}";
  let selectedBookingId = @json($selectedBooking?->id);
  let polling = null;

  const conversationList = document.getElementById('conversationList');
  const chatBody = document.getElementById('chatBody');
  const chatForm = document.getElementById('chatForm');
  const chatInput = document.getElementById('chatInput');
  const chatHeaderTitle = document.getElementById('chatHeaderTitle');
  const chatHeaderSubtitle = document.getElementById('chatHeaderSubtitle');

  const dataUrl = "{{ route('student.messages.data') }}";
  const storeBase = "{{ url('/student/messages') }}";
  const updateBase = "{{ url('/student/messages') }}";
  const deleteBase = "{{ url('/student/messages') }}";
  const indexBase = "{{ route('student.messages.index') }}";

  function escapeHtml(str) {
    return String(str ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function scrollToBottom() {
    if (chatBody) {
      chatBody.scrollTop = chatBody.scrollHeight;
    }
  }

  function messageActionsHtml(message) {
    if (!message.mine || message.deleted) return '';

    return `
      <div class="mt-2 flex ${message.mine ? 'justify-end' : 'justify-start'} gap-2">
        <button type="button"
                onclick="window.chatEditMessage(${message.id}, \`${escapeHtml(message.raw_body).replace(/`/g, '\\`')}\`)"
                class="text-[11px] font-bold text-gray-500 hover:text-gray-800">
          Edit
        </button>
        <button type="button"
                onclick="window.chatDeleteMessage(${message.id})"
                class="text-[11px] font-bold text-red-500 hover:text-red-700">
          Delete for everyone
        </button>
      </div>
    `;
  }

  function renderMessages(messages) {
    if (!selectedBookingId) {
      chatBody.innerHTML = `
        <div class="h-full flex items-center justify-center">
          <div class="text-center text-gray-500">
            <div class="text-lg font-bold mb-2" style="color: {{ $choco }};">No conversation selected</div>
            <div>Select a booking from the left side to start messaging.</div>
          </div>
        </div>
      `;
      return;
    }

    if (!messages.length) {
      chatBody.innerHTML = `
        <div class="h-full flex items-center justify-center">
          <div class="text-center text-gray-500">
            <div class="text-lg font-bold mb-2" style="color: {{ $choco }};">No messages yet</div>
            <div>Start your conversation with the landlord below.</div>
          </div>
        </div>
      `;
      return;
    }

    chatBody.innerHTML = messages.map(message => `
      <div class="mb-4 flex ${message.mine ? 'justify-end' : 'justify-start'}">
        <div class="max-w-[78%]">
          <div class="mb-1 text-[11px] ${message.mine ? 'text-right text-gray-500' : 'text-left text-gray-500'}">
            ${escapeHtml(message.sender_name)}
          </div>

          <div class="rounded-[22px] px-4 py-3 shadow-sm ${message.mine ? 'text-white' : 'bg-white border border-[rgba(201,42,42,.08)] text-gray-800'}"
               style="${message.mine ? 'background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);' : ''}">
            <div class="text-sm leading-relaxed break-words">
              ${escapeHtml(message.body)}
            </div>
          </div>

          ${messageActionsHtml(message)}

          <div class="mt-1 text-[11px] ${message.mine ? 'text-right text-gray-500' : 'text-left text-gray-500'}">
            ${escapeHtml(message.created_at ?? '')}
            ${message.edited ? ' • edited' : ''}
            ${message.mine ? (message.seen ? ' • Seen' : ' • Sent') : ''}
          </div>
        </div>
      </div>
    `).join('');

    scrollToBottom();
  }

  function renderConversations(conversations) {
    conversationList.innerHTML = conversations.length
      ? conversations.map(item => `
        <a href="${indexBase}?booking=${item.id}"
           data-booking-id="${item.id}"
           class="conversation-link block px-5 py-4 transition ${selectedBookingId == item.id ? 'bg-[#fdf2f2]' : 'hover:bg-[#fdf2f2]'}">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="font-extrabold truncate" style="color: {{ $choco }};">
                ${escapeHtml(item.name)}
              </div>
              <div class="text-sm text-gray-700 mt-1 truncate">
                ${escapeHtml(item.subtitle)}
              </div>
              <div class="text-xs text-gray-500 mt-1">
                ${escapeHtml(item.booking_label)}
              </div>
              <div class="text-sm text-gray-600 mt-2 truncate">
                ${escapeHtml(item.preview)}
              </div>
            </div>

            <div class="text-right shrink-0">
              ${item.time ? `<div class="text-[11px] text-gray-500">${escapeHtml(item.time)}</div>` : ''}
              ${item.unread_count > 0 ? `
                <div class="mt-2 inline-flex min-w-[22px] h-[22px] px-1 rounded-full items-center justify-center text-[11px] font-extrabold text-white"
                     style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
                  ${item.unread_count > 99 ? '99+' : item.unread_count}
                </div>
              ` : ''}
            </div>
          </div>
        </a>
      `).join('')
      : `<div class="px-5 py-10 text-center text-gray-600">No booking conversations yet.</div>`;

    document.querySelectorAll('.conversation-link').forEach(link => {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        selectedBookingId = this.dataset.bookingId;
        loadData(false);
        window.history.replaceState({}, '', `${indexBase}?booking=${selectedBookingId}`);
      });
    });
  }

  async function loadData(showLoader = false) {
    const url = selectedBookingId ? `${dataUrl}?booking=${selectedBookingId}` : dataUrl;

    if (showLoader && chatBody) {
      chatBody.innerHTML = `<div class="text-center text-gray-500 py-10">Loading conversation...</div>`;
    }

    try {
      const response = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const data = await response.json();

      selectedBookingId = data.selected_booking_id ?? null;
      chatHeaderTitle.textContent = data.header_title ?? 'Messages';
      chatHeaderSubtitle.textContent = data.header_subtitle ?? '';

      renderConversations(data.conversations || []);
      renderMessages(data.messages || []);
    } catch (error) {
      console.error(error);
    }
  }

  chatForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    if (!selectedBookingId) return;
    const message = chatInput.value.trim();
    if (!message) return;

    try {
      const response = await fetch(`${storeBase}/${selectedBookingId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ message })
      });

      if (!response.ok) throw new Error('Failed to send');

      chatInput.value = '';
      await loadData(false);
    } catch (error) {
      alert('Unable to send message.');
    }
  });

  window.chatEditMessage = async function (messageId, currentMessage) {
    const updated = prompt('Edit your message:', currentMessage);
    if (updated === null) return;

    const trimmed = updated.trim();
    if (!trimmed) {
      alert('Message cannot be empty.');
      return;
    }

    try {
      const response = await fetch(`${updateBase}/${messageId}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ message: trimmed })
      });

      if (!response.ok) throw new Error('Failed to edit');

      await loadData(false);
    } catch (error) {
      alert('Unable to edit message.');
    }
  };

  window.chatDeleteMessage = async function (messageId) {
    if (!confirm('Delete this message for everyone?')) return;

    try {
      const response = await fetch(`${deleteBase}/${messageId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        }
      });

      if (!response.ok) throw new Error('Failed to delete');

      await loadData(false);
    } catch (error) {
      alert('Unable to delete message.');
    }
  };

  loadData(true);
  polling = setInterval(() => loadData(false), 4000);
})();
</script>
@endsection