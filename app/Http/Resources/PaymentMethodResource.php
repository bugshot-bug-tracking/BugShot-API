<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$stripePaymentMethod = $this->asStripePaymentMethod();

		return [
			'id' => $stripePaymentMethod->id,
			'type' => 'PaymentMethod',
			'attributes' => [
				'billing_details' => $stripePaymentMethod->billing_details,
				'card' => $stripePaymentMethod->card,
				'customer_id' => $stripePaymentMethod->customer,
				'livemode' => $stripePaymentMethod->livemode,
				'metadata' => $stripePaymentMethod->metadata,
				'type' => $stripePaymentMethod->type
			]
		];
	}
}


