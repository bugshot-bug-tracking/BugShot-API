<?php

namespace App\Events;

use App\Http\Resources\ScreenshotResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScreenshotCreated implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $bug;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(public $screenshot)
	{
		$this->bug = $screenshot->bug;
	}

	/**
	 * The event's broadcast name.
	 *
	 * @return string
	 */
	public function broadcastAs()
	{
		return 'screenshot.created';
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
			'data' => new ScreenshotResource($this->screenshot)
		];
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\PrivateChannel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('bug.' . $this->bug->id);
	}
}
