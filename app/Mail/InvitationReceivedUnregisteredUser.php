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

	public $locale;
    public $invitation;
    public $entryMessage;
	public $sender;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($locale, Invitation $invitation, $message)
    {
		$this->locale = $locale;
		$this->sender = $invitation->sender->first_name . " " . $invitation->sender->last_name;
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
        return $this->from(config('mail.noreply'))
        ->markdown('emails.' . $this->locale . '.unregistered-user-invitation-mail');
    }
}
