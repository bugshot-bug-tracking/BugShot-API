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

		return [
			"project" => new ProjectResource($project),
			"user" => new UserResource($user),
			"role" => new RoleResource($role),
		];
	}
}
