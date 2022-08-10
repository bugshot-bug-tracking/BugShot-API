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
		$user = $this->user;
		$setting = $this->setting;
		$value = $this->value;

		return [
			"id" => $this->id,
			"type" => "SettingUserValue",
			"attributes" => [
				"user" => new UserResource($user),
				"setting" => new SettingResource($setting),
				"value" => new ValueResource($value)
			]
		];
	}
}
