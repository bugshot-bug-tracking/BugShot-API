<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
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
        $this->invitation = $invitation;
        $this->resource = NULL;
        $this->message = NULL;
        $this->registerUrl = NULL;
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
            case Company::class:
                $this->resource = new CompanyResource($this->invitation->invitable);
                $this->message = __('email.invited_to_company', ['company' => __('data.company'), 'companyDesignation' => $this->resource->designation]);
                break;

            case Project::class:
                $this->resource = new ProjectResource($this->invitation->invitable);
                $this->message = __('email.invited_to_project', ['project' => __('data.project'), 'projectDesignation' => $this->resource->designation]);
                break;

            case Bug::class:
                $this->resource = new BugResource($this->invitation->invitable);
                $this->message = __('email.invited_to_bug', ['bug' => __('data.bug'), 'bugDesignation' => $this->resource->designation]);
                break;
        }

        $this->registerUrl = config('app.webpanel_url') . '/auth/register';

        return (new InvitationReceivedUnregisteredUserMailable($this->invitation, $this->message, $this->registerUrl))
        ->subject('BugShot - ' . __('email.invitation-received'))
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
