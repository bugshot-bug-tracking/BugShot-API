<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use App\Models\User;

class SubscriptionStarted extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
	public $locale;
	public $product;
	public $price;
	public $subscription;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, $locale, $product, $price, $subscription)
    {
		$this->locale = $locale;
        $this->user = $notifiable;
		$this->product = $product;
		$this->price = $price;
		$this->subscription = $subscription;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.noreply'))
        ->markdown('emails.' . $this->locale . '.subscription-started-mail');
    }
}
