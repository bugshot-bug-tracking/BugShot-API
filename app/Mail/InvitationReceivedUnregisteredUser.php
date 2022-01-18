<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use App\Models\Invitation;

class InvitationReceivedUnregisteredUser extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $resource;
    public $entryMessage;
    public $registerUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation, $resource, $message, $registerUrl)
    {
        $this->invitation = $invitation;
        $this->resource = $resource;
        $this->entryMessage = $message;
        $this->registerUrl = $registerUrl;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.unregistered-user-invitation-mail');
    }
}
