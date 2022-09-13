<?php

namespace App\Http\Resources;

use App\Models\Organization;
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
		if($this->owner->billing_addressable_type == 'user') {
			$model = new UserResource($this->owner->billingAddressable());
		} else {
			$model = new OrganizationResource($this->owner->billingAddressable());
		}
		
		return [
			'id' => $this->id,
			'type' => 'Subscription',
			'attributes' => [
				'name' => $this->name,
				'owner' => [
					'id' => $this->owner->id,
					'billable' => $model,
					// 'first_name' => $this->owner->first_name,
					// 'last_name' => $this->owner->last_name,
					// 'stripe_id' => $this->owner->stripe_id,
					// 'pm_type' => $this->owner->pm_type,
					// 'pm_last_four' => $this->owner->pm_last_four,
					// 'trial_ends_at' => $this->owner->trial_ends_at,
					// TODO: Resource anpassen
					// "billing_addressable_id" => "AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA"
					// "billing_addressable_type" => "organization"
					// "street" => "string"
					// "housenumber" => "stringa"
					// "city" => "string"
					// "state" => "string"
					// "zip" => "string"
					// "country" => "string"
					// "tax_id" => "string"
					// "created_at" => "2022-09-07 06:53:51"
					// "updated_at" => "2022-09-12 08:35:00"
					// "deleted_at" => null
					// "stripe_id" => "cus_MO4HXllhP1dIsb"
					// "pm_type" => "visa"
					// "pm_last_four" => "4242"
					// "trial_ends_at" => null

				],
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
