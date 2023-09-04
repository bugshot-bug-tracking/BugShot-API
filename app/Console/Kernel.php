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

        // Restart queue and daemon
		$schedule->exec('php81 artisan queue:restart')
			->everySixHours($minutes = 0)
			->then(function () use ($schedule) {
				// Restarts the job daemon
				$schedule->exec('nohup php81 artisan queue:work --daemon >> storage/logs/scheduler.log &');
			});

		// Archive bugs
		$schedule->call(function () {
			Log::info("Retrieving bugs to archive");
			$bugs = Bug::where("archived_at", NULL)
						->whereNot("deleted_at", NULL)
						->orWhere("done_at", "<=", date('Y-m-d', strtotime(now() . ' - 30 days')))
						->withTrashed()
						->get();

			foreach($bugs as $bug) {
				$bug->update([
					"archived_at" => now()
				]);
			}

			Log::info('Bugs archived successfully!');
		})->everyTwoMinutes();

        // Send project summary
        $schedule->call(function() {
            Log::info("Retrieving updated projects");
            $projects = Project::whereDate('updated_at', '>=', Carbon::now()->subDay())->get();
            // $projects = Project::all(); // ONLY DEV
            foreach($projects as $project) {

                $comments = $project->comments()->whereDate('comments.created_at', '>=', Carbon::now()->subDay())->get();
                $doneBugs = $project->bugs()->whereDate('bugs.done_at', '>=', Carbon::now()->subDay())->get();
                $bugs = $project->bugs()->whereDate('bugs.created_at', '>=', Carbon::now()->subDay())->get();

                // Check if at least one entity is not empty
                if(!$comments->isEmpty() || !$doneBugs->isEmpty() || !$bugs->isEmpty()) {
					foreach($project->users as $user) {
						if($user->getSettingValueByName("user_settings_select_notifications") == "every_notification" || $user->getSettingValueByName("custom_notifications_daily_summary") == "activated") {
							$user->notify((new ProjectSummaryNotification($project, $comments, $doneBugs, $bugs))->locale(GetUserLocaleService::getLocale($user)));
						}
					}
                }
            }
        })->dailyAt('06:00');

        $schedule->command('auth:clear-resets')->daily();
		$schedule->command('queue:retry all')->everyFifteenMinutes();

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
