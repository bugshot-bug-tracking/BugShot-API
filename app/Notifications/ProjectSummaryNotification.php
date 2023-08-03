<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\ProjectSummary as ProjectSummaryMailable;

class ProjectSummaryNotification extends Notification
{
    use Queueable;

    public $comments;
    public $bugs;
	public $doneBugs;
    public $project;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($project, $comments, $doneBugs, $bugs)
    {
        $this->project = $project;
		$this->comments = $comments;
		$this->doneBugs = $doneBugs;
		$this->bugs = $bugs;
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
        return (new ProjectSummaryMailable($notifiable, $this->locale, $this->project, $this->comments, $this->doneBugs, $this->bugs))
        ->subject('BugShot - ' . __('email.project-summary', [], $this->locale))
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
