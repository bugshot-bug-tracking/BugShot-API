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
use App\Services\GetUserLocaleService;
use Carbon\Carbon;

class ProjectSummary extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
	public $locale;
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
    public function __construct(User $notifiable, $locale, Project $project)
    {
        $this->locale = $locale;
        $this->user = $notifiable;
        $this->project = $project;
		// TODO: Get comments that where updated
		dd($project->bugs->comments);
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
            '/(?:<([0-9]+)\$(.*?)>)/',
            $this->readableContent,
            $matches
        );

		foreach($matches[0] as $key=>$match) {
			$this->readableContent = str_replace($match, substr_replace($matches[1][$key], "", -1), $this->readableContent);
		}

        return $this->from(config('mail.noreply'))
        ->markdown('emails.' . $this->locale . '.comment-created');
    }
}
