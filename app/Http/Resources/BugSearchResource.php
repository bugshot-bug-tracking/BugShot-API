<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BugSearchResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$bug = array(
			"id" => $this->id,
			"type" => "Bug",
			"attributes" => [
				"organization_id" => $this->project->company->organization_id,
				"group_id" => $this->project->company_id,
				"project_id" => $this->project_id,
				"designation" => $this->designation,
				"description" => $this->description,
				"url" => $this->url
			]
		);

		return $bug;
	}
}
