<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait Bugherd
{
    /**
     * @param string $apiToken
     * @return string $parameter
     */
	public static function sendBugherdRequest($apiToken, $parameter)
	{
		$bugherdApiUrl = config('app.bugherd_api_url');
		$response = Http::withBasicAuth($apiToken, 'x')->get("${bugherdApiUrl}/${parameter}");

		return $response;
	}
}
