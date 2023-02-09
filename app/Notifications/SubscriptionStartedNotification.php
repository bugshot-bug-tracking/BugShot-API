<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\SubscriptionStarted as SubscriptionStartedMailable;
use Stripe\StripeClient;

class SubscriptionStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

	// The newly booked products
	public $products;

	// The calculated total price of the subscription
	public $totalSubscriptionPrice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $subscription)
    {
		$stripe = new StripeClient(config('app.stripe_api_secret'));

		// Retrieve the corresponding products
		$subscriptionItems = $stripe->subscriptionItems->all(['subscription' => $subscription->stripe_id])->data;
		foreach($subscriptionItems as $subscriptionItem) {
			$subscriptionItem->parent_product = $stripe->products->retrieve(
				$subscriptionItem->plan->product,
				[]
			);
		}
		$this->products = $subscriptionItems;

		// Retrieve the corresponding stripe subscription
		$this->subscription = $stripe->subscriptions->retrieve(
			$subscription->stripe_id,
			[]
		);

		$this->totalSubscriptionPrice = 0;
		foreach($this->products as $product) {
			$this->totalSubscriptionPrice = $this->totalSubscriptionPrice + $product->plan->amount * $product->quantity;
		}
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
        return (new SubscriptionStartedMailable($notifiable, $this->locale, $this->products, $this->subscription, $this->totalSubscriptionPrice))
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
