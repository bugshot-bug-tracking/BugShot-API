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

class ImportBugherdProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Bugherd;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
		public $apiToken,
		public $projectId,
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
		$response = Bugherd::sendBugherdRequest($apiToken, 'projects.json'); // TODO: Change parameters
    }
}
