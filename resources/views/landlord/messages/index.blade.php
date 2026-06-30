@extends('layouts.landlord')

@section('title', 'Messages • Landlord')
@section('page_title', 'Messages')
@section('page_subtitle', 'Chat with students about bookings and rooms.')

@section('content')
<style>
    .sr-msg-panel{
        border:1px solid #E5E7EB;
        background:#FFFFFF;
        box-shadow:0 18px 42px rgba(15,23,42,.06);
    }

    .sr-msg-shell{
        background:
            radial-gradient(circle at top right, rgba(214,179,107,.10), transparent 24%),
            linear-gradient(135deg,#FFFFFF 0%,#FAF6F2 100%);
        border-bottom:1px solid rgba(0,0,0,.05);
    }

    .sr-convo-item{
        transition:all .2s ease;
    }
    .sr-convo-item:hover{
        background:#F8FAFC;
    }
    .sr-convo-active{
        background:linear-gradient(135deg,#EFF6FF 0%,#F8FBFF 100%);
        border-left:4px solid #2563EB;
    }

    .sr-avatar{
        height:48px;
        width:48px;
        border-radius:16px;
        display:grid;
        place-items:center;
        font-size:18px;
        font-weight:900;
        flex-shrink:0;
        background:linear-gradient(135deg,#0F172A 0%,#1E3A8A 100%);
        color:#FFFFFF;
        box-shadow:0 10px 20px rgba(15,23,42,.16);
    }

    .sr-chat-bg{
        background:
            radial-gradient(circle at top right, rgba(214,179,107,.08), transparent 22%),
            linear-gradient(180deg,#F8FAFC 0%, #F1F5F9 100%);
    }

    .sr-msg-bubble-mine{
        background:linear-gradient(135deg,#2563EB 0%,#1D4ED8 100%);
        color:#FFFFFF;
        box-shadow:0 12px 22px rgba(37,99,235,.20);
    }

    .sr-msg-bubble-other{
        background:#FFFFFF;
        border:1px solid rgba(0,0,0,.06);
        color:#1F2937;
        box-shadow:0 10px 18px rgba(15,23,42,.05);
    }

    .sr-msg-action{
        font-size:11px;
        font-weight:800;
        border:none;
        background:none;
        padding:0;
        cursor:pointer;
        transition:.2s ease;
    }
    .sr-msg-action:hover{
        opacity:.8;
    }

    .sr-send-btn{
        min-height:52px;
        min-width:110px;
        border-radius:18px;
        font-size:14px;
        font-weight:900;
        color:#FFFFFF;
        background:linear-gradient(135deg,#2563EB 0%,#1D4ED8 100%);
        box-shadow:0 12px 24px rgba(37,99,235,.22);
        transition:all .2s ease;
    }
    .sr-send-btn:hover{
        transform:translateY(-2px);
        box-shadow:0 16px 28px rgba(37,99,235,.28);
    }

    .sr-chip{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:9999px;
        padding:.38rem .8rem;
        font-size:11px;
        font-weight:800;
        white-space:nowrap;
    }

    .sr-chat-input{
        border:1px solid rgba(0,0,0,.10);
        border-radius:20px;
        min-height:52px;
        resize:none;
        transition:.2s ease;
    }
    .sr-chat-input:focus{
        outline:none;
        box-shadow:0 0 0 3px rgba(37,99,235,.10);
        border-color:rgba(37,99,235,.25);
    }
</style>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- LEFT PANEL --}}
    <div class="lg:col-span-4">
        <div class="sr-msg-panel rounded-[30px] overflow-hidden">
            <div class="sr-msg-shell px-5 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-[0.2em] font-black" style="color:#9CA3AF;">
                            Inbox
                        </div>
                        <div class="text-2xl font-extrabold mt-2" style="color:#111827;">Messages</div>
                        <p class="mt-2 text-sm" style="color:#6B7280;">
                            Manage student conversations clearly and keep them as permanent records.
                        </p>
                    </div>

                <div class="hidden sm:flex items-center justify-center h-10 w-10 rounded-xl"
                 style="background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE;">
                💬
                </div>
                </div>
            </div>

            <div id="conversationList" class="max-h-[680px] overflow-y-auto divide-y divide-black/5">
                @forelse($bookings as $booking)
                    @php
                        $latestMessage = $booking->messages->sortByDesc('created_at')->first();
                        $isActive = $selectedBooking && $selectedBooking->id === $booking->id;
                        $name = $booking->student?->name ?? 'Student';
                        $initial = strtoupper(substr($name, 0, 1));
                    @endphp

                    <a href="{{ route('landlord.messages.index', ['booking' => $booking->id]) }}"
                       class="sr-convo-item block px-5 py-4 {{ $isActive ? 'sr-convo-active' : '' }}">
                        <div class="flex items-start gap-3">
                            <div class="sr-avatar">{{ $initial }}</div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="font-extrabold truncate" style="color:#111827;">
                                            {{ $name }}
                                        </div>
                                        <div class="text-sm mt-1 truncate" style="color:#6B7280;">
                                            {{ $booking->room?->title ?? 'Room' }}
                                        </div>
                                    </div>

                                    <div class="text-right shrink-0">
                                        @if($latestMessage)
                                            <div class="text-[11px]" style="color:#9CA3AF;">
                                                {{ $latestMessage->created_at?->format('h:i A') }}
                                            </div>
                                        @endif

                                        @if(($booking->unread_messages_count ?? 0) > 0)
                                            <div class="mt-2 inline-flex min-w-[22px] h-[22px] px-1 rounded-full items-center justify-center text-[11px] font-extrabold text-white"
                                                 style="background:#2563EB;">
                                                {{ $booking->unread_messages_count > 99 ? '99+' : $booking->unread_messages_count }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-xs mt-1" style="color:#9CA3AF;">
                                    Booking #{{ $booking->id }}
                                </div>

                                <div class="text-sm mt-2 truncate" style="color:#6B7280;">
                                    {{ $latestMessage ? ($latestMessage->deleted_for_everyone_at ? 'This message was deleted.' : $latestMessage->message) : '' }}
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-12 text-center" style="color:#6B7280;">
                        <div class="text-4xl mb-3">💬</div>
                        <div class="font-extrabold text-lg" style="color:#374151;">No student conversations yet.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="lg:col-span-8">
        <div class="sr-msg-panel rounded-[30px] overflow-hidden min-h-[680px] flex flex-col">

            <div class="sr-msg-shell px-6 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div id="chatHeaderTitle" class="text-2xl font-extrabold" style="color:#111827;">
                            {{ $selectedBooking?->student?->name ?? 'Select a conversation' }}
                        </div>

                        <div id="chatHeaderSubtitle" class="text-sm mt-2" style="color:#6B7280;">
                            @if($selectedBooking)
                                {{ $selectedBooking->room?->title ?? 'Room' }} • Booking #{{ $selectedBooking->id }}
                            @else
                                Select a student conversation to read and reply.
                            @endif
                        </div>

                        @if($selectedBooking)
                            <div class="mt-3">
                                <span class="sr-chip" style="background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE;">
                                    BOOKING CHAT
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="hidden sm:block text-right">
                        <div class="text-xs font-black uppercase tracking-[0.16em]" style="color:#9CA3AF;">Smart Rental Chat</div>
                        <div class="text-xs mt-2" style="color:#6B7280;">Evidence-safe message history</div>
                    </div>
                </div>
            </div>

            <div id="chatBody" class="sr-chat-bg flex-1 px-6 py-5 overflow-y-auto">
                @if($selectedBooking)
                    <div class="text-center py-10" style="color:#6B7280;">Loading conversation...</div>
                @else
                    <div class="flex-1 flex items-center justify-center p-10 text-center" style="color:#6B7280;">
                        Select a student conversation to read and reply.
                    </div>
                @endif
            </div>

            <div class="border-t border-black/5 bg-white p-4 md:p-5">
                <form id="chatForm" class="flex items-end gap-3">
                    @csrf
                    <textarea id="chatInput"
                              rows="2"
                              maxlength="2000"
                              placeholder="Type your reply to student..."
                              class="sr-chat-input flex-1 px-4 py-3"></textarea>

                    <button type="submit" class="sr-send-btn">
                        Send
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>

<script>
(() => {
    const csrf = "{{ csrf_token() }}";
    let selectedBookingId = @json($selectedBooking?->id);

    const conversationList = document.getElementById('conversationList');
    const chatBody = document.getElementById('chatBody');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatHeaderTitle = document.getElementById('chatHeaderTitle');
    const chatHeaderSubtitle = document.getElementById('chatHeaderSubtitle');

    const dataUrl = "{{ route('landlord.messages.data') }}";
    const storeBase = "{{ url('/landlord/messages') }}";
    const updateBase = "{{ url('/landlord/messages') }}";
    const deleteBase = "{{ url('/landlord/messages') }}";
    const indexBase = "{{ route('landlord.messages.index') }}";

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
            <div class="mt-2 flex ${message.mine ? 'justify-end' : 'justify-start'} gap-3">
                <button type="button"
                        onclick="window.chatEditMessage(${message.id}, \`${escapeHtml(message.raw_body).replace(/`/g, '\\`')}\`)"
                        class="sr-msg-action"
                        style="color:#6B7280;">
                    Edit
                </button>
                <button type="button"
                        onclick="window.chatDeleteMessage(${message.id})"
                        class="sr-msg-action"
                        style="color:#DC2626;">
                    Delete for everyone
                </button>
            </div>
        `;
    }

    function renderMessages(messages) {
        if (!selectedBookingId) {
            chatBody.innerHTML = `
                <div class="flex h-full items-center justify-center p-10 text-center" style="color:#6B7280;">
                    Select a student conversation to read and reply.
                </div>
            `;
            return;
        }

        if (!messages.length) {
            chatBody.innerHTML = `
                <div class="h-full flex items-center justify-center">
                    <div class="text-center" style="color:#6B7280;">
                        <div class="text-4xl mb-3">💬</div>
                        <div class="text-lg font-extrabold mb-2" style="color:#334155;">No messages yet</div>
                        <div>Student has not started the conversation.</div>
                    </div>
                </div>
            `;
            return;
        }

        chatBody.innerHTML = messages.map(message => `
            <div class="mb-5 flex ${message.mine ? 'justify-end' : 'justify-start'}">
                <div class="max-w-[80%]">
                    <div class="mb-1 text-[11px] ${message.mine ? 'text-right' : 'text-left'}" style="color:#6B7280;">
                        ${escapeHtml(message.sender_name)}
                    </div>

                    <div class="rounded-[22px] px-4 py-3 ${message.mine ? 'sr-msg-bubble-mine' : 'sr-msg-bubble-other'}">
                        <div class="text-sm leading-relaxed break-words">
                            ${escapeHtml(message.body)}
                        </div>
                    </div>

                    ${messageActionsHtml(message)}

                    <div class="mt-1 text-[11px] ${message.mine ? 'text-right' : 'text-left'}" style="color:#6B7280;">
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
            ? conversations.map(item => {
                const initial = (item.name || 'S').charAt(0).toUpperCase();
                return `
                <a href="${indexBase}?booking=${item.id}"
                   data-booking-id="${item.id}"
                   class="conversation-link sr-convo-item block px-5 py-4 ${selectedBookingId == item.id ? 'sr-convo-active' : ''}">
                    <div class="flex items-start gap-3">
                        <div class="sr-avatar">${escapeHtml(initial)}</div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-extrabold truncate" style="color:#111827;">
                                        ${escapeHtml(item.name)}
                                    </div>
                                    <div class="text-sm mt-1 truncate" style="color:#6B7280;">
                                        ${escapeHtml(item.subtitle)}
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    ${item.time ? `<div class="text-[11px]" style="color:#9CA3AF;">${escapeHtml(item.time)}</div>` : ''}
                                    ${item.unread_count > 0 ? `
                                        <div class="mt-2 inline-flex min-w-[22px] h-[22px] px-1 rounded-full items-center justify-center text-[11px] font-extrabold text-white"
                                             style="background:#2563EB;">
                                            ${item.unread_count > 99 ? '99+' : item.unread_count}
                                        </div>
                                    ` : ''}
                                </div>
                            </div>

                            <div class="text-xs mt-1" style="color:#9CA3AF;">
                                ${escapeHtml(item.booking_label)}
                            </div>

                            <div class="text-sm mt-2 truncate" style="color:#6B7280;">
                                ${escapeHtml(item.preview)}
                            </div>
                        </div>
                    </div>
                </a>`;
            }).join('')
            : `<div class="px-5 py-12 text-center" style="color:#6B7280;"><div class="text-4xl mb-3">💬</div><div class="font-extrabold text-lg" style="color:#374151;">No student conversations yet.</div></div>`;

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
            chatBody.innerHTML = `<div class="text-center py-10" style="color:#6B7280;">Loading conversation...</div>`;
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
            alert('Unable to send reply.');
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
    setInterval(() => loadData(false), 4000);
})();
</script>
@endsection