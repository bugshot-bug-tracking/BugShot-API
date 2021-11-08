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
		// Get and transform the stored company image back to a base64 if it exists
		$base64 = NULL;
		if($this->image_path != NULL) {
			$path = "storage" . $this->image_path;
			$data = file_get_contents($path);
			$base64 = base64_encode($data);
		}

		return [
			"id" => $this->id,
			"type" => "Company",
			"attributes" => [
				"designation" => $this->designation,
				"base64" => $base64,
				"color_hex" => $this->color_hex
			]
		];
	}
}
