<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\SubscriptionStarted as SubscriptionStartedMailable;
use Stripe\StripeClient;

class SubscriptionStartedNotification extends Notification
{
    use Queueable;

	// The newly booked product
	public $product;

	// The price the product was booked for
	public $price;

	// The subscription itself
	public $subscription;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
		$stripe = new StripeClient(config('app.stripe_api_secret'));

		// Retrieve the price the product was booked for
		$this->price = $stripe->prices->retrieve(
			$subscription->stripe_price,
			[]
		);

		// Retrieve the corresponding product
		$this->product = $stripe->products->retrieve(
			$this->price->product,
			[]
		);

		// Retrieve the subscription
		$this->subscription = $stripe->subscriptions->retrieve(
			$subscription->stripe_id,
			[]
		);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new SubscriptionStartedMailable($notifiable, $this->locale, $this->product, $this->price, $this->subscription))
        ->subject('BugShot - ' . __('email.subscription-started', [], $this->locale))
        ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
