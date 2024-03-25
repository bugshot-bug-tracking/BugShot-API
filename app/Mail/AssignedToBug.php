<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Bug;
use App\Models\Project;
use App\Services\GetUserLocaleService;

class AssignedToBug extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
	public $locale;
    public $bug;
    public $project;
    public $initiator;
	public $projectBaseUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, $sender, $locale, Bug $bug)
    {
        $this->locale = $locale;
        $this->user = $notifiable;
		$this->initiator = $sender;
        $this->bug = $bug;
        $this->project = Project::find($this->bug->project_id);
		$company = $this->project->company;
		$this->projectBaseUrl = config('app.webpanel_url') . "/" . $company->organization->id . "/company/" . $company->id . "/project/" . $this->project->id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.noreply'))
        ->markdown('emails.' . $this->locale . '.assigned-to-bug');
    }
}
