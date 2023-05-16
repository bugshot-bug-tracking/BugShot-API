<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use App\Services\GetUserLocaleService;

use App\Models\User;

class ApprovalReport extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
	public $locale;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, $locale)
    {
        $this->locale = $locale;
        $this->user = $notifiable;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $status = $this->from(config('mail.noreply'))
        ->markdown('emails.' . $this->locale . '.approval-report-mail');

		return $status;
    }
}
