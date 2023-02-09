<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\ProjectUserRole;

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
		// Count the total and done bugs within this project
		$statuses = $this->statuses;
		$bugsDone = $statuses->last()->bugs->count();
		$bugsTotal = $this->bugs->count();
		$company = Company::find($this->company_id);

		$project = array(
			"id" => $this->id,
			"type" => "Project",
			"attributes" => [
				"creator" => new UserResource(User::find($this->user_id)),
				"designation" => $this->designation,
				"url" => $this->url,
				"color_hex" => $this->color_hex,
				"company" => array(
					"id" => $company->id,
					"type" => "Company",
					"attributes" => [
						"creator" => new UserResource(User::find($company->user_id)),
						"designation" => $company->designation,
						"color_hex" => $company->color_hex,
					]
				),
				"bugsTotal" => $bugsTotal,
				"bugsDone" => $bugsDone,
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at
			]
		);

		$header = $request->header();

		// Check if the response should contain the respective statuses
		if(array_key_exists('include-statuses', $header) && $header['include-statuses'][0] == "true") {
			$project['attributes']['statuses'] = StatusResource::collection($statuses);
		}

		// Check if the response should contain the respective project users
		if(array_key_exists('include-project-users', $header) && $header['include-project-users'][0] == "true") {
			if(array_key_exists('include-project-users-roles', $header) && $header['include-project-users'][0] == "true") {
				$projectUserRoles = ProjectUserRole::where("project_id", $this->id)
				->with('user')
				->with('role')
				->get();

				$project['attributes']['users'] = $projectUserRoles->map(function ($item, $key) {
					return [
						'id' => $item->user->id,
						'type' => 'User',
						'attributes' => [
							"first_name" => $item->user->first_name,
							"last_name" => $item->user->last_name,
							"email" => $item->user->email,
						],
						'role' => new RoleResource($item->role)
					];
				});
			} else {
				$users = $this->users;
				$project['attributes']['users'] = UserResource::collection($users);
			}
		}

		if(array_key_exists('include-project-users-with-owner', $header) && $header['include-project-users-with-owner'][0] == "true"){
			$users = $this->users;
			$returnCollections = UserResource::collection($users);
			$returnCollections = $returnCollections->push(new UserResource(User::find($this->user_id)));
			$project['attributes']['users'] = $returnCollections;
		}

		// Check if the response should contain the respective project image
		if(array_key_exists('include-project-image', $header) && $header['include-project-image'][0] == "true") {
			$image = $this->image;
			$project['attributes']['image'] = new ImageResource($image);
		}

		// Check if the response should contain the respective user role within this project
		if(array_key_exists('include-project-role', $header) && $header['include-project-role'][0] == "true") {
			$userProject = Auth::user()->projects()->find($this->id);

			if($userProject == NULL) {
				$userProject = Auth::user()->createdProjects()->find($this->id);
				$role =  Role::find(1); // Owner
			} else {
				$role = Role::find($userProject->pivot->role_id);
			}

			$project['attributes']['role'] = new RoleResource($role);
		}

		return $project;
	}
}
