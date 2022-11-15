<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\CompanyUserRole;

class CompanyResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$organization = Organization::find($this->organization_id);

		$company = array(
			"id" => $this->id,
			"type" => "Company",
			"attributes" => [
				"creator" => new UserResource(User::find($this->user_id)),
				"designation" => $this->designation,
				"color_hex" => $this->color_hex,
				"organization" => array(
					"id" => $organization->id,
					"type" => "Organization",
					"attributes" => [
						"creator" => new UserResource(User::find($organization->user_id)),
						"designation" => $organization->designation,
						"color_hex" => $organization->color_hex,
					]
				),
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at
			]
		);

		$header = $request->header();

		// Check if the response should contain the respective projects
		if(array_key_exists('include-projects', $header) && $header['include-projects'][0] == "true") {
			$projects = Auth::user()->projects->where('company_id', $this->id);
			$createdProjects = Auth::user()->createdProjects->where('company_id', $this->id);
			$projects = $projects->concat($createdProjects);

			$company['attributes']['projects'] = ProjectResource::collection($projects);
		}

		// Check if the response should contain the respective company users
		if(array_key_exists('include-company-users', $header) && $header['include-company-users'][0] == "true") {
			if(array_key_exists('include-company-users-roles', $header) && $header['include-company-users'][0] == "true") {
				$companyUserRoles = CompanyUserRole::where("company_id", $this->id)
				->with('user')
				->with('role')
				->get();

				$company['attributes']['users'] = $companyUserRoles->map(function ($item, $key) {
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
				$company['attributes']['users'] = UserResource::collection($users);
			}
		}

		// Check if the response should contain the respective company image
		if(array_key_exists('include-company-image', $header) && $header['include-company-image'][0] == "true") {
			$image = $this->image;
			$company['attributes']['image'] = new ImageResource($image);
		}

		// Check if the response should contain the respective user role within this company
		if(array_key_exists('include-company-role', $header) && $header['include-company-role'][0] == "true") {
			$userCompany = Auth::user()->companies()->find($this->id);

			if($userCompany == NULL) {
				$userCompany = Auth::user()->createdCompanies()->find($this->id);
				$role =  Role::find(1); // Owner
			} else {
				$role = Role::find($userCompany->pivot->role_id);
			}

			$company['attributes']['role'] = new RoleResource($role);
		}

		return $company;
	}
}
