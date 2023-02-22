<?php

namespace App\Services;

use App\Events\ScreenshotCreated;
use App\Events\ScreenshotDeleted;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;

class AttachmentService
{
    private $storagePath = "/uploads/attachments/";

    // Store a newly created attachment on the server.
    public function store($bug, $attachment)
    {
        // Get the mime_type of the attachment to build the filename with file extension
        $decodedBase64 = base64_decode($attachment->base64);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $decodedBase64, FILEINFO_MIME_TYPE);
        $fileName = (preg_replace("/[^0-9]/", "", microtime(true)) . rand(0, 99)) . "." . explode('/', $mime_type)[1];

        // Complete building the path where the attachment will be stored
        $project = $bug->project;
		$company = $project->company;
        $filePath = $this->storagePath . "$company->id/$project->id/$bug->id/" . $fileName;

        // Store the attachment in the public storage
        Storage::disk('public')->put($filePath, $decodedBase64);

        // Create a new attachment
		$attachment = $bug->attachments()->create([
			"url" => $filePath,
			"designation" => $attachment->designation
		]);

        broadcast(new ScreenshotCreated($attachment))->toOthers();

        return $attachment;
    }

    // Delete the attachment
    public function delete($attachment) 
    {
        $val = $attachment->delete();
        broadcast(new ScreenshotDeleted($attachment))->toOthers();

        return $val;
    }
}