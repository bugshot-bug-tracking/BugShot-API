<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use App\Services\GetUserLocaleService;

use App\Models\User;
use App\Models\Invitation;

class InvitationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
	public $locale;
    public $invitation;
    public $entryMessage;
	public $sender;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, $locale, Invitation $invitation, $message)
    {
        $this->locale = $locale;
        $this->user = $notifiable;
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
        $status = $this->from(config('mail.noreply'))
        ->markdown('emails.' . $this->locale . '.invitation-mail');

		return $status;
    }
}
