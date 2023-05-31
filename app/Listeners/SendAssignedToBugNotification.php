<?php

namespace App\Listeners;

use App\Events\BugMembersUpdated;
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
    public function handle(BugMembersUpdated $event)
    {
        $event->user->notify((new AssignedToBugNotification($event->bug, $event->sender, $event->assignedAt))->locale(GetUserLocaleService::getLocale($event->user)));
    }
}
