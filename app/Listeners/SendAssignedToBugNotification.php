<?php

namespace App\Listeners;

use App\Events\BugMembersUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\AssignedToBugNotification;
use App\Services\GetUserLocaleService;
use Illuminate\Support\Facades\Auth;

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
		$event->user->notify((new AssignedToBugNotification($event->bug, $event->sender, now()->format('d.m.Y H:i')))->locale(GetUserLocaleService::getLocale($event->user)));
	}
}
