<?php

namespace App\Notifications;

// Miscellaneous, Helpers, ...
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use App\Mail\ApprovalReport as ApprovalReportMailable;
use App\Models\Report;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class ApprovalReportNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $filePath;
	public $fileName;
	public $export;
	public $evaluator;
	public $report;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Report $report, $export, $evaluator, public $user)
    {
		$this->report = $report;
        $this->filePath = config("app.url") . "/storage" . $report->url;
		$this->fileName = basename($report->filePath);
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
		$channels = ['broadcast'];

		if($notifiable->getSettingValueByName("user_settings_app_notifications") == "activated")
		{
			$channels[] = 'database';
		}

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
				"file_path" => $this->filePath,
				"created_at" => $this->report->created_at
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
			"type" => "ApprovalReportReceived",
            "data" => [
				"evaluator_name" => base64_decode($this->evaluator["name"]),
				"file_path" => $this->filePath,
				"created_at" => $this->report->created_at
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
