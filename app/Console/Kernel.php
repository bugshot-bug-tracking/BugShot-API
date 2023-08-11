<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Models\Bug;
use App\Models\Project;
use App\Services\GetUserLocaleService;
use Carbon\Carbon;
use App\Notifications\ProjectSummaryNotification;

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
		$schedule->exec('php artisan queue:restart')
			->everySixHours($minutes = 0)
			->then(function () use ($schedule) {
				// Restarts the job daemon
				$schedule->exec('nohup php artisan queue:work --daemon >> storage/logs/scheduler.log &');
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
                    $project->creator->notify((new ProjectSummaryNotification($project, $comments, $doneBugs, $bugs))->locale(GetUserLocaleService::getLocale($project->creator)));
                }
            }
        })->dailyAt('06:00');

        $schedule->command('auth:clear-resets')->daily();
		$schedule->command('queue:retry all')->everyFifteenMinutes();
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
