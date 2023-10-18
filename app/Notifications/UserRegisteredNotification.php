<?php

namespace App\Notifications;

use App\Http\Resources\UserResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\UserRegistered as UserRegisteredMailable;

class UserRegisteredNotification extends Notification implements ShouldQueue
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(public $user)
	{
		//
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
		// Get the 'register_mailer' value from the Laravel application configuration
		$register_mailer = config('app.register_mailer');

		// Check if 'register_mailer' is null. If it is, it means the 'register_mailer' config is not set.
		// In this case, we return early without sending the notification email.
		if (is_null($register_mailer)) {
			return;
		}

		// If 'register_mailer' is not null, we create a new 'UserRegisteredMailable' object, set the email subject,
		// and specify the recipient using the 'register_mailer' email.
		return (new UserRegisteredMailable($notifiable, $this->locale, $this->user))
			->subject('A User has been registered')
			->to($register_mailer);
	}
}
