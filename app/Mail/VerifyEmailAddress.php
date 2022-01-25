<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\User;

class VerifyEmailAddress extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;

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
        return $this->view('emails.verify-email-address-mail');
    }
}
