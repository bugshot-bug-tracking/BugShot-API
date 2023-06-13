<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\InvitationReceived as InvitationReceivedMailable;
use Illuminate\Support\Facades\App;

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

// Services
use App\Services\GetUserLocaleService;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InvitationReceivedNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $resource;
    public $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $invitation, public $user)
    {
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
        return ['mail', 'database', 'broadcast'];
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
            case 'organization':
                $this->resource = new OrganizationResource($this->invitation->invitable);
                $this->message = __('email.invited_to_organization', ['organization' => __('data.organization'), 'organizationDesignation' => $this->resource->designation]);
                break;

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

        return (new InvitationReceivedMailable($notifiable, $this->locale, $this->invitation, $this->message))
        ->subject('BugShot - ' . __('email.invitation-received', [], $this->locale))
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
			"type" => "InvitationReceived",
            "data" => [
				"id" => $this->invitation->id,
				"invited_to_type" => $this->invitation->invitable_type,
				"invited_to" => $this->invitation->invitable->designation,
				"invited_by" => $this->invitation->sender->first_name . " " . $this->invitation->sender->last_name,
				"created_at" => $this->invitation->created_at
			]
        ];
    }


	/**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'notification.created';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
			"type" => "InvitationReceived",
            "data" => [
				"id" => $this->invitation->id,
				"invited_to_type" => $this->invitation->invitable_type,
				"invited_to" => $this->invitation->invitable->designation,
				"invited_by" => $this->invitation->sender->first_name . " " . $this->invitation->sender->last_name,
				"created_at" => $this->invitation->created_at
			]
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }
}
