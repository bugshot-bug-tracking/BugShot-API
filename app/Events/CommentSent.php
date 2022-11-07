<?php

namespace App\Events;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * User that sent the comment
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Comment details
     *
     * @var \App\Models\Comment
     */
    public $comment;

	/**
     * The array of tagged users
     */
    public $taggedUsers;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $comment, $taggedUsers)
    {
        $this->user = $user;
        $this->comment = $comment;
		$this->taggedUsers = $taggedUsers;
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
		if($this->comment->bug->users->isNotEmpty()) {
			return true;
		}

		// Check if a user other then the comment author is tagged in the comment
		if(count($this->taggedUsers) > 0 && !in_array(['user_id' => $this->user->id], $this->taggedUsers)) {
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
			'comment' => $this->comment
		];
	}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('comments.' . $this->comment->id);
    }

}
