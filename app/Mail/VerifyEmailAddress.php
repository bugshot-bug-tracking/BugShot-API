<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

use App\Models\User;

class VerifyEmailAddress extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, $url)
    {   
        // Gets the token of the generated url
        preg_match(
            '/[^\/]*$/',
            $url,
            $matches
        );

        $this->user = $notifiable;
        $this->url = config('app.webpanel_url') . '/auth/verify/' . $notifiable->id . '/' . $matches[0];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('emails.' . App::currentLocale() . '.verify-email-address-mail');
        return $this->from(config('mail.noreply'))
                ->markdown('emails.' . App::currentLocale() . '.verify-email-address-mail', [
                    'user' => $this->user,
                    'url' => $this->url,
                ]);
    }
}
