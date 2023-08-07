<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

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
		Log::info("Restarting queue");
		$schedule->exec('php artisan queue:restart')
			->everySixHours($minutes = 0)
			->then(function () use ($schedule) {
				Log::info("Queue restarted. Starting daemon now");
				$schedule->exec('nohup php artisan queue:work --daemon >> storage/logs/scheduler.log &');
				Log::info("Daemon started successfully");
			}); // Restarts the job daemon
		Log::info("Archiving bugs");
		$schedule->command('bugs:archive')->hourly();
		// Log::info("Sending project summaries");
        // $schedule->command('projects:send-summary')->dailyAt('06:00');
		Log::info("Clearing auths");
        $schedule->command('auth:clear-resets')->daily();
		Log::info("Retrying");
		$schedule->command('queue:retry all')->everyFifteenMinutes();
		Log::info("Scheduler finished running ---");
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
