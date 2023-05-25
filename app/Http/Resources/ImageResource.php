<?php

namespace App\Http\Resources;

use Exception;
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

		try {
			return [
				"type" => "Image",
				"attributes" => [
					"url" => config("app.url") . "/storage" . $this->url,
					// "base64" => $base64
				]
			];
		} catch (Exception $e) {
			return [];
		}
	}
}
