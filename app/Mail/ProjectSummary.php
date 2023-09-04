<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

use App\Models\User;
use App\Services\GetUserLocaleService;

class ProjectSummary extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
	public $locale;
    public $comments;
    public $bugs;
	public $doneBugs;
    public $project;
	public $groupsWording;
	public $readableContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, $locale, $project, $comments, $doneBugs, $bugs)
    {
        $this->locale = $locale;
        $this->user = $notifiable;
        $this->project = $project;
		$this->comments = $comments;
		$this->doneBugs = $doneBugs;
		$this->bugs = $bugs;

		$company = $project->company;
		$organization = $company->organization;
		$this->groupsWording = $organization->groups_wording;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$this->comments->map(function(object $item, int $key) {
			$this->readableContent = $item->content;

			preg_match_all(
				'/(?:<([0-9]+)\$(.*?)>)/',
				$this->readableContent,
				$matches
			);

			foreach($matches[0] as $key=>$match) {
				$this->readableContent = str_replace($match, $matches[2][$key], $this->readableContent);
			}

			return $item["content"] = $this->readableContent;
		});

		return $this->from(config('mail.noreply'))
		->markdown('emails.' . $this->locale . '.project-summary');
    }
}
