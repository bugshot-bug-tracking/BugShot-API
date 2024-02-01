<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccessTokenResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$accessToken = array(
			"id" => $this->id,
			"type" => "AccessToken",
			"attributes" => [
				"access_token" => $this->access_token,
				"description" => $this->description,
				"project_id" => $this->project_id,
				"user_id" => $this->user_id,
				"created_at" => $this->created_at,
                "updated_at" => $this->updated_at
			]
		);

		return $accessToken;
	}
}
