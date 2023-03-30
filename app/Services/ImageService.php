<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Image;
use App\Jobs\CompressImage;
use Illuminate\Support\Facades\Log;

class ImageService
{
    private $storagePath = "/uploads/images/";

    // Store a newly created image on the server.
    public function store($base64, $image)
    {
        // Compare if the old image is different two the new one so no duplicate will be stored
        if($image != NULL) {
            $md5OldImage = md5(base64_encode(Storage::disk('public')->get($image->url)));
            $md5NewImage = md5($base64);
            if ($md5OldImage == $md5NewImage) {
                return false;
            } else {
                $this->delete($image);
            }
        }

		$base64 = base64_decode($base64);
		$explodedBase64 = explode(',', $base64);
		$base64 = $explodedBase64[1];

        // Get the mime_type of the image to build the filename with file extension
		$decodedBase64 = base64_decode($base64);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $decodedBase64, FILEINFO_MIME_TYPE);
        $fileName = (preg_replace("/[^0-9]/", "", microtime(true)) . rand(0, 99)) . "." . explode('/', $mime_type)[1];

        // Complete building the path where the image will be stored
        $filePath = $this->storagePath . $fileName;

        // Store the image in the public storage
        Storage::disk('public')->put($filePath, $decodedBase64);

		try
		{
			if(config("app.tinypng_active")) {
				CompressImage::dispatch("storage" . $filePath);
			}
		}
		catch (\Exception $e)
		{
			Log::info($e);
		}

        // Create a new image model
        $image = new Image([
            "url" => $filePath
        ]);

        return $image;
    }

    public function delete($image)
    {
        if($image != NULL) {
            $val = $image->delete();

            return $val;
        }
    }

    // Compress the image via tinypng
    public function compressImage($filePath)
	{
        $source = \Tinify\fromFile($filePath);
        $source->toFile($filePath);
    }
}

