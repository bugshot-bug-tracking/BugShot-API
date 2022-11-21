<?php

namespace App\Policies;

// Miscellaneous, Helpers, ...
use Illuminate\Auth\Access\HandlesAuthorization;

// Models
use App\Models\User;
use App\Models\Company;
use App\Models\Organization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Roles:
     * | id | designation
     * |----|----------------------
     * | 1  | Manager
     * | 2  | Team
     * | 3  | Client (e.g. Customer)
    */

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability)
    {
        if ($user->isAdministrator()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Organization $organization)
    {
		if($organization->user_id == $user->id) {
            return true;
        }

        $organization = $user->organizations()->find($organization);
        if ($organization != NULL) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Company $company)
    {
        // Check company role
        if($company->user_id == $user->id) {
            return true;
        }

        $company = $user->companies()->find($company);
        if ($company == NULL) {
            return false;
        }

        $role = $company->pivot->role_id;
        switch ($role) {
            case 1:
                return true;
                break;
            case 2:
                return true;
                break;
            case 3:
                return true;
                break;

            default:
                return false;
                break;
        }

		// Check company role
        if($company->organization->user_id == $user->id) {
            return true;
        }

        $organization = $user->companies()->find($company->organization);
        if ($organization == NULL) {
            return false;
        }

        $role = $organization->pivot->role_id;
        switch ($role) {
            case 1:
                return true;
                break;
        }


    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Organization $organization)
    {
		// Check if user is the manager or owner of the parent organization
		if($user->isPriviliegated('organizations', $company->organization)) {
			return true;
		};
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Company $company)
    {
		// Check if user is the manager or owner of the company
		if($user->isPriviliegated('companies', $company)) {
			return true;
		};

		// Check if user is the manager or owner of the parent organization
		if($user->isPriviliegated('organizations', $company->organization)) {
			return true;
		};
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Company $company)
    {
        // Check if user is the owner
        if($company->user_id == $user->id) {
            return true;
        }

		// Check if user is the manager or owner of the parent organization
		if($user->isPriviliegated('organizations', $company->organization)) {
			return true;
		};
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Company $company)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Company $company)
    {
        //
    }

    /**
     * Determine whether the user can view the image of the the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewImage(User $user, Company $company)
    {
        //
    }

    /**
     * Determine whether the user can view the users of the the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewUsers(User $user, Company $company)
    {
        //
    }

    /**
     * Determine whether the user is authorized to update the users role in the given company
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateUserRole(User $user, Company $company)
    {
		// Check if user is the manager or owner of the company
		if($user->isPriviliegated('companies', $company)) {
			return true;
		};

		// Check if user is the manager or owner of the parent organization
		if($user->isPriviliegated('organizations', $company->organization)) {
			return true;
		};
    }

    /**
     * Determine whether the user can remove a user from the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function removeUser(User $user, Company $company)
    {
		// Check if user is the manager or owner of the company
		if($user->isPriviliegated('companies', $company)) {
			return true;
		};

		// Check if user is the manager or owner of the parent organization
		if($user->isPriviliegated('organizations', $company->organization)) {
			return true;
		};
    }

    /**
     * Determine whether the user can view the invitations of the the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewInvitations(User $user, Company $company)
    {
		// Check if user is the manager or owner of the company
		if($user->isPriviliegated('companies', $company)) {
			return true;
		};

		// Check if user is the manager or owner of the parent organization
		if($user->isPriviliegated('organizations', $company->organization)) {
			return true;
		};
    }

    /**
     * Determine whether the user can view the invitations of the the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function invite(User $user, Company $company)
    {
		// Check if user is the manager or owner of the company
		if($user->isPriviliegated('companies', $company)) {
			return true;
		};

		// Check if user is the manager or owner of the parent organization
		if($user->isPriviliegated('organizations', $company->organization)) {
			return true;
		};
    }
}
