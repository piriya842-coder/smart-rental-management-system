<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LandlordRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $landlord;

    public function __construct(User $landlord)
    {
        $this->landlord = $landlord;
    }

    public function build()
    {
        return $this->subject('Smart Rental — Landlord Application Rejected')
            ->view('emails.landlord-rejected')
            ->with([
                'landlord' => $this->landlord,
                'reason'   => $this->landlord->landlord_rejected_reason,
            ]);
    }
}
