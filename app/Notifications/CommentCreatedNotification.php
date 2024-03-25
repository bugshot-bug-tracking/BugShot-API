<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\CommentCreated as CommentCreatedMailable;

class CommentCreatedNotification extends Notification implements ShouldQueue
{
	use Queueable;

	public $comment;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($comment)
	{
		$this->comment = $comment;
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

		$channels = [];

		if ($notifiable->getSettingValueByName("user_settings_app_notifications") == "activated") {
			$channels[] = 'database';
		}

		if (
			$notifiable->getSettingValueByName("user_settings_mail_select_notifications") == "activated"
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
		return (new CommentCreatedMailable($notifiable, $this->locale, $this->comment))
			->subject('BugShot - ' . __('email.comment-created', [], $this->locale))
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
			"type" => "CommentCreated",
			"data" => [
				"creator_name" => $this->comment->user->first_name . " " . $this->comment->user->last_name,
				"organization_id" => $this->comment->bug->project->company->organization->id,
				"company_id" => $this->comment->bug->project->company->id,
				"project_id" => $this->comment->bug->project->id,
				"bug_id" => $this->comment->bug->id,
				"comment_id" => $this->comment->id,
				"is_internal" => $this->comment->is_internal ? true : false,
				"created_at" => $this->comment->created_at
			]
		];
	}
}
