<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\GetUserLocaleService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use App\Notifications\ProjectSummaryNotification;

class SendDailyProjectSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:send-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gathers all projects that where updated in the last 24 hours and sends a summary of the updates to the owner of the project';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		Log::info("Retrieving updated projects");
		$date = Carbon::now()->subDay();

		$projects = Project::
					whereDate('updated_at', '>=', $date)
					->with(['comments' => function ($query) use ($date) {
						$query->whereDate('comments.created_at', '>=', $date);
					}])
					->with(['bugs' => function ($query) use ($date) {
						$query->whereDate('bugs.created_at', '>=', $date)
							->orWhereDate('bugs.done_at', '>=', $date);
					}])
					->get();

		foreach($projects as $project) {
			$comments = $project->comments()->whereDate('comments.updated_at', '>=', Carbon::now()->subDay())->get();
			$doneBugs = $project->bugs()->whereDate('bugs.done_at', '>=', Carbon::now()->subDay())->get();
			$bugs = $project->bugs()->whereDate('bugs.created_at', '>=', Carbon::now()->subDay())->get();

			if(!$comments->isEmpty() || !$doneBugs->isEmpty() || !$bugs->isEmpty()) {
				foreach($project->users as $user) {
					if($user->getSettingValueByName("user_settings_select_notifications") == "every_notification"
					|| $user->getSettingValueByName("custom_notifications_daily_summary") == "activated") {
						$user->notify((new ProjectSummaryNotification($project, $comments, $doneBugs, $bugs))->locale(GetUserLocaleService::getLocale($user)));
					}
				}
			}
		}
    }
}
