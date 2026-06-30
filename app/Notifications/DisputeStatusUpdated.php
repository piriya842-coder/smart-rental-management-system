<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(
        public Dispute $dispute,
        public string $eventType = 'status_updated'
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'            => 'dispute',
            'kind'            => 'dispute',
            'event_type'      => $this->eventType,
            'dispute_id'      => $this->dispute->id,
            'code'            => $this->dispute->code,
            'status'          => $this->dispute->status,
            'resolution'      => $this->dispute->resolution,
            'title'           => $this->databaseTitle(),
            'message'         => $this->databaseMessage(),
            'outcome_details' => $this->dispute->outcome_details,
            'created_at'      => now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Smart Rental — Dispute ' . $this->dispute->code . ' Updated')
            ->greeting('Hello ' . ($notifiable->name ?? 'User') . ',')
            ->line($this->mailIntro())
            ->line('Dispute Code: ' . $this->dispute->code)
            ->line('Current Status: ' . ucwords(str_replace('_', ' ', (string) $this->dispute->status)));

        if (!empty($this->dispute->title)) {
            $mail->line('Issue Title: ' . $this->dispute->title);
        }

        if (!empty($this->dispute->resolution) && $this->dispute->status === 'resolved') {
            $mail->line('Resolution: ' . ucwords(str_replace('_', ' ', (string) $this->dispute->resolution)));
        }

        if (!empty($this->dispute->outcome_details)) {
            $mail->line('Outcome Details: ' . $this->dispute->outcome_details);
        }

        return $mail
            ->line('Please log in to Smart Rental to view the latest dispute details.')
            ->salutation('Regards, Smart Rental');
    }

    private function databaseTitle(): string
    {
        return match ($this->eventType) {
            'resolved' => 'Dispute Resolved',
            'rejected' => 'Dispute Rejected',
            default    => 'Dispute Updated',
        };
    }

    private function databaseMessage(): string
    {
        return match ($this->eventType) {
            'resolved' => 'Your dispute ' . $this->dispute->code . ' has been resolved by admin.',
            'rejected' => 'Your dispute ' . $this->dispute->code . ' has been rejected by admin.',
            default    => 'Your dispute ' . $this->dispute->code . ' status is now ' . ucwords(str_replace('_', ' ', (string) $this->dispute->status)) . '.',
        };
    }

    private function mailIntro(): string
    {
        return match ($this->eventType) {
            'resolved' => 'Your submitted dispute has been resolved by admin.',
            'rejected' => 'Your submitted dispute has been rejected by admin.',
            default    => 'Your submitted dispute has been updated by admin.',
        };
    }
}