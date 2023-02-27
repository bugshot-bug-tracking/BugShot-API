<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Stripe\StripeClient;

class ProductResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		// Get the corresponding prices of the selected product
		$stripe = new StripeClient(config('app.stripe_api_secret'));
	  	$prices = $stripe->prices->all(['product' => $this->id,'expand'=>['data.tiers']])->data;

		return [
			'id' => $this->id,
			'type' => 'Product',
			'attributes' => [
				'active' => $this->active,
				'attributes' => $this->attributes,
				'created' => $this->created,
				'default_price' => $this->default_price,
				'description' => $this->description,
				'images' => $this->images,
				'livemode' => $this->livemode,
				'metadata' => $this->metadata,
				'name' => $this->name,
				'package_dimensions' => $this->package_dimensions,
				'shippable' => $this->shippable,
				'statement_descriptor' => $this->statement_descriptor,
				'tax_code' => $this->tax_code,
				'type' => $this->type,
				'unit_label' => $this->unit_label,
				'updated' => $this->updated,
				'url' => $this->url,
				'prices' => PriceResource::collection($prices)
			]
		];
	}
}
