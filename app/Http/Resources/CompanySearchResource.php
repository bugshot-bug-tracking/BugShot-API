<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanySearchResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$company = array(
			"id" => $this->id,
			"type" => "Company",
			"attributes" => [
				"organization_id" => $this->organization_id,
				"designation" => $this->designation
			]
		);

		return $company;
	}
}
