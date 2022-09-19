<?php

namespace App\Http\Resources;

use Laravel\Cashier\Subscription;
use App\Models\BillingAddress;
use Illuminate\Http\Resources\Json\JsonResource;

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
		return [
			'id' => $this->id,
			'type' => 'Subscription',
			'attributes' => [
				'name' => $this->name,
				'billable' => $this->owner,
				'stripe_id' => $this->stripe_id,
				'stripe_status' => $this->stripe_status,
				'stripe_price' => $this->stripe_price,
				'quantity' => $this->quantity,
				'trial_ends_at' => $this->trial_ends_at,
				'ends_at' => $this->ends_at,
				'updated_at' => $this->updated_at,
				'created_at' => $this->updated_at
			]
		];
	}
}
