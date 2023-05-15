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
		$project = Project::find($this->project_id);

		$export = array(
			"id" => $this->id,
			"type" => "Export",
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

		$header = $request->header();

		// Check if the response should contain the respective project
		if(array_key_exists('include-project', $header) && $header['include-project'][0] == "true") {
			$export['attributes']['projects'] = ProjectResource::collection($this->project);
		}

		// Check if the response should contain the respective project-users
		if(array_key_exists('include-project-users', $header) && $header['include-project-users'][0] == "true") {
			$project = $this->project;
			$users = $project->users()
			->orWherePivot('role_id', 0)
			->orWherePivot('role_id', 1)
			->get();

			$export['attributes']['users'] = UserResource::collection($users);
		}

		return $export;
	}
}
