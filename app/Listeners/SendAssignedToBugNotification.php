<?php

namespace App\Listeners;

use App\Events\AssignedToBug;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\AssignedToBugNotification;
use App\Services\GetUserLocaleService;

class SendAssignedToBugNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  AssignedToBug  $event
     * @return void
     */
    public function handle(AssignedToBug $event)
    {
        $event->user->notify((new AssignedToBugNotification($event->bug))->locale(GetUserLocaleService::getLocale($event->user)));
    }
}
