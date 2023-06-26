<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\Notification  $notification
     * @param  string  $ability
     * @return void|bool
     */
    public function before($notification, $ability)
    {
        if ($notification->isAdministrator()) {
            return true;
        }
    }

    /**
     * Determine whether the notification can view any models.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny($notification, User $requestedUser)
    {
        return $notification->notifiable == $requestedUser->id;
    }

    /**
     * Determine whether the notification can view the model.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view($notification, User $user)
    {
        return $notification->notifiable_id == $user->id;
    }

    /**
     * Determine whether the notification can create models.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create($notification)
    {
        return false;
    }

    /**
     * Determine whether the notification can update the model.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update($notification, User $user)
    {
        return $notification->notifiable_id == $user->id;
    }

    /**
     * Determine whether the notification can delete the model.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete($notification, User $user)
    {
        return $notification->notifiable_id == $user->id;
    }
}
