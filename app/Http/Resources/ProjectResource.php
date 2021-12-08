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
		// Check if the response should contain the respective statuses
		$header = $request->header();
		$statuses = array_key_exists('include-statuses', $header) && $header['include-statuses'][0] == "true" ? $this->statuses : [];
	
		// Count the total and done bugs within this project
		$bugsTotal = $this->bugs->count();
		$bugsDone = $this->statuses->last()->bugs->count();
	
		return [
			"id" => $this->id,
			"type" => "Project",
			"attributes" => [
				"designation" => $this->designation,
				"url" => $this->url,
				"color_hex" => $this->color_hex,
				"company_id" => $this->company_id,
				"bugsTotal" => $bugsTotal,
				"bugsDone" => $bugsDone,
				"statuses" => StatusResource::collection($statuses)
			]
		];
	}
}
