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
	public $products;
	public $subscription;
	public $totalSubscriptionPrice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, $locale, $products, $subscription, $totalSubscriptionPrice)
    {
		$this->locale = $locale;
        $this->user = $notifiable;
		$this->products = $products;
		$this->subscription = $subscription;
		$this->totalSubscriptionPrice = $totalSubscriptionPrice;
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
