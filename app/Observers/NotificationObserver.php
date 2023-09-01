<?php

namespace App\Observers;

use App\Events\NotificationCreated;
use Illuminate\Notifications\DatabaseNotification as Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationObserver
{
    /**
     * Handle the Notification "created" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function created(Notification $notification)
    {
        broadcast(new NotificationCreated($notification->notifiable))->toOthers();
    }
}
