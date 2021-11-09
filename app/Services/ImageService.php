<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageService
{
    // Store a newly created image on the server.
    public function store($base64)
    {
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

        return $filePath;
    }
}