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
		$user = $this->user;
		$status = $this->status;
		$priority = $this->priority;
		$project = $this->project;

		return [
			"id" => $this->id,
			"type" => "Bug",
			"attributes" => [
				"project" => [
					"id" => $project->id,
					"designation" => $project->designation,
				],
				"user" => [
					"id" => $user->id,
					"first_name" => $user->first_name,
					"last_name" => $user->last_name,
				],
				"designation" => $this->designation,
				"description" => $this->description,
				"url" => $this->url,
				"status" => [
					"id" => $status->id,
					"designation" => $status->designation,
				],
				"priority" => [
					"id" => $priority->id,
					"designation" => $priority->designation,
				],
				"operating_system" => $this->operating_system,
				"browser" => $this->browser,
				"selector" => $this->selector,
				"resolution" => $this->resolution,
				"deadline" => $this->deadline,
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at,
			]
		];
	}
}
