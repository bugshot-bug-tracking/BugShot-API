<?php

namespace App\Providers;

// Misc
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Cashier\Events\WebhookReceived;

// Models
use App\Models\Project;
use App\Models\Bug;
use Illuminate\Notifications\DatabaseNotification as Notification;

// Events
use App\Events\TaggedInComment;
use App\Events\BugMembersUpdated;

// Listeners
use App\Listeners\StripeEventListener;
use App\Listeners\SendTaggedInCommentNotification;
use App\Listeners\SendAssignedToBugNotification;

// Observers
use App\Observers\NotificationObserver;
use App\Observers\ProjectObserver;
use App\Observers\BugObserver;


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
	 * The model observers for your application.
	 *
	 * @var array
	 */
	protected $observers = [
		Project::class => [ProjectObserver::class],
		Bug::class => [BugObserver::class],
		Notification::class => [NotificationObserver::class],
	];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
		//
    }
}
