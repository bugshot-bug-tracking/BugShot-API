<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExportResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$export = array(
			"id" => $this->id,
			"type" => "BugExport",
			"attributes" => [
				"exporter" => new UserResource(User::find($this->exported_by)),
				"project" => array(
					"id" => $project->id,
					"type" => "Project",
					"attributes" => [
						"creator" => new UserResource(User::find($project->user_id)),
						"designation" => $project->designation,
						"color_hex" => $project->color_hex
					]
				),
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at
			]
		);

		return $export;
	}
}
