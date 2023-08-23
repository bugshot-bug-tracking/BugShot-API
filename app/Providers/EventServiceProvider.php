<?php

namespace App\Providers;

use App\Events\BugMembersUpdated;
use App\Listeners\SendAssignedToBugNotification;
use App\Events\TaggedInComment;
use App\Listeners\SendTaggedInCommentNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Events\WebhookReceived;
use App\Listeners\StripeEventListener;
use App\Observers\NotificationObserver;
use Illuminate\Notifications\DatabaseNotification as Notification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        TaggedInComment::class => [
            SendTaggedInCommentNotification::class,
        ],
        BugMembersUpdated::class => [
            SendAssignedToBugNotification::class,
        ],
		WebhookReceived::class => [
            StripeEventListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
		Notification::observe(NotificationObserver::class);
    }
}
