<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingUserValueResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$setting = $this->setting;
		$value = $this->value;

		return [
			"id" => $this->id,
			"type" => "SettingUserValue",
			"attributes" => [
				"setting" => new SettingResource($setting),
				"value" => new ValueResource($value)
			]
		];
	}
}
