<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StripeCustomerResource extends JsonResource
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
			'type' => 'StripeCustomer',
			'attributes' => [
				'first_name' => $this->first_name,
				'last_name' => $this->last_name,
				'stripe_id' => $this->stripe_id,
				'pm_type' => $this->pm_type,
				'pm_last_four' => $this->pm_last_four,
				'trial_ends_at' => $this->trial_ends_at,
			]
		];
	}
}


