<?php

namespace App\Services;

use App\Models\Client;
use stdClass;

class ApiCallService
{
	function callAPI($method, $url, $data, $headers = false)
	{
		$curl = curl_init();
		switch ($method) {
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);
				if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case "PUT":
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			default:
				if (isset($data) && $data != null)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		}

		// Optional Authentication:
		// if ($auth) {
		//     //Change to bearer token
		//     curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//     curl_setopt($curl, CURLOPT_USERPWD, "username:password");
		// }

		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		if ($headers)
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = new stdClass();
		$result->httpContent = json_decode(curl_exec($curl));
		$result->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		return $result;
	}


	function getBsHeader($client_key, $project_id)
	{
		$authorization = "client-key: " . $client_key;

		$BSheaders = array(
			"Accept: application/json",
			"Content-Type: application/json",
			"project-id: " . $project_id,
			$authorization,
		);

		return $BSheaders;
	}

	function triggerInterfaces($resource, $trigger_id, $project_id)
	{
		$clients = Client::where('client_url', '!=', '')->get();
		foreach ($clients as $item) {
			$this->callAPI("POST", $item->client_url . "/trigger/" . $trigger_id, json_encode($resource), $this->getBsHeader($item->client_key, $project_id));
		}

		return $resource;
	}
}
