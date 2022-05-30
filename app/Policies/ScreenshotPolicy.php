<?php

namespace App\Policies;

// Miscellaneous, Helpers, ...
use Illuminate\Auth\Access\HandlesAuthorization;

// Models
use App\Models\Project;
use App\Models\User;

class ScreenshotPolicy
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
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Project $project)
    {
        // Check company role
        if($project->company->user_id == $user->id) {
            return true;
        }

        $company = $user->companies()->find($project->company);
        if ($company == NULL) {
            return false;
        }

        $role = $company->pivot->role_id;
        switch ($role) {
            case 1:
                return true;
                break;
        }

        // Check project role
        return $user->projects()->find($project) != NULL;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Project $project)
    {
        // Check company role
        if($project->company->user_id == $user->id) {
            return true;
        }

        $company = $user->companies()->find($project->company);
        if ($company == NULL) {
            return false;
        }

        $role = $company->pivot->role_id;
        switch ($role) {
            case 1:
                return true;
                break;
        }

        // Check project role
        return $user->projects()->find($project) != NULL;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Project $project)
    {
        // Check company role
        if($project->company->user_id == $user->id) {
            return true;
        }

        $company = $user->companies()->find($project->company);
        if ($company == NULL) {
            return false;
        }

        $role = $company->pivot->role_id;
        switch ($role) {
            case 1:
                return true;
                break;
        }

        // Check project role
        return $user->projects()->find($project) != NULL;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Project $project)
    {
        // Check company role
        if($project->company->user_id == $user->id) {
            return true;
        }

        $company = $user->companies()->find($project->company);
        if ($company == NULL) {
            return false;
        }

        $role = $company->pivot->role_id;
        switch ($role) {
            case 1:
                return true;
                break;
        }

        // Check project role
        return $user->projects()->find($project) != NULL;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Project $project)
    {
        // Check company role
        if($project->company->user_id == $user->id) {
            return true;
        }

        $company = $user->companies()->find($project->company);
        if ($company == NULL) {
            return false;
        }

        $role = $company->pivot->role_id;
        switch ($role) {
            case 1:
                return true;
                break;
        }

        // Check project role
        return $user->projects()->find($project) != NULL;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Project $project)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Project $project)
    {
        //
    }
}
