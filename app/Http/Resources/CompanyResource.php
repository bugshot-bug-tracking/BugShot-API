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
			/**
			 * Check if the user is a manager or owner in the organization or the company.
			 * If so, show him all the projects.
			 * If not, only show him the ones he is part of in any way
			*/
			if(Auth::user()->isPriviliegated('companies', $this->resource)) {
				$projects = $this->projects;
			} else {
				$createdProjects = Auth::user()->createdProjects->where('company_id', $this->id);
				$projects = Auth::user()->projects->where('company_id', $this->id);

				$projects = $createdProjects->concat($projects);
			}

			$company['attributes']['projects'] = ProjectResource::collection($projects);
		}

		// Check if the response should contain the respective company users
		if(array_key_exists('include-company-users', $header) && $header['include-company-users'][0] == "true") {
			if($this->resource->users->contains(Auth::user()) || Auth::user()->isPriviliegated('companies', $this->resource)) {
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
		}

		// Check if the response should contain the respective company image
		if(array_key_exists('include-company-image', $header) && $header['include-company-image'][0] == "true") {
			if($this->resource->users->contains(Auth::user()) || Auth::user()->isPriviliegated('companies', $this->resource)) {
				$image = $this->image;
				$company['attributes']['image'] = new ImageResource($image);
			}
		}

		// Check if the response should contain the respective user role within this company
		if(array_key_exists('include-company-role', $header) && $header['include-company-role'][0] == "true") {
            if($this->resource->users->contains(Auth::user()) || Auth::user()->isPriviliegated('companies', $this->resource)) {
                $userCompany = Auth::user()->companies()->find($this->id);

                if($userCompany == NULL) {
                    $userCompany = Auth::user()->createdCompanies()->find($this->id);
                    $role =  Role::find(1); // Owner
                } else {
                    $role = Role::find($userCompany->pivot->role_id);
                }

                $company['attributes']['role'] = new RoleResource($role);
            }
		}

		// Check if the response should contain the respective user role within this company
		if(array_key_exists('include-users-company-role', $header) && $header['include-users-company-role'][0] == "true") {
			if($this->resource->users->contains(Auth::user()) || Auth::user()->isPriviliegated('companies', $this->resource)) {
				$user = User::find($request->get('user_id'));

                if($this->user_id == $user->id) {
                    $company['attributes']['role'] = 'owner';
                } else {
					$companyUserRole = CompanyUserRole::where('user_id', $user->id)->where('company_id', $this->id)->first();
                    $company['attributes']['role'] = new RoleResource($companyUserRole->role);
                }
			}
		}

		// Check if the response should contain the respective projects of the given user
		if(array_key_exists('include-users-projects', $header) && $header['include-users-projects'][0] == "true") {

			// Check if the user is a manager or owner in the company.
			if(Auth::user()->isPriviliegated('companies', $this->resource)) {
				$createdProjects = $user->createdProjects->where('company_id', $this->id);
				$projects = $user->projects->where('company_id', $this->id);
				$projects = $createdProjects->concat($projects);
			} else {
				$createdProjects = $user->createdProjects->where('company_id', $this->id)->intersect(Auth::user()->projects); // Projects the user created where Auth::user() is in
				$projects = $user->projects->where('company_id', $this->id)->where('user_id', Auth::id()); // Projects the user is in where Auth::user() is the owner/manager
				$projectsOfBoth = $user->projects->where('company_id', $this->id)->intersect(Auth::user()->projects); // Projects where both the user and the Auth:user() are in
				$projects = $createdProjects->concat($projects);
				$projects = $projects->concat($projectsOfBoth);
			}

			$company['attributes']['projects'] = ProjectResource::collection($projects);
		}

		return $company;
	}
}
