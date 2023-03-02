<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyUserRole;
use App\Models\OrganizationUserRole;

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

		$organizationUserRole = array(
			'user' => new UserResource($user),
			'assigned_on' => $this->assigned_on
		);

		$header = $request->header();

		// Check if the response should contain the respective role
		if(array_key_exists('include-users-organization-role', $header) && $header['include-users-organization-role'][0] == 'true') {
			if(Auth::user()->isPriviliegated('organizations', $organization)) {
				$organizationUserRole['role'] = new RoleResource($this->role);
			}
		}

		// Check if the response should contain the respective subscription
		if(array_key_exists('include-subscription-item', $header) && $header['include-subscription-item'][0] == 'true') {
			if(Auth::user()->isPriviliegated('organizations', $organization)) {
				$organizationUserRole['subscription'] = new SubscriptionItemResource($this->subscriptionItem);

				/**
				 * If the user does not have a subscription assigned to him in this organization, check if he has any subscriptions
				 * assigned to him in other organizations.
				 */

				if($this->subscriptionItem == NULL) {
					$altSubscriptionsOrganizationUserRoles = OrganizationUserRole::whereNotNull('subscription_item_id')->where('user_id', $this->user_id)
					->where("restricted_subscription_usage", 0)->get();

					if($altSubscriptionsOrganizationUserRoles != NULL) {
						$altSubscriptions = collect();

						foreach($altSubscriptionsOrganizationUserRoles as $item) {
							$altSubscriptions->push(array(
								"organization" => $item->organization->designation,
								// TODO: Get the id and name of the sub and proceed with the flow
								"subscription" => $item->subscriptionItem->subscription
							));
						}

						$organizationUserRole['alternative_subscriptions'] = $altSubscriptions;
					};
				}
			}
		}

		// Check if the response should contain the respective companies
		if(array_key_exists('include-users-companies', $header) && $header['include-users-companies'][0] == 'true') {

			// Check if the user is a manager or owner in the organization.
			if(Auth::user()->isPriviliegated('organizations', $organization)) {
				$createdCompanies = $user->createdCompanies->where('organization_id', $organization->id);
				$companies = $user->companies->where('organization_id', $organization->id);
				$companies = $createdCompanies->concat($companies);
			} else {
				$createdCompanies = $user->createdCompanies->where('organization_id', $organization->id)->intersect(Auth::user()->companies); // Companies the user created where Auth::user() is in
				$companies = $user->companies->where('organization_id', $organization->id)->where('user_id', Auth::id()); // Companies the user is in where Auth::user() is the owner/manager
				$companiesOfBoth = $user->companies->where('organization_id', $organization->id)->intersect(Auth::user()->companies); // Companies where both the user and the Auth:user() are in
				$companies = $createdCompanies->concat($companies);
				$companies = $companies->concat($companiesOfBoth);
			}

			$companies = CompanyResource::collection($companies);

			if(array_key_exists('include-users-company-role', $header) && $header['include-users-company-role'][0] == 'true') {
				$companyUserRoles = collect();
				foreach($companies as $company) {
					if($company->user_id == $user->id) {
						$role = "owner";
					} else {
						$role = CompanyUserRole::where('company_id', $company->id)->where('user_id', $user->id)->first()->role;
						$role = new RoleResource($role);
					}

					$companyUserRole = array(
						"company" => new CompanyResource($company),
						"role" => $role
					);

					$companyUserRoles->push($companyUserRole);
				}

				$companies = $companyUserRoles;
			}

			$organizationUserRole['companies'] = $companies;

		}

		return $organizationUserRole;
	}
}
