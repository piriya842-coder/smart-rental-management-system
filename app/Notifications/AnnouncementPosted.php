<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AnnouncementPosted extends Notification
{
    use Queueable;

    public function __construct(
        public int $announcementId,
        public string $title,
        public string $message
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'kind' => 'announcement',
            'title' => $this->title,
            'message' => $this->message,
            'announcement_id' => $this->announcementId,
            // optional: where to go when click
            'url' => route('student.notifications.index'),
        ];
    }
}