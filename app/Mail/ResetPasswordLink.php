<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

use App\Models\User;
use App\Services\GetUserLocaleService;

class ResetPasswordLink extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
	public $locale;
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, $locale, $url)
    {
        $this->locale = $locale;
        $this->user = $notifiable;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('emails.' . App::currentLocale() . '.reset-password-link-mail');
        return $this->from(config('mail.noreply'))
        ->markdown('emails.' . $this->locale . '.reset-password-link-mail');
    }
}
