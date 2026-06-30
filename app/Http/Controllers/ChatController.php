<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Message;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function studentIndex(Request $request)
    {
        $studentId = Auth::id();

        $bookings = $this->getStudentBookings($studentId);
        $selectedBooking = $this->resolveStudentSelectedBooking($bookings, $request->query('booking'));

        if ($selectedBooking) {
            $this->markMessagesAsRead($selectedBooking->id, $studentId);
        }

        return view('student.messages.index', compact('bookings', 'selectedBooking'));
    }

    public function landlordIndex(Request $request)
    {
        $landlordId = Auth::id();

        $bookings = $this->getLandlordBookings($landlordId);
        $selectedBooking = $this->resolveLandlordSelectedBooking($bookings, $request->query('booking'));

        if ($selectedBooking) {
            $this->markMessagesAsRead($selectedBooking->id, $landlordId);
        }

        return view('landlord.messages.index', compact('bookings', 'selectedBooking'));
    }

    public function studentData(Request $request): JsonResponse
    {
        $studentId = Auth::id();

        $bookings = $this->getStudentBookings($studentId);
        $selectedBooking = $this->resolveStudentSelectedBooking($bookings, $request->query('booking'));

        if ($selectedBooking) {
            $this->markMessagesAsRead($selectedBooking->id, $studentId);
            $selectedBooking = $this->reloadBookingWithMessages($selectedBooking->id);
        }

        return response()->json([
            'conversations' => $this->serializeConversations($bookings, $studentId, 'student'),
            'selected_booking_id' => $selectedBooking?->id,
            'header_title' => $selectedBooking?->landlord?->name ?? 'Select a conversation',
            'header_subtitle' => $selectedBooking
                ? (($selectedBooking->room?->title ?? 'Room') . ' • Booking #' . $selectedBooking->id)
                : 'Select a booking conversation to start chatting with landlord.',
            'messages' => $selectedBooking
                ? $this->serializeMessages($selectedBooking->messages->sortBy('created_at'), $studentId)
                : [],
        ]);
    }

    public function landlordData(Request $request): JsonResponse
    {
        $landlordId = Auth::id();

        $bookings = $this->getLandlordBookings($landlordId);
        $selectedBooking = $this->resolveLandlordSelectedBooking($bookings, $request->query('booking'));

        if ($selectedBooking) {
            $this->markMessagesAsRead($selectedBooking->id, $landlordId);
            $selectedBooking = $this->reloadBookingWithMessages($selectedBooking->id);
        }

        return response()->json([
            'conversations' => $this->serializeConversations($bookings, $landlordId, 'landlord'),
            'selected_booking_id' => $selectedBooking?->id,
            'header_title' => $selectedBooking?->student?->name ?? 'Select a conversation',
            'header_subtitle' => $selectedBooking
                ? (($selectedBooking->room?->title ?? 'Room') . ' • Booking #' . $selectedBooking->id)
                : 'Select a student conversation to read and reply.',
            'messages' => $selectedBooking
                ? $this->serializeMessages($selectedBooking->messages->sortBy('created_at'), $landlordId)
                : [],
        ]);
    }

    public function studentStore(Request $request, Booking $booking): JsonResponse
    {
        abort_unless((int) $booking->student_id === (int) Auth::id(), 403);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'booking_id' => $booking->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $booking->landlord_id,
            'message' => trim($data['message']),
        ]);

        $message->load('sender');

        if ($booking->landlord) {
            $booking->landlord->notify(new NewMessageNotification($message));
        }

        return response()->json([
            'ok' => true,
        ]);
    }

    public function landlordStore(Request $request, Booking $booking): JsonResponse
    {
        abort_unless((int) $booking->landlord_id === (int) Auth::id(), 403);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'booking_id' => $booking->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $booking->student_id,
            'message' => trim($data['message']),
        ]);

        $message->load('sender');

        if ($booking->student) {
            $booking->student->notify(new NewMessageNotification($message));
        }

        return response()->json([
            'ok' => true,
        ]);
    }

    public function studentUpdate(Request $request, Message $message): JsonResponse
    {
        abort_unless((int) $message->sender_id === (int) Auth::id(), 403);
        abort_if($message->deleted_for_everyone_at, 422, 'Deleted messages cannot be edited.');

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message->update([
            'message' => trim($data['message']),
            'edited_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }

    public function landlordUpdate(Request $request, Message $message): JsonResponse
    {
        abort_unless((int) $message->sender_id === (int) Auth::id(), 403);
        abort_if($message->deleted_for_everyone_at, 422, 'Deleted messages cannot be edited.');

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message->update([
            'message' => trim($data['message']),
            'edited_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }

    public function studentDestroy(Message $message): JsonResponse
    {
        abort_unless((int) $message->sender_id === (int) Auth::id(), 403);

        $message->update([
            'deleted_for_everyone_at' => now(),
            'edited_at' => null,
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }

    public function landlordDestroy(Message $message): JsonResponse
    {
        abort_unless((int) $message->sender_id === (int) Auth::id(), 403);

        $message->update([
            'deleted_for_everyone_at' => now(),
            'edited_at' => null,
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }

    private function getStudentBookings(int $studentId): Collection
    {
        return Booking::with([
                'room',
                'landlord',
                'messages.sender',
                'messages.receiver',
            ])
            ->withCount([
                'messages as unread_messages_count' => function ($q) use ($studentId) {
                    $q->where('receiver_id', $studentId)
                      ->where('is_read', false);
                }
            ])
            ->where('student_id', $studentId)
            ->latest()
            ->get()
            ->map(function ($booking) {
                $booking->latest_chat_at = optional($booking->messages->sortByDesc('created_at')->first())->created_at;
                return $booking;
            })
            ->sortByDesc(function ($booking) {
                return $booking->latest_chat_at ?? $booking->created_at;
            })
            ->values();
    }

    private function getLandlordBookings(int $landlordId): Collection
    {
        return Booking::with([
                'room',
                'student',
                'messages.sender',
                'messages.receiver',
            ])
            ->withCount([
                'messages as unread_messages_count' => function ($q) use ($landlordId) {
                    $q->where('receiver_id', $landlordId)
                      ->where('is_read', false);
                }
            ])
            ->where('landlord_id', $landlordId)
            ->whereHas('messages')
            ->latest()
            ->get()
            ->map(function ($booking) {
                $booking->latest_chat_at = optional($booking->messages->sortByDesc('created_at')->first())->created_at;
                return $booking;
            })
            ->sortByDesc(function ($booking) {
                return $booking->latest_chat_at ?? $booking->created_at;
            })
            ->values();
    }

    private function resolveStudentSelectedBooking(Collection $bookings, $bookingId): ?Booking
    {
        if ($bookingId) {
            $found = $bookings->firstWhere('id', (int) $bookingId);
            if ($found) {
                return $found;
            }
        }

        return $bookings->first();
    }

    private function resolveLandlordSelectedBooking(Collection $bookings, $bookingId): ?Booking
    {
        if ($bookingId) {
            $found = $bookings->firstWhere('id', (int) $bookingId);
            if ($found) {
                return $found;
            }
        }

        return $bookings->first();
    }

    private function markMessagesAsRead(int $bookingId, int $receiverId): void
    {
        Message::where('booking_id', $bookingId)
            ->where('receiver_id', $receiverId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    private function reloadBookingWithMessages(int $bookingId): ?Booking
    {
        return Booking::with([
                'room',
                'landlord',
                'student',
                'messages.sender',
                'messages.receiver',
            ])
            ->find($bookingId);
    }

    private function serializeConversations(Collection $bookings, int $authId, string $role): array
    {
        return $bookings->map(function ($booking) use ($authId, $role) {
            $latestMessage = $booking->messages->sortByDesc('created_at')->first();

            return [
                'id' => $booking->id,
                'name' => $role === 'student'
                    ? ($booking->landlord?->name ?? 'Landlord')
                    : ($booking->student?->name ?? 'Student'),
                'subtitle' => $booking->room?->title ?? 'Room',
                'booking_label' => 'Booking #' . $booking->id,
                'preview' => $latestMessage
                    ? ($latestMessage->deleted_for_everyone_at ? 'This message was deleted.' : $latestMessage->message)
                    : ($role === 'student' ? 'Start a conversation for this booking.' : ''),
                'time' => $latestMessage?->created_at?->format('h:i A') ?? '',
                'unread_count' => (int) ($booking->unread_messages_count ?? 0),
            ];
        })->values()->all();
    }

    private function serializeMessages(Collection $messages, int $authId): array
    {
        return $messages->map(function ($message) use ($authId) {
            $mine = (int) $message->sender_id === (int) $authId;

            return [
                'id' => $message->id,
                'mine' => $mine,
                'sender_name' => $mine ? 'You' : ($message->sender?->name ?? 'User'),
                'body' => $message->deleted_for_everyone_at ? 'This message was deleted.' : $message->message,
                'raw_body' => $message->message,
                'created_at' => $message->created_at?->format('d M Y, h:i A'),
                'edited' => !is_null($message->edited_at),
                'deleted' => !is_null($message->deleted_for_everyone_at),
                'seen' => $mine && !is_null($message->read_at),
            ];
        })->values()->all();
    }
}