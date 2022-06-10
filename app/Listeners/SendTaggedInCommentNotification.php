<?php

namespace App\Listeners;

use App\Events\TaggedInComment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\TaggedInCommentNotification;

class SendTaggedInCommentNotification
{
    /**
     * Handle the event.
     *
     * @param  TaggedInComment  $event
     * @return void
     */
    public function handle(TaggedInComment $event)
    {
        $event->user->notify(new TaggedInCommentNotification($event->comment));
    }
}
