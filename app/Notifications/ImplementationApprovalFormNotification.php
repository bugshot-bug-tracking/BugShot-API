<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Mail\ImplementationApprovalForm as ImplementationApprovalFormMailable;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\URL;

class ImplementationApprovalFormNotification extends Notification implements ShouldQueue
{
	use Queueable;

	public $export;
	public $user;
	public $url;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($export, $user)
	{
		$this->export = $export;
		$this->user = $user;
		$this->url = config('app.webpanel_url') . "/approvals/" . base64_encode($user->email) . "/" . base64_encode($user->first_name . " " . $user->last_name) . "/" . $export->id;
	}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
		// Check if user is licensed
		if(!$notifiable->licenseActive())
		{
			return [];
		}

		$channels = ['broadcast'];

		if ($notifiable->getSettingValueByName("user_settings_app_notifications") == "activated") {
			$channels[] = 'database';
		}

		if (
			$notifiable->getSettingValueByName("user_settings_mail_select_notifications") == "activated" &&
			($notifiable->getSettingValueByName("user_settings_select_notifications") == "every_notification" ||
				$notifiable->getSettingValueByName("custom_notifications_implementation_approval_form_received") == "activated")
		) {
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
		return (new ImplementationApprovalFormMailable($notifiable, $this->locale, $this->url))
			->subject('BugShot - ' . __('email.implementation-approval-form-received', [], $this->locale))
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
			"type" => "ImplementationApprovalFormReceived",
			"data" => [
				"exporter_name" => $this->export->exporter->first_name . " " . $this->export->exporter->last_name,
				"project_designation" => $this->export->project->designation,
				"url" => $this->url,
				"created_at" => $this->export->created_at
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
			"type" => "ImplementationApprovalFormReceived",
			"data" => [
				"exporter_name" => $this->export->exporter->first_name . " " . $this->export->exporter->last_name,
				"project_designation" => $this->export->project->designation,
				"url" => $this->url,
				"created_at" => $this->export->created_at
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
