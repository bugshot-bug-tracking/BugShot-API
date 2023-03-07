<?php

namespace App\Events;

use App\Http\Resources\InvitationResource;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public $invitation)
    {
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'invitation.deleted';
    }

    /**
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen()
    {
        return true;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'data' => new InvitationResource($this->invitation)
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel|array
     */
    public function broadcastOn()
    {
        switch ($this->invitation->invitable_type) {
            case 'organization':
                return new PrivateChannel('organization.' . $this->invitation->invitable_id);
                break;

            case 'company':
                return new PrivateChannel('company.' . $this->invitation->invitable_id);
                break;

            case 'project':
                return new PrivateChannel('project.' . $this->invitation->invitable_id);
                break;

            default:
                $userList = User::all()->where('email', '=', $this->invitation->target_email);
                if ($userList->isEmpty()) {
                    return null;
                }
                $userId = $userList->first()->id;
                return new PrivateChannel('user.' . $userId);
                break;
        }
    }
}
