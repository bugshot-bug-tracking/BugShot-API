<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectSearchResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$project = array(
			"id" => $this->id,
			"type" => "Project",
			"attributes" => [
				"organization_id" => $this->company->organization_id,
				"group_id" => $this->company_id,
				"designation" => $this->designation,
				"url" => $this->url
			]
		);

		return $project;
	}
}
