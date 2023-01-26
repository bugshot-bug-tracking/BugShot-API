<?php

namespace App\Jobs;

use App\Services\ApiCallService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TriggerInterfacesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ApiCallService $apiCallService;
    public $resource;
    public $trigger_id;
    public $project_id;
    public $uuid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ApiCallService $apiCallService, $resource, $trigger_id, $project_id, $uuid = null)
    {
        $this->apiCallService = $apiCallService;
        $this->resource = $resource;
        $this->trigger_id = $trigger_id;
        $this->project_id = $project_id;
        $this->uuid = $uuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->apiCallService->triggerInterfaces($this->resource, $this->trigger_id, $this->project_id, $this->uuid);
    }
}
