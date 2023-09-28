<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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

		/**
		 * Specifies what status the user is in regard to the project
		 * e.g.: A user can be removed from a company while still being part of a project in that company. He will not have access to the project though,
		 * as long as he is not invited back to the company. In this case the users status in regard to the project would'nt be 'removed' but more like 'deactivated'
		*/
		$company = $project->company;
        if($company->user_id == $user->id || $user->companies()->find($company) != NULL) {
			$status = NULL;
        } else {
			$status = $user->companies()->find($company) != NULL ? NULL : 'deactivated';
		}

		$projectUserRole = array(
			'user' => new UserResource($user),
			'status' => $status,
			'is_favorite' => $this->is_favorite
		);

		$header = $request->header();

		// Check if the response should contain the respective role
		if(array_key_exists('include-roles', $header) && $header['include-roles'][0] == 'true') {
			if(Auth::user()->isPriviliegated('projects', $project)) {
				$projectUserRole['role'] = new RoleResource($this->role);
			}
		}

		// Check if the response should contain the respective role
		if(array_key_exists('include-users-project-role', $header) && $header['include-users-project-role'][0] == 'true') {
			if(Auth::user()->isPriviliegated('projects', $project)) {
				$projectUserRole['role'] = new RoleResource($this->role);
			}
		}

		return $projectUserRole;
	}
}
