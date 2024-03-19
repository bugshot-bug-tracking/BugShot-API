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
use Illuminate\Support\Facades\Artisan;
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
		// Archive bugs
		$schedule->call(function () {
			Log::info("Start bug archiving...");

			$bugs = Bug::whereNull("archived_at")
				->where(function ($query) {
					$query->where("done_at", "<=", date('Y-m-d', strtotime(now() . '- 30 days')))
						->orWhere("deleted_at", "<=", date('Y-m-d', strtotime(now() . '- 30 days')));
				})
				->withTrashed()
				->get();

			foreach ($bugs as $bug) {
				$bug->fill([
					"archived_at" => now()
				]);

				$bug->fireCustomEvent('bugArchived');
				$bug->save();
			}

			Log::info('Bugs archived successfully!');
		})->everyThirtyMinutes();

		// Send project summary
		$schedule->call(function () {
			Log::info("Start summary emails...");

			$date = Carbon::now()->subDay();

			$projects = Project::whereDate('updated_at', '>=', $date)
				->with(['comments' => function ($query) use ($date) {
					$query->whereDate('comments.created_at', '>=', $date);
				}])
				->with(['bugs' => function ($query) use ($date) {
					$query->whereDate('bugs.created_at', '>=', $date)
						->orWhereDate('bugs.done_at', '>=', $date);
				}])
				->get();

			foreach ($projects as $project) {
				$comments = $project->comments()->whereDate('comments.updated_at', '>=', Carbon::now()->subDay())->get();
				$doneBugs = $project->bugs()->whereDate('bugs.done_at', '>=', Carbon::now()->subDay())->get();
				$bugs = $project->bugs()->whereDate('bugs.created_at', '>=', Carbon::now()->subDay())->get();

				if (!$comments->isEmpty() || !$doneBugs->isEmpty() || !$bugs->isEmpty()) {
					foreach ($project->users as $user) {
						if (
							$user->getSettingValueByName("user_settings_select_notifications") == "every_notification"
							|| $user->getSettingValueByName("custom_notifications_daily_summary") == "activated"
						) {
							$user->notify((new ProjectSummaryNotification($project, $comments, $doneBugs, $bugs))->locale(GetUserLocaleService::getLocale($user)));
						}
					}
				}
			}

			Log::info("Summary emails sent!");
		})->dailyAt('06:00');

		// Count the job table entries to see if they stack up
		$schedule->call(function () {
			$jobCount = DB::table('jobs')->count();
			if ($jobCount > config("app.max_job_stack_size")) {
				Mail::to(config("mail.reply_to.address"))->send(new MaxJobStackSizeReached($jobCount));
			}
		})->everyThirtyMinutes();

		$schedule->call(function () {
			Artisan::call('auth:clear-resets');
			Log::info('Auth: Expired password reset links cleared!');
		})->daily();

		$schedule->command('sanctum:prune-expired --hours=24')->weekly();
	}

	/**
	 * Register the commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		$this->load(__DIR__ . '/Commands');

		require base_path('routes/console.php');
	}
}
