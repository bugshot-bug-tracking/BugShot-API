<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Screenshot;

class ScreenshotService
{
    // Store a newly created screenshot on the server.
    public function store($bug_id, $screenshot)
    {
        // Build the path where the screenshot will be stored
        $storagePath = "/uploads/screenshots/";
        $fileName = (preg_replace("/[^0-9]/", "", microtime(true)) . rand(0, 99));
        $filePath = $storagePath . $fileName;

        // Get the mime_type of the screenshot and complete building the files path of storage
        $decodedBase64 = base64_decode($screenshot->base64);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $decodedBase64, FILEINFO_MIME_TYPE);

        switch ($mime_type) {
            case 'image/jpeg':
                $filePath .= ".jpeg";
                break;
            
            case 'image/gif':
                $filePath .= ".gif";
                break;
                
            case 'image/png':
                $filePath .= ".png";
                break;
        }

        // Store the screenshot in the public storage
        Storage::disk('public')->put($filePath, base64_decode($screenshot->base64));

		Screenshot::create([
			"bug_id" => $bug_id,
			"designation" => $screenshot->designation,
			"url" => $filePath,
			"position_x" => $screenshot->position_x,
			"position_y" => $screenshot->position_y,
			"web_position_x" =>  $screenshot->web_position_x,
			"web_position_y" =>  $screenshot->web_position_y,
		]);

        return true;
    }
}