<?php

namespace App\Http\Resources;

use Laravel\Cashier\Subscription;
use App\Models\BillingAddress;
use Illuminate\Http\Resources\Json\JsonResource;
use Stripe\StripeClient;

class SubscriptionResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$stripe = new StripeClient(config('app.stripe_api_secret'));
		$subscriptionItems = $stripe->subscriptionItems->all(['subscription' => $this->stripe_id]);
		foreach($subscriptionItems as $subscriptionItem) {
			$subscriptionItem->parent_product = $stripe->products->retrieve(
				$subscriptionItem->plan->product,
				[]
			);
		}

		return [
			'id' => $this->id,
			'type' => 'Subscription',
			'attributes' => [
				'name' => $this->name,
				'billable' => $this->owner,
				'stripe_id' => $this->stripe_id,
				'stripe_status' => $this->stripe_status,
				'products' => $subscriptionItems->data,
				'trial_ends_at' => $this->trial_ends_at,
				'ends_at' => $this->ends_at,
				'updated_at' => $this->updated_at,
				'created_at' => $this->updated_at
			]
		];
	}
}
