<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatabaseNotificationPolicy
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
		return true;
    }

    /**
     * Determine whether the user can view the model.
     *
	 * @param  \App\Models\User  $user
     * @param  $notification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, $notification)
    {
		$notification = $user->notifications()->find($notification);

        return $notification ? true : false;
    }

    /**
     * Determine whether the user can create models.
     *
	 * @param  \App\Models\User  $user
     * @param  $notification
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
     * @param  $notification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, $notification)
    {
		$notification = $user->notifications()->find($notification);

        return $notification ? true : false;
    }

    /**
     * Determine whether the user can delete the model.
     *
	 * @param  \App\Models\User  $user
     * @param  $notification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, $notification)
    {
		$notification = $user->notifications()->find($notification->id);

        return $notification ? true : false;
    }

	/**
     * Determine whether the user can delete the models.
     *
	 * @param  \App\Models\User  $user
     * @param  $notification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteAll(User $user, $notification)
    {
		return true;
    }
}
