<?php

namespace App\Services;

// Miscellaneous, Helpers, ...
use App\Events\CommentCreated;
use App\Http\Requests\BugUpdateRequest;
use Illuminate\Http\Request;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Resources
use App\Http\Resources\PostJiraBugResource;

// Services

// Models
use App\Models\Bug;
use App\Models\Client;
use App\Models\Comment;
use App\Models\JiraBugLink;
use App\Models\JiraCommentLink;
use App\Models\Project;
use App\Models\JiraProjectLink;
use App\Models\JiraWebhooks;

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

		$date = ($link->expires_at);
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
				$query = "&query=" . urlencode($request->query->get('query'));

			$response = Http::withHeaders([
				"Content-Type" => "application/json",
				"Accept" => "application/json",
				"Authorization" => $project->jiraLink->token_type . " " . $project->jiraLink->access_token,
			])->withUrlParameters([
				'endpoint' => 'https://api.atlassian.com/ex/jira',
				'site_id' => $project->jiraLink->site_id,
				'api_path' => 'rest/api/2/project/search',
			])->get('{+endpoint}/{site_id}/{+api_path}' . '?action=create&expand=issueTypes' . $query);

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
				'endpoint' => 'https://api.atlassian.com/ex/jira',
				'site_id' => $bug->project->jiraLink->site_id,
				'api_path' => 'rest/api/2/issue',
			])->post('{+endpoint}/{site_id}/{+api_path}', new PostJiraBugResource($bug));

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
				'endpoint' => 'https://api.atlassian.com/ex/jira',
				'site_id' => $bug->project->jiraLink->site_id,
				'api_path' => 'rest/api/2/issue',
				"issue_id" => $bug->jiraLink->issue_id,
			])->post('{+endpoint}/{site_id}/{+api_path}/{issue_id}/comment', [
				"body" => $comment->user->first_name . " " . $comment->user->last_name . " (BugShot): " . $processedContent
			]);

			if ($response->status() === 401 && $attempts === 0) {
				self::updateToken($bug->project);
				$attempts++;
				continue;
			}

			$body = json_decode($response->body());

			JiraCommentLink::create([
				'comment_id' => $comment->id,
				'jira_comment_id' => $body->id,
				"jira_comment_url" => $body->self
			]);

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
				'endpoint' => 'https://api.atlassian.com/ex/jira',
				'site_id' => $bug->project->jiraLink->site_id,
				'api_path' => 'rest/api/2/issue',
				"issue_id" => $bug->jiraLink->issue_id,
			])->post('{+endpoint}/{site_id}/{+api_path}/{issue_id}/attachments');

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

	public static function refreshWebhook($event, Project $project)
	{
		// check if the event is one that requires a webhook
		switch ($event) {
			case 'sync_comments_from_jira':
				$jira_event = "comment_created";
				break;

			case 'update_status_from_jira':
				$jira_event = "jira:issue_updated";
				break;

			default:
				$jira_event = NULL;
		}

		// if the event provided does not match any valid case exit without doing anything
		if (is_null($jira_event)) {
			return;
		}

		// get all the project links that the user has created that have the desired local event
		$project_link_list = JiraProjectLink::where([
			["user_id", Auth::id()],
			[$event, true],
			["jira_project_id", '!=', NULL],
		])->get()->toArray();

		// check for an existing webhook with the desired event and creator
		$webhook = JiraWebhooks::where([
			["user_id", Auth::id()],
			['event', $jira_event],
		])->first();

		// self::getWebhooks($project);

		// if there are no eligible project but a webhook is present delete it and after that's done return
		if (empty($project_link_list)) {
			if ($webhook) {
				self::deleteWebhook($webhook->webhook_id, $project);
			}

			return;
		}

		if ($webhook) {
			self::deleteWebhook($webhook->webhook_id, $project);
		}

		self::createWebhook($project_link_list, $jira_event, $project);

		// self::getWebhooks($project);
	}

	public static function createWebhook($jira_project_links, $jira_event, $project)
	{
		self::preCallCheck($project);

		$attempts = 0;

		while ($attempts < 2) {
			$projects_list = array_map(function ($link) {
				return $link['jira_project_id'];
			}, $jira_project_links);

			$response = Http::withHeaders([
				"Content-Type" => "application/json",
				"Accept" => "application/json",
				"Authorization" => $project->jiraLink->token_type . " " . $project->jiraLink->access_token,
			])->withUrlParameters([
				'endpoint' => 'https://api.atlassian.com/ex/jira',
				'site_id' => $project->jiraLink->site_id,
				'api_path' => 'rest/api/2/webhook',
			])->post('{+endpoint}/{site_id}/{+api_path}', [
				"url" => env("APP_PUBLIC_URL") . "/api/v1/atlassian/webhook",
				"webhooks" => [
					[
						"events" => [$jira_event],
						"jqlFilter" => "project IN (" . implode(",", $projects_list) . ")",
					]
				]
			]);

			if ($response->status() === 401 && $attempts === 0) {
				self::updateToken($project);
				$attempts++;
				continue;
			}

			$body = json_decode($response->body());

			$jira_webhook = JiraWebhooks::create([
				"webhook_id" => $body->webhookRegistrationResult[0]->createdWebhookId,
				"user_id" => Auth::id(),
				"event" => $jira_event
			]);

			return $body;
		}

		return false;
	}

	public static function deleteWebhook($webhook_id, $project)
	{
		self::preCallCheck($project);

		$attempts = 0;

		while ($attempts < 2) {
			$response = Http::withHeaders([
				"Content-Type" => "application/json",
				"Accept" => "application/json",
				"Authorization" => $project->jiraLink->token_type . " " . $project->jiraLink->access_token,
			])->withUrlParameters([
				'endpoint' => 'https://api.atlassian.com/ex/jira',
				'site_id' => $project->jiraLink->site_id,
				'api_path' => 'rest/api/2/webhook',
			])->delete('{+endpoint}/{site_id}/{+api_path}', [
				"webhookIds" => [$webhook_id]
			]);

			if ($response->status() === 401 && $attempts === 0) {
				self::updateToken($project);
				$attempts++;
				continue;
			}

			$webhook = JiraWebhooks::where("webhook_id", $webhook_id)->first()->delete();

			$body = json_decode($response->body());

			return $body;
		}

		return false;
	}

	public static function getWebhooks($project)
	{
		self::preCallCheck($project);

		$attempts = 0;

		while ($attempts < 2) {
			$response = Http::withHeaders([
				"Content-Type" => "application/json",
				"Accept" => "application/json",
				"Authorization" => $project->jiraLink->token_type . " " . $project->jiraLink->access_token,
			])->withUrlParameters([
				'endpoint' => 'https://api.atlassian.com/ex/jira',
				'site_id' => $project->jiraLink->site_id,
				'api_path' => 'rest/api/2/webhook',
			])->get('{+endpoint}/{site_id}/{+api_path}');

			if ($response->status() === 401 && $attempts === 0) {
				self::updateToken($project);
				$attempts++;
				continue;
			}

			$body = json_decode($response);

			return $body;
		}

		return false;
	}

	public function handleWebhook(Request $request)
	{
		if ($request->webhookEvent === "jira:issue_updated") {
			$bug_link = JiraBugLink::where("issue_id", $request->issue['id'])->orWhere("issue_key", $request->issue['key'])->first();

			if ($bug_link && $bug_link->projectLink->update_status_from_jira == true) {
				self::updateBugShotStatusToDone($request, $bug_link);
			}
		}

		if ($request->webhookEvent === "comment_created") {
			$bug_link = JiraBugLink::where("issue_id", $request->issue['id'])->orWhere("issue_key", $request->issue['key'])->first();

			if ($bug_link && $bug_link->projectLink->sync_comments_from_jira == true) {
				self::createBugShotComment($request, $bug_link);
			}
		}
	}

	private static function createBugShotComment($request, JiraBugLink $bug_link)
	{
		// prevent creation of duplicate entries
		if (JiraCommentLink::where("jira_comment_id", $request->comment['id'])->first())
			return;

		// Store the new comment in the database
		$comment = $bug_link->bug->comments()->create([
			"id" => Str::uuid(),
			'content' => $request->comment['author']['displayName'] . " (Jira): " . $request->comment['body'],
			'user_id' => $bug_link->projectLink->user->id,
			"client_id" => Client::where("designation", "atlassian integration")->first()->id,
			"created_at" => new Carbon($request->timestamp / 1000)
		]);

		JiraCommentLink::create([
			'comment_id' => $comment->id,
			'jira_comment_id' => $request->comment['id'],
			"jira_comment_url" => $request->comment['self']
		]);

		// Broadcast the event
		broadcast(new CommentCreated($comment));
	}

	private static function updateBugShotStatusToDone($request, JiraBugLink $bug_link)
	{
		// is the status that was changed is not "Done" exit
		if (!($request->issue['fields']['status']['id'] === '10001')) return;

		$done = $bug_link->projectLink->project->statuses->where("permanent", "done")->first();

		// if the done status for the project is not found exit
		if (!$done) return;

		// if the bug is already in the done status exit
		if ($bug_link->bug->status->permanent === 'done') return;

		$bugService = new BugService;

		$update_request = new BugUpdateRequest(
			[
				"status_id" => $done->id
			]
		);

		$bugService->update(
			$update_request,
			$bug_link->bug->status,
			$bug_link->bug
		);
	}
}
