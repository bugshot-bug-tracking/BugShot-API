<?php

namespace App\Services;

use App\Models\ApiToken;
use App\Models\Client;
use ErrorException;
use Exception;
use stdClass;
use Illuminate\Support\Facades\Http;

class ApiCallService
{
	function callAPI($method, $url, $data, $headers = false)
	{
		$result = new stdClass();
		$response = null;
		switch ($method) {
			case "POST":
				$response = Http::withHeaders($headers)->post($url, $data);
				break;
			case "PUT":
				$response = Http::withHeaders($headers)->put($url, $data);
				break;
			default:
				$response = Http::withHeaders($headers)->get($url);
				break;
		}
		$result->httpContent = $response->getBody()->getContents();
		$result->httpCode = $response->getStatusCode();
		return $result;
	}

	function getHeader($client_key, $project_id, $uuid = null)
	{
		$BSheaders = array(
			"Accept" => "application/json",
			"Content-Type" => "application/json",
			"project-id" => $project_id,
			"client-key" => $client_key
		);
		if ($uuid != null) {
			$BSheaders["session-id"] = $uuid;
		}

		return $BSheaders;
	}

	function triggerInterfaces($resource, $trigger_id, $project_id, $uuid = null)
	{
		$apitoken_entries = ApiToken::where([
			['api_tokenable_id', '=', $project_id],
		]);

		if ($apitoken_entries->count() > 0) {
			$clients = Client::where('client_url', '!=', '')->get();
			foreach ($clients as $item) {
				$result = $this->callAPI("POST", $item->client_url . "/trigger/" . $trigger_id, $resource, $this->getHeader($item->client_key, $project_id, $uuid));
				if ($result->httpCode != 200 && $result->httpCode != 201) {
					file_put_contents(storage_path('logs/WorkerErrorLog.txt'), json_encode($resource) . "\n" . $result->httpCode . "\n" . $result->httpContent, FILE_APPEND);
				}
			}
		}
	}
}
