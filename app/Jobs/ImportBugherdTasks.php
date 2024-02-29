<?php

namespace App\Jobs;

use App\Services\ApiCallService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\Bugherd;
use App\Models\ImportStatus;
use App\Models\Import;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportBugherdTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Bugherd;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
		public $import,
		public $apiToken,
		public $bugherdProjectId,
		public $project
	)
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$response = Bugherd::sendBugherdRequest($this->apiToken, "projects/" . $this->bugherdProjectId . "/tasks.json");

		if($response->ok())
		{
			$tasks = json_decode($response->body(), true)['tasks'];
			$status = $this->project->statuses()->where('permanent', 'backlog')->first();

			// Get the max order number in this status and increase it by one
			$order_number = $status->bugs->isEmpty() ? 0 : $status->bugs->max('order_number') + 1;

			// Determine the number of bugs in the project to generate the $ai_id
			$allBugsQuery = $status->project->bugs()->withTrashed();
			$numberOfBugs = $allBugsQuery->count();
			$ai_id = $allBugsQuery->get()->isEmpty() ? 0 : $numberOfBugs + 1;

			try
			{
				foreach($tasks as $task)
				{
					$bug = new Bug();
					$bug->fill([
						"id" => (string) Str::uuid(),
						"designation" => $task["description"],
						"description" => "",
						"url" => $this->project->url,
						"priority_id" => 1,
						"operating_system" => "",
						"time_estimation" => "",
						"browser" => "",
						"selector" => "",
						"resolution" => "",
						"project_id" => $this->project->id,
						"user_id" => $this->import->imported_by,
						"time_estimation_type" => 'm',
						"approval_status_id" => null,
						"deadline" => $task['due_at'] == NULL ? null : new Carbon($task['due_at']),
						"order_number" => $order_number,
						"ai_id" => $ai_id,
						"client_id" => $client_id
					]);

					// Do the save and fire the custom event
					$bug->fireCustomEvent('bugCreated');
					$bug->save();
				}

				$this->import->update([
					'status_id' => ImportStatus::IMPORTED
				]);
			} catch(Exception $e)
			{
				Log::error($e->getMessage());
				$this->import->update([
					'status_id' => ImportStatus::IMPORT_FAILED
				]);
			}
		} else {
			$this->import->update([
				'status_id' => ImportStatus::IMPORT_FAILED
			]);
		}
    }
}
