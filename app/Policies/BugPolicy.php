<?php

namespace App\Policies;

// Miscellaneous, Helpers, ...
use Illuminate\Auth\Access\HandlesAuthorization;

// Models
use App\Models\Project;
use App\Models\Bug;
use App\Models\User;

class BugPolicy
{
    use HandlesAuthorization;

    /**
     * Roles:
     * | id | designation
     * |----|----------------------
     * | 1  | Admin
     * | 2  | Owner
     * | 3  | Company Manager
     * | 4  | Project Manager
     * | 5  | Developer
     * | 6  | Client (e.g. Customer)
     */

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Project $project)
    {
        return $user->projects()->find($project) != NULL;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Project $project)
    {
        return $user->projects()->find($project) != NULL;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Project $project)
    {
        return $user->projects()->find($project) != NULL;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Project $project)
    {
        $project = $user->projects()->find($project);
        if ($project == NULL) {
            return false;
        }

        $role = $project->pivot->role_id;

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
            case 5:
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
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Project $project)
    {
        $project = $user->projects()->find($project);
        if ($project == NULL) {
            return false;
        }

        $role = $project->pivot->role_id;

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
            case 5:
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
     * @param  \App\Models\Bug  $bug
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
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Project $project)
    {
        //
    }

    /**
     * Determine whether the user can assign a user to the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function assignUser(User $user, Bug $bug)
    {
        $bug = $user->bugs()->find($bug);
        if ($bug == NULL) {
            return false;
        }
        
        $role = $bug->pivot->role_id;
    
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
}
