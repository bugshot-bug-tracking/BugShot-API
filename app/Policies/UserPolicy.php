<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

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
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $requestedUser)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $requestedUser)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $requestedUser)
    {
        return $user->id == $requestedUser->id;    
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user)
    {
        //
    }

    /**
     * Determine whether the user can check if the url belongs to a project of the user
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function checkProject(User $user, User $requestedUser)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine whether the user can show the balance of the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $requestedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function showBalance(User $user, User $requestedUser)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine whether the user can retrieve the setup intent form.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $requestedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function showSetupIntentForm(User $user, User $requestedUser)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine whether the user can create a new subscription
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $requestedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createSubscription(User $user, User $requestedUser)
    {
        return $user->id == $requestedUser->id;
    }
}