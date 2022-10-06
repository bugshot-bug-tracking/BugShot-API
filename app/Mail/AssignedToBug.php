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
    public $bug;
    public $project;
    public $initiator;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, Bug $bug)
    {
        $this->user = $notifiable;
        $this->bug = $bug;
        $this->initiator = Auth::user();
        $this->project = Project::find($this->bug->project_id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.noreply'))
        ->markdown('emails.' . GetUserLocaleService::getLocale($this->user) . '.assigned-to-bug');
    }
}
