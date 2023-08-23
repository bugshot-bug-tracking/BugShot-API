<?php

namespace App\Services;

use App\Events\ScreenshotCreated;
use App\Events\ScreenshotDeleted;
use App\Http\Resources\BugResource;
use App\Http\Resources\ScreenshotInterfaceResource;
use App\Http\Resources\ScreenshotResource;
use App\Jobs\TriggerInterfacesJob;
use App\Jobs\CompressImage;
use App\Models\Client;
use App\Models\Screenshot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use stdClass;

class ScreenshotService
{
    private $storagePath = "/uploads/screenshots/";

    // Store a newly created screenshot on the server.
    public function store(Request $request, $bug, $screenshot, $client_id, ApiCallService $apiCallService)
    {
        $base64 = $screenshot->base64;

        // If the base64 string contains a prefix, remove it
        // if (str_contains($base64, 'base64')) {
        //     $explodedBase64 = explode(',', $base64);
        //     $base64 = $explodedBase64[1];
        // } else {
        // 	$base64 = base64_decode($base64);
        // 	$explodedBase64 = explode(',', $base64);
        //     $base64 = $explodedBase64[1];
        // }


        $interfaceBase64 = "" . $base64;
        $base64 = base64_decode($base64);
        $explodedBase64 = explode(',', $base64);
        $base64 = $explodedBase64[1];

        // Get the mime_type of the screenshot to build the filename with file extension
        $decodedBase64 = base64_decode($base64);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $decodedBase64, FILEINFO_MIME_TYPE);
        $fileName = (preg_replace("/[^0-9]/", "", microtime(true)) . rand(0, 99)) . "." . explode('/', $mime_type)[1];

        // Complete building the path where the screenshot will be stored
        $project = $bug->project;
        $company = $project->company;
        $filePath = $this->storagePath . "$company->id/$project->id/$bug->id/" . $fileName;

        // Store the screenshot in the public storage
        Storage::disk('public')->put($filePath, $decodedBase64);

        try {
            if (config("app.tinypng_active")) {
				CompressImage::dispatch("/app/public" . $filePath);
            }
        } catch (\Exception $e) {
            Log::info($e);
        }

        // Create a new screenshot
        $screenshot = $bug->screenshots()->create([
            "url" => $filePath,
            "client_id" => $client_id,
            "bug_id" => $bug->id,
            "position_x" => $screenshot->position_x,
            "position_y" => $screenshot->position_y,
            "web_position_x" =>  $screenshot->web_position_x,
            "web_position_y" =>  $screenshot->web_position_y,
            "device_pixel_ratio" =>  $screenshot->device_pixel_ratio
        ]);

        $resource = $this->createInterfaceModel($screenshot, $interfaceBase64);
        TriggerInterfacesJob::dispatch($apiCallService, $resource, "bug-updated-sc", $project->id, $request->get('session_id'));
        broadcast(new ScreenshotCreated($screenshot))->toOthers();

        return $screenshot;
    }

    // Delete the screenshot
    public function delete($screenshot)
    {
        $val = $screenshot->delete();
        broadcast(new ScreenshotDeleted($screenshot))->toOthers();

        return $val;
    }
    // Compress the image via tinypng
    public function compressImage($filePath)
    {
        $source = \Tinify\fromFile($filePath);
        $source->toFile($filePath);
    }
    public function createInterfaceModel($screenshot, $base64)
    {
        $sendobj = clone $screenshot;
        $sendobj->base64 = $base64;

        return new ScreenshotInterfaceResource($sendobj);
    }
}
