<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Role;
use App\Models\OrganizationUserRole;
use Laravel\Cashier\SubscriptionItem;

class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
		$organization = array(
			'id' => $this->id,
			'type' => 'Organization',
			'attributes' => [
				'creator' => new UserResource(User::find($this->user_id)),
				'designation' => $this->designation,
				'created_at' => $this->created_at,
				'updated_at' => $this->updated_at
			]
		);

		$header = $request->header();

		// Check if the response should contain the respective organization users
		if(array_key_exists('include-organization-users', $header) && $header['include-organization-users'][0] == "true") {
			if($this->resource->users->contains(Auth::user()) || Auth::user()->isPriviliegated('organizations', $this)) {
				if(array_key_exists('include-organization-users-roles', $header) && $header['include-organization-users'][0] == "true") {
					$organizationUserRoles = OrganizationUserRole::where("organization_id", $this->id)
					->with('user')
					->with('role')
					->get();

					$organization['attributes']['users'] = $organizationUserRoles->map(function ($item, $key) {
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
					$organization['attributes']['users'] = UserResource::collection($users);
				}
			}
		}

		// Check if the response should contain the respective user role within this organization
		if(array_key_exists('include-organization-role', $header) && $header['include-organization-role'][0] == "true") {
            if($this->resource->users->contains(Auth::user()) || Auth::user()->isPriviliegated('organizations', $this)) {
                $userOrganization = Auth::user()->organizations()->find($this->id);

                if($userOrganization == NULL) {
                    $userOrganization = Auth::user()->createdOrganizations()->find($this->id);
                    $role = Role::find(1); // Owner
                } else {
                    $role = Role::find($userOrganization->pivot->role_id);
                }

                $organization['attributes']['role'] = new RoleResource($role);
            }
		}

		// Check if the response should contain the respective subscription
		// if(array_key_exists('include-creator-subscription', $header) && $header['include-creator-subscription'][0] == 'true') {
		// 	if(Auth::user()->isPriviliegated('organizations', $this)) {
		// 		$organizationsUserRole = OrganizationUserRole::where("user_id", $this->user_id)->first();
		// 		$subscriptionItem = SubscriptionItem::where('stripe_id', $organizationsUserRole->subscription_item_id)->first();

		// 		$organization['attributes']['subscription_item'] = new SubscriptionItemResource($subscriptionItem);
		// 	}
		// }

		// Check if the response should contain the respective user role within this organization
		if(array_key_exists('include-organization-subscription', $header) && $header['include-organization-subscription'][0] == "true") {
            $userOrganization = Auth::user()->organizations()->find($this->id);

			if(Auth::user()->isPriviliegated('organizations', $this)) {
				$subscriptionItem = SubscriptionItem::where('stripe_id', $userOrganization->pivot->subscription_item_id)->first();
			} else if ($this->resource->users->contains(Auth::user())) {
				if($userOrganization != NULL) {
					$subscriptionItem = SubscriptionItem::where('stripe_id', $userOrganization->pivot->subscription_item_id)->first();
				}
			}

			$organization['attributes']['subscription_item'] = new SubscriptionItemResource($subscriptionItem);
		}

		// Check if the response should contain the respective projects
		if(array_key_exists('include-companies', $header) && $header['include-companies'][0] == "true") {
			$companies = NULL;
			/**
			 * Check if the user is a manager or owner in the organization.
			 * If so, show him all the companies.
			 * If not, only show him the ones he is part of in any way
			*/
			if(Auth::user()->isPriviliegated('organizations', $this->resource)) {
				$companies = $this->companies;
			} else {
				$companies = Auth::user()->companies->where('organization_id', $this->id);
				$createdCompanies = Auth::user()->createdCompanies->where('organization_id', $this->id);
				$companies = $companies->concat($createdCompanies);
			};

			$organization['attributes']['companies'] = CompanyResource::collection($companies);
		}

		// Check if the response should contain the respective company users
		if(array_key_exists('include-company-users', $header) && $header['include-company-users'][0] == "true") {
			if($this->resource->users->contains(Auth::user()) || Auth::user()->isPriviliegated('organizations', $this)) {
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
			if($this->resource->users->contains(Auth::user()) || Auth::user()->isPriviliegated('organizations', $this)) {
				$image = $this->image;
				$company['attributes']['image'] = new ImageResource($image);
			}
		}

		// Check if the response should contain the respective user role within this company
		if(array_key_exists('include-company-role', $header) && $header['include-company-role'][0] == "true") {
            if($this->resource->users->contains(Auth::user()) || Auth::user()->isPriviliegated('organizations', $this)) {
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

		return $organization;
    }
}
