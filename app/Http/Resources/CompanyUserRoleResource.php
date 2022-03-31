<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyUserRoleResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$company = $this->company;
		$user = $this->user;
		$role = $this->role;

		return [
			"company" => new CompanyResource($company),
			"user" => new UserResource($user),
			"role" => new RoleResource($role),
		];
	}
}
