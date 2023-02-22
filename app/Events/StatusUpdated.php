<?php

namespace App\Events;

use App\Http\Resources\StatusResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatusUpdated implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(public $status)
	{
	}

	/**
	 * The event's broadcast name.
	 *
	 * @return string
	 */
	public function broadcastAs()
	{
		return 'status.updated';
	}

	/**
	 * Determine if this event should broadcast.
	 *
	 * @return bool
	 */
	public function broadcastWhen()
	{
		// check if multiple users are part of the project
		// if ($this->status->project->users->isNotEmpty()) {
		// 	return true;
		// }

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
			'data' => new StatusResource($this->status)
		];
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\PrivateChannel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('project.' . $this->status->project->id);
	}
}
