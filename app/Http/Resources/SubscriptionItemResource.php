<?php

namespace App\Http\Resources;

use Laravel\Cashier\Subscription;
use App\Models\BillingAddress;
use Illuminate\Http\Resources\Json\JsonResource;
use Stripe\StripeClient;

class SubscriptionItemResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		return [
			"id" => $this->id,
			"type" => "SubscriptionItem",
			"attributes" => [
				"subscription" => new SubscriptionResource($this->subscription),
				"stripe_id" => $this->stripe_id,
				"stripe_product" => $this->stripe_product,
				"stripe_price" => $this->stripe_price,
				"quantity" => $this->quantity,
				"updated_at" => $this->updated_at,
				"created_at" => $this->updated_at
			]
		];
	}
}
