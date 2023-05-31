<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class ApprovalReportUnregisteredUser extends Mailable
{
    use Queueable, SerializesModels;

	public $locale;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $status = $this->from(config('mail.noreply'))
        ->markdown('emails.' . $this->locale . '.unregistered-user-approval-report-mail');

		return $status;
    }
}
