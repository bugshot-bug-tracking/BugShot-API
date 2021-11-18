<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Image;

class ImageService
{
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
                $this->destroy($image);
            }
        }

        // Build the path where the image will be stored
        $storagePath = "/uploads/images/";
        $fileName = (preg_replace("/[^0-9]/", "", microtime(true)) . rand(0, 99));
        $filePath = $storagePath . $fileName;

        // Get the mime_type of the image and complete building the files path of storage
        $decodedBase64 = base64_decode($base64);
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

        // Store the image in the public storage
        Storage::disk('public')->put($filePath, base64_decode($base64));

        // Create a new image model
        $image = new Image([
            "url" => $filePath
        ]);

        return $image;
    }

    public function destroy($image)
    {
        $image->update([
            "deleted_at" => new \DateTime()
        ]);
    }
}