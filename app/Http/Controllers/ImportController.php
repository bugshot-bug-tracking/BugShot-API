<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImportController extends Controller
{
	public function getBugherdConnection($apiToken, $password, $parameter)
	{
		$bugherdApiUrl = config('app.bugherd_api_url');

		$response = Http::withOptions([
			'debug' => true,
		])->get(`${bugherdApiUrl}/${parameter}`);

		return $response;
	}

	public function getProjects(Request $request)
	{
		$apiToken = $request->api_token;
		$password = $request->password;
		$response = $this->getBugherdConnection($apiToken, $password, 'projects.json');
	}

	public function importProject()
	{

	}

	public function getBugs(Project $project)
	{

	}

	public function importBug(Project $project)
	{

	}
}
