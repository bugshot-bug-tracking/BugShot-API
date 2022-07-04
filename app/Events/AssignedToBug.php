<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use App\Models\Bug;

class AssignedToBug
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * User that sent the comment
     *
     * @var User
     */
    public $user;

    /**
     * Bug details
     *
     * @var Bug
     */
    public $bug;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Bug $bug)
    {
        $this->user = $user;
        $this->bug = $bug;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    // public function broadcastOn()
    // {
    //     return new PrivateChannel('comments');
    // }
}
