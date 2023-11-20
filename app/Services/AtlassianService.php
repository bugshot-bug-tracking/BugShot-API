<?php

namespace App\Services;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Request;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

// Resources
use App\Http\Resources\PostJiraBugResource;

// Services

// Models
use App\Models\Bug;
use App\Models\Comment;
use App\Models\JiraBugLink;
use App\Models\Project;
use App\Models\JiraProjectLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AtlassianService
{
	public static function createJiraLink(Request $request, Project $project)
	{
		$response = Http::withHeaders([
			"Content-Type" => "application/json"
		])->post("https://auth.atlassian.com/oauth/token", [
			'grant_type' => "authorization_code",
			'client_id' => env('ATLASSIAN_CLIENT_ID', ''),
			'client_secret' => env('ATLASSIAN_CLIENT_SECRET', ''),
			'code' =>  $request->code,
			'redirect_uri' => env('APP_WEBPANEL_URL', 'http://localhost:8080') . "/extra/integrations/atlassian",
		]);

		$body = json_decode($response->body());

		$expires_at = new DateTime();
		$expires_at->modify("+$body->expires_in seconds");

		//TODO after creating the link make a request to get the sites and if only 1 site is available set it as the active one so that 1 step is removed from the user needed actions
		return JiraProjectLink::create([
			"user_id" => Auth::id(),
			"project_id" => $project->id,
			"token_type" => $body->token_type,
			"access_token" => $body->access_token,
			"refresh_token" => $body->refresh_token,
			"expires_in" => $body->expires_in,
			"expires_at" => $expires_at,
			"scope" => $body->scope,
		]);
	}

	private static function preCallCheck(Project $project)
	{
		$link = $project->jiraLink;

		if (!$link) {
			throw new Exception("Project has no link");
		}

		$date = new DateTime($link->expires_at);
		$current_date = new DateTime();

		if ($date < $current_date) {
			self::updateToken($project);
		}
	}

	private static function updateToken(Project $project)
	{
		$link = $project->jiraLink;

		$response = Http::withHeaders([
			"Content-Type" => "application/json"
		])->post("https://auth.atlassian.com/oauth/token", [
			'grant_type' => "refresh_token",
			'client_id' => env('ATLASSIAN_CLIENT_ID', ''),
			'client_secret' => env('ATLASSIAN_CLIENT_SECRET', ''),
			'refresh_token' =>  $link->refresh_token,
		]);

		$data = json_decode($response);

		$expires_at = new DateTime();
		$expires_at->modify("+$data->expires_in seconds");

		$link->update([
			"token_type" => $data->token_type,
			"access_token" => $data->access_token,
			"refresh_token" => $data->refresh_token,
			"expires_in" => $data->expires_in,
			"expires_at" => $expires_at,
			"scope" => $data->scope,
		]);

		return true;
	}

	public static function getSites(Project $project)
	{
		self::preCallCheck($project);

		$attempts = 0;

		while ($attempts < 2) {
			$response = Http::withHeaders([
				"Content-Type" => "application/json",
				"Authorization" => $project->jiraLink->token_type . " " . $project->jiraLink->access_token,
				"Accept" => "application/json",
			])->get("https://api.atlassian.com/oauth/token/accessible-resources");

			if ($response->status() === 401 && $attempts === 0) {
				self::updateToken($project);
				$attempts++;
				continue;
			}

			return $response;
		}

		return [];
	}

	public static function getProjects(Request $request, Project $project)
	{
		self::preCallCheck($project);

		$attempts = 0;

		while ($attempts < 2) {

			$query = "";

			if ($request->query->get('query'))
				$query = "?query=" . $request->query->get('query');

			$response = Http::withHeaders([
				"Content-Type" => "application/json",
				"Accept" => "application/json",
				"Authorization" => $project->jiraLink->token_type . " " . $project->jiraLink->access_token,
			])->get("https://api.atlassian.com/ex/jira/" . $project->jiraLink->site_id . "/rest/api/2/project/search" . $query);

			if ($response->status() === 401 && $attempts === 0) {
				self::updateToken($project);
				$attempts++;
				continue;
			}

			return $response;
		}

		return [];
	}

	public static function createLinkedIssue(Bug $bug)
	{
		self::preCallCheck($bug->project);

		$attempts = 0;

		while ($attempts < 2) {
			$response = Http::withHeaders([
				"Content-Type" => "application/json",
				"Accept" => "application/json",
				"Authorization" => $bug->project->jiraLink->token_type . " " . $bug->project->jiraLink->access_token,
			])->withUrlParameters([
				'endpoint' => 'https://api.atlassian.com/ex/jira/',
				'site_id' => $bug->project->jiraLink->site_id,
				"api_path" => "rest/api/2/issue",
			])->post('{+endpoint}/{site_id}/{api_path}', new PostJiraBugResource($bug));

			if ($response->status() === 401 && $attempts === 0) {
				self::updateToken($bug->project);
				$attempts++;
				continue;
			}

			Log::info(($response->body()));

			$body = json_decode($response->body());

			return JiraBugLink::create([
				"project_link_id" => $bug->project->jiraLink->id,
				"bug_id" => $bug->id,
				"issue_id" => $body->id,
				"issue_key" => $body->key,
				"issue_url" => $body->self
			]);

			return $response;
		}

		return false;
	}

	public static function createComment(Bug $bug, Comment $comment)
	{
		self::preCallCheck($bug->project);

		$attempts = 0;

		while ($attempts < 2) {
			// regex pattern for tagged users
			$pattern = '/\<([0-9]+)\$\@(.+?)\>/i';

			// replacement
			$replacement = "@$2";  // $2 is a backreference pointing to the second captured group in your pattern which is the user name.

			// Perform replacement before using comment content
			$processedContent = preg_replace($pattern, $replacement, $comment->content);

			$response = Http::withHeaders([
				"Content-Type" => "application/json",
				"Accept" => "application/json",
				"Authorization" => $bug->project->jiraLink->token_type . " " . $bug->project->jiraLink->access_token,
			])->withUrlParameters([
				'endpoint' => 'https://api.atlassian.com/ex/jira/',
				'site_id' => $bug->project->jiraLink->site_id,
				"api_path" => "rest/api/2/issue",
				"issue_id" => $bug->jiraLink->issue_id,
			])->post('{+endpoint}/{site_id}/{api_path}/{issue_id}/comment', [
				"body" => $comment->user->first_name . " " . $comment->user->last_name . " (BugShot): " . $processedContent
			]);

			if ($response->status() === 401 && $attempts === 0) {
				self::updateToken($bug->project);
				$attempts++;
				continue;
			}

			$body = json_decode($response->body());

			return $body;
		}

		return false;
	}

	public static function sendAttachment($filePath, $fileName, Bug $bug)
	{
		self::preCallCheck($bug->project);

		$attempts = 0;

		while ($attempts < 2) {
			$contents = Storage::disk('public')->get($filePath);

			$response = Http::withHeaders([
				"Accept" => "application/json",
				"Authorization" => $bug->project->jiraLink->token_type . " " . $bug->project->jiraLink->access_token,
				"X-Atlassian-Token" => "no-check"
			])->attach(
				"file",
				$contents,
				$fileName
			)->withUrlParameters([
				'endpoint' => 'https://api.atlassian.com/ex/jira/',
				'site_id' => $bug->project->jiraLink->site_id,
				"api_path" => "rest/api/2/issue",
				"issue_id" => $bug->jiraLink->issue_id,
			])->post('{+endpoint}/{site_id}/{api_path}/{issue_id}/attachments');

			if ($response->status() === 401 && $attempts === 0) {
				self::updateToken($bug->project);
				$attempts++;
				continue;
			}

			$body = json_decode($response->body());

			var_dump($body);

			return $body;
		}

		return false;
	}

}
