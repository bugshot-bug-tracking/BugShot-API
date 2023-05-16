<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Mail\ApprovalReportUnregisteredUser as ApprovalReportUnregisteredUserMailable;

class ApprovalReportUnregisteredUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $filePath;
	public $fileName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = config("app.url") . "/storage" . $filePath;
		$this->fileName = basename($filePath);
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
        return (new ApprovalReportUnregisteredUserMailable($this->locale))
        ->subject('BugShot - ' . __('email.approval-report-received', [], $this->locale))
		->attach($this->filePath, [
            'as' => $this->fileName,
            'mime' => 'application/pdf'
        ])
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
