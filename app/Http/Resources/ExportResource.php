<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Bug;
use App\Models\User;
use App\Models\BugExportStatus;
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

		$exporter = User::find($this->exported_by);

		$export = array(
			"id" => $this->id,
			"type" => "Export",
			"attributes" => [
				"exporter" => new UserResource($exporter),
				"project" => array(
					"id" => $this->project->id,
					"designation" => $this->project->designation
				),
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at
			]
		);

		$header = $request->header();

		// Check if the response should contain the respective project-users
		if(array_key_exists('include-project-users', $header) && $header['include-project-users'][0] == "true") {
			$project = $this->project;
			$users = $project->users()
			->where(function($query) {
                $query->where('project_user_roles.role_id', '=', 0)
                      ->orWhere('project_user_roles.role_id', '=', 1);
            })
			->get();

			$export['attributes']['users'] = UserResource::collection($users);
		}

		// Check if the response should contain the respective bugs
		if(array_key_exists('include-bugs', $header) && $header['include-bugs'][0] == "true") {
			$bugs = $this->bugs->map(function ($item, $key) {
				return [
					'id' => $item->pivot->bug_id,
					'type' => 'BugExport',
					'attributes' => [
						"bug" => new BugResource(Bug::find($item->pivot->bug_id)),
						"status" => array(
							"id" => $item->pivot->status_id,
							"type" => "Status",
							"attributes" => [
								"designation" => BugExportStatus::find($item->pivot->status_id)->designation
							]
						),
						"time_estimation" => $item->pivot->time_estimation,
						"evaluated_by" => User::find($item->pivot->evaluated_by) ? new UserResource(User::find($item->pivot->evaluated_by)) : NULL
					]
				];
			});

			$export['attributes']['bugs'] = $bugs;
		}

		return $export;
	}
}
