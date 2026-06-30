<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AnnouncementPublished extends Notification
{
    use Queueable;

    public function __construct(public Announcement $announcement) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'announcement',
            'kind' => 'announcement',
            'title' => $this->announcement->title,
            'message' => $this->announcement->message,
            'announcement_id' => $this->announcement->id,
            'created_at' => $this->announcement->created_at?->toDateTimeString(),
        ];
    }
}