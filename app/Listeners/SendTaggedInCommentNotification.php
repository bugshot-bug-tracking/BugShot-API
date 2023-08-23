<?php

namespace App\Listeners;

use App\Events\TaggedInComment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\TaggedInCommentNotification;
use App\Services\GetUserLocaleService;

class SendTaggedInCommentNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  TaggedInComment  $event
     * @return void
     */
    public function handle(TaggedInComment $event)
    {
		if($event->user->getSettingValueByName("user_settings_select_notifications") == "every_notification" || $event->user->getSettingValueByName("custom_notifications_tagged_in_comment") == "active") {
			$event->user->notify((new TaggedInCommentNotification($event->comment))->locale(GetUserLocaleService::getLocale($event->user)));
		}
    }
}
