<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
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
			"type" => "Status",
			"attributes" => [
				"designation" => $this->designation,
				"order_number" => $this->order_number,
				"project_id" => $this->project_id
				// "bugs" => $this->bugs
			]
		];
	}
}
