<?php

namespace App\Console;

use App\Mail\MaxJobStackSizeReached;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Models\Bug;
use App\Models\Project;
use App\Services\GetUserLocaleService;
use Carbon\Carbon;
use App\Notifications\ProjectSummaryNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ArchiveBugs::class,
        Commands\SendDailyProjectSummary::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		Log::info("Running scheduler ---");

		// Archive bugs
		$schedule->call(function () {
			Log::info("Start bug archiving...");

			Bug::whereNull("archived_at")
				->whereNotNull("deleted_at")
				->orWhere("done_at", "<=", date('Y-m-d', strtotime(now() . '- 30 days')))
				->withTrashed()
				->update(["archived_at" => now()]);

			Log::info('Bugs archived successfully!');
		})->everyThirtyMinutes();

        // Send project summary
        $schedule->call(function() {
			Log::info("Retrieving updated projects");

			$date = Carbon::now()->subDay();

			$projects = Project::
						whereDate('updated_at', '>=', $date)
						->with(['comments' => function ($query) use ($date) {
							$query->whereDate('created_at', '>=', $date);
						}])
						->with(['bugs' => function ($query) use ($date) {
							$query->whereDate('created_at', '>=', $date)
								->orWhereDate('done_at', '>=', $date);
						}])
						->get();

			foreach($projects as $project) {
				$comments = $project->comments;
				$bugs = $project->bugs;
				$doneBugs = $bugs->where('done_at', '>=', $date);

				if(!$comments->isEmpty() || !$doneBugs->isEmpty() || !$bugs->isEmpty()) {
					foreach($project->users as $user) {
						if($user->getSettingValueByName("user_settings_select_notifications") == "every_notification" 
						|| $user->getSettingValueByName("custom_notifications_daily_summary") == "activated") {
							$user->notify((new ProjectSummaryNotification($project, $comments, $doneBugs, $bugs))->locale(GetUserLocaleService::getLocale($user)));
						}
					}
				}
			}
        })->dailyAt('06:00');

        $schedule->command('auth:clear-resets')->daily();

		// Count the job table entries to see if they stack up
		$schedule->call(function() {
			$jobCount = DB::table('jobs')->count();
			if($jobCount > config("app.max_job_stack_size")) {
				Mail::to(config("mail.reply_to.address"))->send(new MaxJobStackSizeReached($jobCount));
			}
		})->hourly();

		$schedule->command('sanctum:prune-expired --hours=24')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
