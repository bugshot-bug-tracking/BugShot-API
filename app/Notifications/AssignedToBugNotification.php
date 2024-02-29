<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\AssignedToBug as AssignedToBugMailable;

class AssignedToBugNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $bug, public $sender, public $assignedAt)
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
		$channels = [];

		if($notifiable->getSettingValueByName("user_settings_mail_select_notifications") == "activated")
		{
			$channels[] = 'mail';
		}

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new AssignedToBugMailable($notifiable, $this->sender, $this->locale, $this->bug))
        ->subject('BugShot - ' . __('email.assigned-to-bug', [], $this->locale))
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
			"type" => "AssignedToBug",
            "data" => [
				"assigned_by" => $this->sender->first_name . " " . $this->sender->last_name,
				"bug" => [
					"id" => $this->bug->id,
					"designation" => $this->bug->id,
				],
				"organization_id" => $this->bug->project->company->organization->id,
				"company_id" => $this->bug->project->company->id,
				"project_id" => $this->bug->project->id,
				"assigned_at" => $this->assignedAt
			]
        ];
    }
}
