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

class ImportBugherdProject implements ShouldQueue
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
		$bugshotCompanyId = $this->project["bugshotCompanyId"];
		$bugherdProjectId = $this->project["bugherdProjectId"];
		$response = Bugherd::sendBugherdRequest($this->apiToken, "projects/${bugherdProjectId}.json");

		// TODO: Test that
		if($response->ok())
		{
			$projectData = json_decode($response->body(), true)['project'];
			try
			{
				$project = new Project();
				$project->id = (string) Str::uuid();
				$project->user_id = $this->import->importer->id;
				$project->designation = $projectData['name'];
				$project->color_hex = '#7A2EE6';
				$project->company_id = $bugshotCompanyId;
				$project->url = $projectData['devurl'];

				// Do the save and fire the custom event
				$project->fireCustomEvent('projectCreated');
				$project->save();

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
