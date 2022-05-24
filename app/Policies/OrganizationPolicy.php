<?php

namespace App\Policies;

// Miscellaneous, Helpers, ...
use Illuminate\Auth\Access\HandlesAuthorization;

// Models
use App\Models\User;
use App\Models\Organization;

class OrganizationPolicy
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
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Organization $organization)
    {
        return $user->organizations()->find($organization) != NULL;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Organization $organization)
    {
        $organization = $user->organizations()->find($organization);
        if ($organization == NULL) {
            return false;
        }

        $role = $organization->pivot->role_id;

        switch ($role) {
            case 1:
                return true;
                break;
            case 2:
                return true;
                break;
            
            default:
                return false;
                break;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Organization $organization)
    {
        $organization = $user->organizations()->find($organization);
        if ($organization == NULL) {
            return false;
        }
        
        $role = $organization->pivot->role_id;

        switch ($role) {
            case 1:
                return true;
                break;
            
            default:
                return false;
                break;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Organization $organization)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Organization $organization)
    {
        //
    }

    /**
     * Determine whether the user can view the image of the the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewImage(User $user, Organization $organization)
    {
        return $user->organizations()->find($organization) != NULL;
    }

    /**
     * Determine whether the user can view the users of the the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewUsers(User $user, Organization $organization)
    {
        $organization = $user->organizations()->find($organization);
        if ($organization == NULL) {
            return false;
        }
        
        $role = $organization->pivot->role_id;

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
            case 4:
                return true;
                break;
            
            default:
                return false;
                break;
        }
    }

    /**
     * Determine whether the user can remove a user from the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function removeUser(User $user, Organization $organization)
    {
        $organization = $user->organizations()->find($organization);
        if ($organization == NULL) {
            return false;
        }
        
        $role = $organization->pivot->role_id;

        switch ($role) {
            case 1:
                return true;
                break;
            case 2:
                return true;
                break;

            default:
                return false;
                break;
        }
    }

    /**
     * Determine whether the user can view the invitations of the the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewInvitations(User $user, Organization $organization)
    {
        $organization = $user->organizations()->find($organization);
        if ($organization == NULL) {
            return false;
        }
        
        $role = $organization->pivot->role_id;

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
            case 4:
                return true;
                break;
            
            default:
                return false;
                break;
        }
    }

    /**
     * Determine whether the user can view the invitations of the the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function invite(User $user, Organization $organization)
    {
        $organization = $user->organizations()->find($organization);
        if ($organization == NULL) {
            return false;
        }
        
        $role = $organization->pivot->role_id;

        switch ($role) {
            case 1:
                return true;
                break;
            case 2:
                return true;
                break;
            
            default:
                return false;
                break;
        }
    }
}
