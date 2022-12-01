<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectUserRole;

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

		$companyUserRole = array(
			'user' => new UserResource($user)
		);

		$header = $request->header();

		// Check if the response should contain the respective role
		if(array_key_exists('include-users-company-role', $header) && $header['include-users-company-role'][0] == 'true') {
			if(Auth::user()->isPriviliegated('companies', $company)) {
				$companyUserRole['role'] = new RoleResource($this->role);
			}
		}

		// Check if the response should contain the respective projects
		if(array_key_exists('include-users-projects', $header) && $header['include-users-projects'][0] == 'true') {

			// Check if the user is a manager or owner in the company.
			if(Auth::user()->isPriviliegated('companies', $company)) {
				$createdProjects = $user->createdProjects->where('company_id', $company->id);
				$projects = $user->projects->where('company_id', $company->id);
				$projects = $createdProjects->concat($projects);
			} else {
				$createdProjects = $user->createdProjects->where('company_id', $company->id)->intersect(Auth::user()->projects); // projects the user created where Auth::user() is in
				$projects = $user->projects->where('company_id', $company->id)->where('user_id', Auth::id()); // projects the user is in where Auth::user() is the owner/manager
				$projectsOfBoth = $user->projects->where('company_id', $company->id)->intersect(Auth::user()->projects); // projects where both the user and the Auth:user() are in
				$projects = $createdProjects->concat($projects);
				$projects = $projects->concat($projectsOfBoth);
			}

			$organizationUserRole['projects'] = $projects;

		}

		return $companyUserRole;
	}
}
