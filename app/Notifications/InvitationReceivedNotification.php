<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\InvitationReceived as InvitationReceivedMailable;

// Resources
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\BugResource;

// Models
use App\Models\Organization;
use App\Models\Company;
use App\Models\Bug;
use App\Models\Project;

class InvitationReceivedNotification extends Notification
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
            case Organization::class:
                $this->resource = new OrganizationResource($this->invitation->invitable);
                $this->message = __('email.invited_to_organization', ['organization' => __('data.organization'), 'organizationDesignation' => $this->resource->designation]);
                break;

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

        return (new InvitationReceivedMailable($notifiable, $this->invitation, $this->message))
        ->subject('BugShot - ' . __('email.invitation-received'))
        ->to($notifiable->email);
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
