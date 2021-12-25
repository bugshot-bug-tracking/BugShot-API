<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScreenshotResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$path = "storage" . $this->url;
		$data = file_get_contents($path);
		$base64 = base64_encode($data);

		return [
			"id" => $this->id,
			"type" => "Screenshot",
			"attributes" => [
				"bug_id" => $this->bug_id,
				"url" => $this->url,
				"position_x" => $this->position_x,
				"position_y" => $this->position_y,
				"web_position_x" => $this->web_position_x,
				"web_position_y" => $this->web_position_y,
				"base64" => $base64
			]
		];
	}
}
