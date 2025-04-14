<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Listing;

class NewApplicationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $listing;

    public function __construct(User $user, Listing $listing)
    {
        $this->user = $user;
        $this->listing = $listing;
    }

    public function build()
    {
        return $this->subject("New Application for {$this->listing->title}")
                    ->view('emails.application_notification');
    }
}
