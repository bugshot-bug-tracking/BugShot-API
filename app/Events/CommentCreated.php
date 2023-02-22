<?php

namespace App\Events;

use App\Http\Resources\CommentResource;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(public User $user, public $comment, public $taggedUsers)
	{
	}

	/**
	 * The event's broadcast name.
	 *
	 * @return string
	 */
	public function broadcastAs()
	{
		return 'comment.created';
	}

	/**
	 * Determine if this event should broadcast.
	 *
	 * @return bool
	 */
	public function broadcastWhen()
	{
		// Check if there are any users assigned to the bug
		if ($this->comment->bug->users->isNotEmpty()) {
			return true;
		}

		// Check if a user other then the comment author is tagged in the comment
		// if(count($this->taggedUsers) > 0 && !in_array(['user_id' => $this->user->id], $this->taggedUsers)) {
		// 	return true;
		// }

		// check if multiple users are part of the project
		if ($this->comment->bug->project->users->isNotEmpty()) {
			return true;
		}

		return false;
	}

	/**
	 * Get the data to broadcast.
	 *
	 * @return array
	 */
	public function broadcastWith()
	{
		return [
			'data' => new CommentResource($this->comment)
		];
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\PrivateChannel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('bug.' . $this->comment->bug->id);
	}
}
