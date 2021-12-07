<?php

namespace App\Policies;

// Miscellaneous, Helpers, ...
use Illuminate\Auth\Access\HandlesAuthorization;

// Models
use App\Models\Bug;
use App\Models\Attachment;
use App\Models\User;

class AttachmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Bug $bug)
    {
        return $user->bugs()->find($bug) != NULL;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Bug $bug)
    {
        return $user->bugs()->find($bug) != NULL;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Bug $bug)
    {
        return $user->bugs()->find($bug) != NULL;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Bug $bug)
    {
        return $user->bugs()->find($bug) != NULL;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Bug $bug)
    {
        return $user->bugs()->find($bug) != NULL;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Bug $bug)
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
    public function forceDelete(User $user, Bug $bug)
    {
        //
    }
}
