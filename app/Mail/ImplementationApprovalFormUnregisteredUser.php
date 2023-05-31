<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use App\Services\GetUserLocaleService;

use App\Models\Export;

class ImplementationApprovalFormUnregisteredUser extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
	public $locale;
    public $export;
	public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($locale, Export $export, $url)
    {
        $this->locale = $locale;
        $this->export = $export;
		$this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $status = $this->from(config('mail.noreply'))
        ->markdown('emails.' . $this->locale . '.unregistered-user-implementation-approval-form-mail');

		return $status;
    }
}
