<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

use App\Models\User;
use App\Models\Invitation;

class InvitationReceivedUnregisteredUser extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $entryMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation, $message)
    {
        $this->invitation = $invitation;
        $this->entryMessage = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.' . App::currentLocale() . 'unregistered-user-invitation-mail');
    }
}
