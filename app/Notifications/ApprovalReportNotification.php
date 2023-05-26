<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Mail\ApprovalReport as ApprovalReportMailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class ApprovalReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $filePath;
	public $fileName;
	public $export;
	public $evaluator;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($filePath, $export, $evaluator)
    {
        $this->filePath = config("app.url") . "/storage" . $filePath;
		$this->fileName = basename($filePath);
		$this->export = $export;
		$this->evaluator = $evaluator;
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
        return (new ApprovalReportMailable($notifiable, $this->locale))
        ->subject('BugShot - ' . __('email.approval-report-received', [], $this->locale))
		->attach($this->filePath, [
            'as' => $this->fileName,
            'mime' => 'application/pdf'
        ])
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
			"type" => "ApprovalReportReceived",
            "data" => [
				"evaluator_name" => base64_decode($this->evaluator["name"]),
				"file_path" => $this->filePath
			]
        ];
    }
}
