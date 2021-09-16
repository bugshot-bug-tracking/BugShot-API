<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BugResource extends JsonResource
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
			"type" => "Bug",
			"attributes" => [
				"project_id" => $this->project_id,
				"user_id" => $this->user_id,
				"designation" => $this->designation,
				"description" => $this->description,
				"url" => $this->url,
				"status_id" => $this->status_id,
				"priority_id" => $this->priority_id,
				"operating_system" => $this->operating_system,
				"browser" => $this->browser,
				"selector" => $this->selector,
				"resolution" => $this->resolution,
				"deadline" => $this->deadline,
			]
		];
	}
}
