<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
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
			'type' => 'Price',
			'attributes' => [
				'product' => $this->product,
				'active' => $this->active,
				'billing_scheme' => $this->billing_scheme,
				'created' => $this->created,
				'currency' => $this->default_price,
				'custom_unit_amount' => $this->custom_unit_amount,
				'livemode' => $this->livemode,
				'metadata' => $this->metadata,
				'lookup_key' => $this->lookup_key,
				'recurring' => $this->recurring,
				'tax_behavior' => $this->tax_behavior,
				'tiers_mode' => $this->tiers_mode,
				'transform_quantity' => $this->transform_quantity,
				'type' => $this->type,
				'unit_amount' => $this->unit_amount,
				'updated' => $this->updated,
				'unit_amount_decimal' => $this->unit_amount_decimal
			]
		];
	}
}
