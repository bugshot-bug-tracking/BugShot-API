<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
		$schedule->exec('php artisan queue:restart')
			->daily()
			->then(function () use ($schedule) {
				$schedule->exec('nohup php artisan queue:work --daemon >> storage/logs/scheduler.log &');
			}); // Restarts the job daemon
        $schedule->command('bugs:archive')->hourly();
        $schedule->command('auth:clear-resets')->daily();
		$schedule->command('php artisan queue:retry all')->everyFifteenMinutes();
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
