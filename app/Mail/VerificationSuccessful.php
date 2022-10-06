<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

use App\Models\User;
use App\Services\GetUserLocaleService;

class VerificationSuccessful extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;
    public $policyUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable)
    {
        $this->user = $notifiable;
        $this->url = config('app.webpanel_url');
        $this->policyUrl = config('app.webpanel_url') . '/terms-and-conditions';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('emails.' . App::currentLocale() . '.verification-successful-mail');
        return $this->from(config('mail.noreply'))
        ->markdown('emails.' . GetUserLocaleService::getLocale($this->user) . '.verification-successful-mail');
    }
}
