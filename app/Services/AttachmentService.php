<?php

namespace App\Services;

use App\Events\ScreenshotCreated;
use App\Events\ScreenshotDeleted;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;
use Illuminate\Support\Facades\Log;

class AttachmentService
{
	private $storagePath = "/uploads/attachments/";

	// Store a newly created attachment on the server.
	public function store($bug, $attachment)
	{
		$base64 = base64_decode($attachment->base64);
		$explodedBase64 = explode(',', $base64);
		$base64 = $explodedBase64[1];

		// Get the mime_type of the attachment to build the filename with file extension
		$decodedBase64 = base64_decode($base64);
		$f = finfo_open();
		$mime_type = finfo_buffer($f, $decodedBase64, FILEINFO_MIME_TYPE);
		$mime_type = explode('/', $mime_type)[1];
		$fileName = (preg_replace("/[^0-9]/", "", microtime(true)) . rand(0, 99)) . "." . $mime_type;

		// Complete building the path where the attachment will be stored
		$project = $bug->project;
		$company = $project->company;
		$filePath = $this->storagePath . "$company->id/$project->id/$bug->id/" . $fileName;

		// Store the attachment in the public storage
		Storage::disk('public')->put($filePath, $decodedBase64);

		if ($mime_type == "jpeg" || $mime_type == "webp" || $mime_type == "jpg" || $mime_type == "png") {
			try {
				if (config("app.tinypng_active")) {
					$this->compressImage("storage" . $filePath);
				}
			} catch (\Exception $e) {
				Log::info($e);
			}
		}

		// Create a new attachment
		$attachment = $bug->attachments()->create([
			"url" => $filePath,
			"designation" => $attachment->designation
		]);

		if ($bug->project->jiraLink && $bug->project->jiraLink->sync_bugs_to_jira == true && $bug->jiraLink) {
			AtlassianService::sendAttachment($filePath, $attachment->designation, $bug);
		}

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

	// Compress the image via tinypng
	public function compressImage($filePath)
	{
		$source = \Tinify\fromFile($filePath);
		$source->toFile($filePath);
	}
}
