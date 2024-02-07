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
use App\Models\Company;
use App\Services\GetUserLocaleService;

class TaggedInComment extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
	public $locale;
    public $comment;
    public $commentCreator;
    public $bug;
    public $project;
	public $company;
    public $readableContent;
	public $groupsWording;
	public $groupBaseUrl;
	public $projectBaseUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, $locale, Comment $comment)
    {
        $this->locale = $locale;
        $this->user = $notifiable;
        $this->comment = $comment;
        $this->commentCreator = User::find($comment->user_id);
        $this->bug = Bug::find($comment->bug_id);
        $this->project = Project::find($this->bug->project_id);
		$this->company = $this->project->company;
		$this->groupBaseUrl = config('app.webpanel_url') . "/" . $this->company->organization->id . "/company/" . $this->company->id;
		$this->projectBaseUrl = config('app.webpanel_url') . "/" . $this->company->organization->id . "/company/" . $this->company->id . "/project/" . $this->project->id;

		$organization = $this->company->organization;
		$this->groupsWording = $organization->groups_wording;
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
        ->markdown('emails.' . $this->locale . '.tagged-in-comment');
    }
}
