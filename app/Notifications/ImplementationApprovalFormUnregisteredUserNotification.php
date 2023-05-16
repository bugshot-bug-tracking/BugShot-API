<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Mail\ImplementationApprovalFormUnregisteredUser as ImplementationApprovalFormUnregisteredUserNotificationMailable;

class ImplementationApprovalFormUnregisteredUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $export;
	public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($export, $usermail)
    {
        $this->export = $export;
		$this->url = config('app.webpanel_url') . "/approvals/" . base64_encode($usermail) . "/" . $export->id;
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
        return (new ImplementationApprovalFormUnregisteredUserNotificationMailable($this->locale, $this->export, $this->url))
        ->subject('BugShot - ' . __('email.implementation-approval-form-received', [], $this->locale))
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
