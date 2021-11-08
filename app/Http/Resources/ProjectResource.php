<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
			"type" => "Project",
			"attributes" => [
				"designation" => $this->designation,
				"url" => $this->url,
				"company" => [
					"id" => $this->company_id,
					"designation" => $this->company->designation,
					"image_path" => $this->image_path,
					"color_hex" => $this->color_hex
				],
				"image_path" => $this->image_path,
				"color_hex" => $this->color_hex
			]
		];
	}
}
