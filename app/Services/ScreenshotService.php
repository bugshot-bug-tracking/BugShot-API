<?php

namespace App\Services;

use App\Http\Resources\BugResource;
use App\Http\Resources\ScreenshotResource;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;

class ScreenshotService
{
    private $storagePath = "/uploads/screenshots/";

    // Store a newly created screenshot on the server.
    public function store($bug, $screenshot, $client_id, ApiCallService $apiCallService, $returnBase64 = false)
    {
        $base64 = $screenshot->base64;

        // If the base64 string contains a prefix, remove it
        if (str_contains($base64, 'base64')) {
            $explodedBase64 = explode(',', $base64);
            $base64 = $explodedBase64[1];
        }

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

        // $this->compressImage("storage" . $filePath);

        // Create a new screenshot
        $screenshot = $bug->screenshots()->create([
            "url" => $filePath,
            "client_id" => $client_id,
            "bug_id" => $bug->id,
            "position_x" => $screenshot->position_x,
            "position_y" => $screenshot->position_y,
            "web_position_x" =>  $screenshot->web_position_x,
            "web_position_y" =>  $screenshot->web_position_y
        ]);

        if($returnBase64)
        {$screenshot->base64 = $decodedBase64;}

        $apiCallService->triggerInterfaces(new ScreenshotResource($screenshot), "bug-updated-sc", $project->id);
        return $screenshot;
    }

    // Delete the screenshot
    public function delete($screenshot)
    {
        $val = $screenshot->delete();

        return $val;
    }

    // Compress the image via tinypng
    public function compressImage($filePath)
    {
        $source = \Tinify\fromFile($filePath);
        $source->toFile($filePath);
    }
}
