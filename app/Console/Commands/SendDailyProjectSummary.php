<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\GetUserLocaleService;
use Carbon\Carbon;
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
    }
}
