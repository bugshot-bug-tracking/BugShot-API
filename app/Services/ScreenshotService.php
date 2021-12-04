<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Screenshot;

class ScreenshotService
{
    private $storagePath = "/uploads/screenshots";

    // Store a newly created screenshot on the server.
    public function store($bug, $screenshot)
    {
        // Get the mime_type of the screenshot to build the filename with file extension
        $decodedBase64 = base64_decode($screenshot->base64);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $decodedBase64, FILEINFO_MIME_TYPE);
        $fileName = (preg_replace("/[^0-9]/", "", microtime(true)) . rand(0, 99)) . "." . explode('/', $mime_type)[1];

        // Complete building the path where the screenshot will be stored
        $project = $bug->project;
		$company = $project->company;
        $filePath = $this->storagePath . "/$company->id/$project->id/$bug->id/" . $fileName;

        // Store the screenshot in the public storage
        Storage::disk('public')->put($filePath, $decodedBase64);

        // Create a new screenshot
		$screenshot = $bug->screenshots()->create([
			"url" => $filePath,
			"position_x" => $screenshot->position_x,
			"position_y" => $screenshot->position_y,
			"web_position_x" =>  $screenshot->web_position_x,
			"web_position_y" =>  $screenshot->web_position_y,
		]);

        return $screenshot;
    }
}