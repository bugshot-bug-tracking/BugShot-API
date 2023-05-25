<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Mail\ImplementationApprovalForm as ImplementationApprovalFormMailable;
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
		$this->url = config('app.webpanel_url') . "/approvals/" . base64_encode($this->user->email) . "/" . $export->id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
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
				"url" => $this->url
			]
        ];
    }
}
