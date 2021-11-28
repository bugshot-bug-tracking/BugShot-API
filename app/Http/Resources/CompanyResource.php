<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		// $base64 = NULL;
		// if($this->image != NULL) {
		// 	$path = "storage" . $this->image->url;
		// 	$data = file_get_contents($path);
		// 	$base64 = base64_encode($data);
		// }

		return [
			"id" => $this->id,
			"type" => "Company",
			"attributes" => [
				"designation" => $this->designation,
				"color_hex" => $this->color_hex,
				// "base64" => $base64
				// "users" => $this->users
			]
		];
	}
}
