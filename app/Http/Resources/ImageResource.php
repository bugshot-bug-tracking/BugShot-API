<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		if ($this->resource == NULL) {
			return null;
		}

		$path = "storage" . $this->url;
		try {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);

            // if($this->created_at > "2023-03-06 15:00:00") {
            //     $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            //     $base64 = base64_encode($base64);
            // } else {
            //     $base64 = base64_encode($data);
            // }
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $base64 = base64_encode($base64);

			return [
				"type" => "Image",
				"attributes" => [
					"url" => $this->url,
					"base64" => $base64
				]
			];
		} catch (Exception $e) {
			return [];
		}
	}
}
