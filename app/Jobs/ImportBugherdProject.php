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

class ImportBugherdProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Bugherd;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
		public $importId,
		public $apiToken,
		public $project,
		BugherdImportController $bugherdImportController
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
		$bugherdProjectId = $this->project->bugherdProjectId;
		$response = Bugherd::sendBugherdRequest($apiToken, "projects/#${bugherdProjectId}.json");

		if($response->getStatus() == 200)
		{
			$import = Import::find($this->importId);
			$import->update([
				'status_id' => ImportStatus::IMPORTED
			]);
		} else {
			$import = Import::find($this->importId);
			$import->update([
				'status_id' => ImportStatus::IMPORT_FAILED
			]);
		}
    }
}
