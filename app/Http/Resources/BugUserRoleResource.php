<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BugUserRoleResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$bug = $this->bug;
		$user = $this->user;
		$role = $this->role;

		return [
			'bug' => new BugResource($bug),
			'user' => new UserResource($user),
			'role' => new RoleResource($role),
		];
	}
}
