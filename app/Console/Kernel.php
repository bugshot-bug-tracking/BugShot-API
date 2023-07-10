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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		Log::info("Running scheduler.");
		$schedule->command('projects:send-summary')->daily();
		$schedule->exec('php artisan queue:restart')
			->daily()
			->then(function () use ($schedule) {
				Log::info("Queue restarted. Starting daemon now.");
				$schedule->exec('nohup php artisan queue:work --daemon >> storage/logs/scheduler.log &');
				Log::info("Daemon started successfully.");
			}); // Restarts the job daemon

		Log::info("Archiving bugs.");
        $schedule->command('bugs:archive')->hourly();
		Log::info("Clearing auths bugs.");
        $schedule->command('auth:clear-resets')->daily();
		Log::info("Retry failed jobs.");
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
