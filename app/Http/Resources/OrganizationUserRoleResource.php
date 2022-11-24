<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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

		$organizationUserRole = array(
			"organization" => new OrganizationResource($organization),
			"user" => new UserResource($user),
			"role" => new RoleResource($role),
			"subscription" => new SubscriptionResource($subscription)
		);


		$header = $request->header();

		// Check if the response should contain the respective companies
		if(array_key_exists('include-users-companies', $header) && $header['include-users-companies'][0] == "true") {
			if(array_key_exists('include-users-company-role', $header) && $header['include-users-company-role'][0] == "true") {
				$request->request->add(['user_id' => $user->id]);
			}

			// Check if the user is a manager or owner in the organization.
			if(Auth::user()->isPriviliegated('organizations', $this->organization)) {
				$createdCompanies = $user->createdCompanies->where('organization_id', $organization->id);
				$companies = $user->companies->where('organization_id', $organization->id);
				$companies = $createdCompanies->concat($companies);
			} else {
				$createdCompanies = $user->createdCompanies->where('organization_id', $organization->id)->intersect(Auth::user()->companies); // Companies the user created where Auth::user() is in
				$companies = $user->companies->where('organization_id', $organization->id)->where('user_id', Auth::id()); // Companies the user is in where Auth::user() is the owner/manager
				$companiesIOfBoth = $user->companies->where('organization_id', $organization->id)->intersect(Auth::user()->companies); // Companies where both the user and the Auth:user() are in
				$companies = $createdCompanies->concat($companies);
				$companies = $companies->concat($companiesIOfBoth);
			}

			$organizationUserRole['attributes']['companies'] = CompanyResource::collection($companies);
		}

		return $organizationUserRole;
	}
}
