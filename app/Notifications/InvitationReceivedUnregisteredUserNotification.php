<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\InvitationReceivedUnregisteredUser as InvitationReceivedUnregisteredUserMailable;

// Resources
use App\Http\Resources\CompanyResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\BugResource;

// Models
use App\Models\Company;
use App\Models\Bug;
use App\Models\Project;

// Services
use App\Services\GetUserLocaleService;

class InvitationReceivedUnregisteredUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invitation)
    {
		$this->locale = GetUserLocaleService::getLocale(Auth::user());
        $this->invitation = $invitation;
        $this->resource = NULL;
        $this->message = NULL;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Check the model type of the invitation
        switch ($this->invitation->invitable_type) {
            case 'company':
                $this->resource = new CompanyResource($this->invitation->invitable);
                $this->message = __('email.invited_to_company', ['company' => __('data.company'), 'companyDesignation' => $this->resource->designation]);
                break;

            case 'project':
                $this->resource = new ProjectResource($this->invitation->invitable);
                $this->message = __('email.invited_to_project', ['project' => __('data.project'), 'projectDesignation' => $this->resource->designation]);
                break;

            case 'bug':
                $this->resource = new BugResource($this->invitation->invitable);
                $this->message = __('email.invited_to_bug', ['bug' => __('data.bug'), 'bugDesignation' => $this->resource->designation]);
                break;
        }

        return (new InvitationReceivedUnregisteredUserMailable($this->locale, $this->invitation, $this->message))
        ->subject('BugShot - ' . __('email.invitation-received', [], $this->locale))
        ->to($notifiable->routes['email']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
