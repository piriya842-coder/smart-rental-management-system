<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public Message $chatMessage) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'message',
            'kind' => 'message',
            'title' => 'New message received',
            'message' => $this->chatMessage->sender->name . ' sent you a new message regarding Booking #' . $this->chatMessage->booking_id . '.',
            'booking_id' => $this->chatMessage->booking_id,
            'sender_id' => $this->chatMessage->sender_id,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Message Received — Smart Rental')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->chatMessage->sender->name . ' sent you a new message in Smart Rental.')
            ->line('Booking ID: #' . $this->chatMessage->booking_id)
            ->line('Preview: "' . \Illuminate\Support\Str::limit($this->chatMessage->message, 80) . '"')
            ->line('Please log in to Smart Rental to read and reply.')
            ->action('Open Smart Rental', url('/'))
            ->line('Thank you for using Smart Rental.');
    }
}