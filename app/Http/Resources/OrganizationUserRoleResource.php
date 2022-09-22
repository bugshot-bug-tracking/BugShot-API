<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationUserRoleResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$organization = $this->organization;
		$user = $this->user;
		$role = $this->role;
		$subscription = $this->subscription;

		return [
			"organization" => new OrganizationResource($organization),
			"user" => new UserResource($user),
			"role" => new RoleResource($role),
			"subscription" => new SubscriptionResource($subscription)
		];
	}
}
