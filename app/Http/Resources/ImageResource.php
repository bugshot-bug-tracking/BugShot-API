<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		if ($this->resource == NULL) {
			return null;
		}

		$path = "storage" . $this->url;
		$data = file_get_contents($path);
		$base64 = base64_encode($data);

		return [
			"type" => "Image",
			"attributes" => [
				"url" => $this->url,
				"base64" => $base64
			]
		];
	}
}
