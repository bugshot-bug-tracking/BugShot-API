<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

use App\Models\User;
use App\Models\Bug;
use App\Models\Project;
use App\Models\Comment;

class TaggedInComment extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $comment;
    public $commentCreator;
    public $bug;
    public $project;
    public $readableContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, Comment $comment)
    {
        $this->user = $notifiable;
        $this->comment = $comment;
        $this->commentCreator = User::find($comment->user_id);
        $this->bug = Bug::find($comment->bug_id);
        $this->project = Project::find($this->bug->project_id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		// String replace the content so the tags are readable
		$this->readableContent = $this->comment->content;

		preg_match_all(
            '/(?:<1\$(.*?)>)/',
            $this->readableContent,
            $matches
        );
		
		foreach($matches[0] as $key=>$match) {
			$this->readableContent = str_replace($match, substr_replace($matches[1][$key], "", -1), $this->readableContent);
		}
 
        return $this->from(config('mail.noreply'))
        ->markdown('emails.' . App::currentLocale() . '.tagged-in-comment');
    }
}
