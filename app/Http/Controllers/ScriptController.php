<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Models\Screenshot;
use App\Models\Image;

class ScriptController extends Controller
{
	public function compressImages() {

		$files = $this->find_files(getcwd() . "/storage/uploads", '*.plain');
		// $files = $this->find_files(getcwd() . "/storage/uploads/screenshots/BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB/CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC/1ccf0906-2776-43ea-903d-fa5a86895ccd/", '*.plain');

		foreach($files as $key=>$file) {
			$data = file_get_contents($file);

			// Check if the data is a correct base64 string
			if(strpos($data, "base64") !== false) {
				$explodedBase64 = explode(',', $data);
				$base64 = $explodedBase64[1];

				// Get the mime_type of the screenshot to build the filename with file extension
				$decodedBase64 = base64_decode($base64);
				$f = finfo_open();
				$mime_type = finfo_buffer($f, $decodedBase64, FILEINFO_MIME_TYPE);
				$fileName = (preg_replace("/[^0-9]/", "", microtime(true)) . rand(0, 99)) . "." . explode('/', $mime_type)[1];

				$disk = Storage::build([
					'driver' => 'local',
					'root' => dirname($file)
				]);

				$disk->put($fileName,  $decodedBase64);

				// Update the url of that screenshot and compress it
				if(strpos($file, "screenshots") !== false) {
					$screenshot = Screenshot::where("url", "like", "%" . basename($file) . "%")->first();
					if($screenshot) {
						try
						{
                            Log::info("Compressing file '" . $file . "' ...");
							if(config("app.tinypng_active")) {
								$this->compressImage(dirname($file) . "/" . $fileName);
							}
						}
						catch (\Exception $e)
						{
							Log::info($e);
						}

						$screenshot->update([
							"url" => str_replace(basename($file), $fileName, $screenshot->url)
						]);

						// Delete the old plain file
						unlink($file);
					} else {
                        Log::info("Deleted file '" . $file . "' because it was not found in the DB.");
                        // Also delete the old file because
                        unlink($file);
						unlink(dirname($file) . "/" . $fileName);
					}
				} else if (strpos($file, "images") !== false) {
					$image = Image::where("url", "like", "%" . basename($file) . "%")->first();
					if($image) {
						try
						{
							if(config("app.tinypng_active")) {
								$this->compressImage(dirname($file) . "/" . $fileName);
							}
						}
						catch (\Exception $e)
						{
							Log::info($e);
						}

						$image->update([
							"url" => str_replace(basename($file), $fileName, $image->url)
						]);

						// Delete the old plain file
						unlink($file);
					} else {
                        // Also delete the old file because
                        unlink($file);
						unlink(dirname($file) . "/" . $fileName);
					}
				}
			}

            Log::info("File [" . $key . "of" . count($files) . "] '" . $file . "' finished.");
		}

		return response()->json([], 200);
	}

    public function find_files($dir, $pattern) {
		$files = glob($dir . '/' . $pattern);
		foreach (glob($dir . '/*', GLOB_ONLYDIR) as $subdir) {
			$files = array_merge($files, $this->find_files($subdir, $pattern));
		}
		return $files;
	}

	// Compress the image via tinypng
	public function compressImage($filePath)
	{
		$source = \Tinify\fromFile($filePath);
		$source->toFile($filePath);
	}
}
