<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectUserRoleResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$project = $this->project;
		$user = $this->user;
		$role = $this->role;

		/**
		 * Specifies what status the user is in regard to the project 
		 * e.g.: A user can be removed from a company while still being part of a project in that company. He will not have access to the project though,
		 * as long as he is not invited back to the company. In this case the users status in regard to the project would'nt be "removed" but more like "deactivated"
		*/
		$company = $project->company;
        if($company->user_id == $user->id || $user->companies()->find($company) != NULL) {
			$status = NULL;
        } else {
			$status = $user->companies()->find($company) != NULL ? NULL : 'deactivated';
		}
		
		return [
			"project" => new ProjectResource($project),
			"user" => new UserResource($user),
			"role" => new RoleResource($role),
			"status" => $status
		];
	}
}
