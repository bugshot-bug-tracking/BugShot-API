<?php

namespace App\Services;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

// Resources
use App\Http\Resources\BugResource;
use App\Http\Resources\BugUserRoleResource;

// Services
use App\Services\ScreenshotService;
use App\Services\AttachmentService;
use App\Services\CommentService;

// Models
use App\Models\Bug;
use App\Models\User;
use App\Models\Status;
use App\Models\BugUserRole;

// Requests
use App\Http\Requests\BugStoreRequest;
use App\Http\Requests\BugUpdateRequest;

// Events
use App\Events\AssignedToBug;
use App\Http\Controllers\BugController;
use App\Models\Client;

class BugService
{
	public function store(BugStoreRequest $request, Status $status, $bugId, ScreenshotService $screenshotService, AttachmentService $attachmentService)
	{
		// Get user information if a api key was used
		$tempProject = $request->get('project');
		$client_id = $request->get('client_id');
		if ($tempProject != NULL) {
			$creator_id = $tempProject->user_id;
		} else {
			$creator_id = Auth::user()->id;
		}


		// Get the max order number in this status and increase it by one
		$order_number = $status->bugs->isEmpty() ? 0 : $status->bugs->max('order_number') + 1;

		// Determine the number of bugs in the project to generate the $ai_id
		$allBugsQuery = $status->project->bugs()->withTrashed();
		$numberOfBugs = $allBugsQuery->count();
		$ai_id = $allBugsQuery->get()->isEmpty() ? 0 : $numberOfBugs + 1;

		// Store the new bug in the database
		$bug = $status->bugs()->create([
			"id" => $bugId,
			"project_id" => $status->project_id,
			"user_id" => $creator_id,
			"priority_id" => $request->priority_id,
			"designation" => $request->designation,
			"description" => $request->description,
			"url" => $request->url,
			"operating_system" => $request->operating_system,
			"browser" => $request->browser,
			"selector" => $request->selector,
			"resolution" => $request->resolution,
			"deadline" => $request->deadline == NULL ? null : new Carbon($request->deadline),
			"order_number" => $order_number,
			"ai_id" => $ai_id,
			"client_id" => $client_id
		]);

		// Check if the bug comes with a screenshot (or multiple) and if so, store it/them
		$screenshots = $request->screenshots;
		if ($screenshots != NULL) {
			foreach ($screenshots as $screenshot) {
				$screenshot = (object) $screenshot;
				$screenshotService->store($bug, $screenshot, $client_id);
			}
		}


		// Check if the bug comes with a attachment (or multiple) and if so, store it/them
		$attachments = $request->attachments;
		if ($attachments != NULL) {
			foreach ($attachments as $attachment) {
				$attachment = (object) $attachment;
				$attachmentService->store($bug, $attachment);
			}
		}

		return $this->triggerInterfaces(new BugResource($bug), 1, $status->project_id);
	}

	public function update(BugUpdateRequest $request, BugController $controller, Status $status, Bug $bug)
	{
		// Check if the order of the bugs or the status has to be synchronized
		if (($request->order_number != $bug->getOriginal('order_number') && $request->has('order_number')) || ($request->status_id != $bug->getOriginal('status_id') && $request->has('status_id'))) {
			$controller->synchronizeBugOrder($request, $bug, $status);
		}

		// Update the bug
		$bug->update($request->all());
		$bug->update([
			"project_id" => $status->project_id,
			"deadline" => $request->deadline ? new Carbon($request->deadline) : null,
		]);

		// if status equal to old one send normal update Trigger else send status update trigger
		if ($request->status_id !=  null && $status->id == $request->status_id) {
			return $this->triggerInterfaces(new BugResource($bug), 2, $status->project_id);
		} else {
			// Add status?
			return $this->triggerInterfaces(new BugResource($bug), 4, $status->project_id);
		}

	}

	public function destroy(Status $status, Bug $bug, ScreenshotService $screenshotService, CommentService $commentService, AttachmentService $attachmentService)
	{
		$val = $bug->delete();

		// // Delete the respective screenshots
		// foreach ($bug->screenshots as $screenshot) {
		// 	$screenshotService->delete($screenshot);
		// }

		// // Delete the respective comments
		// foreach ($bug->comments as $comment) {
		// 	$commentService->delete($comment);
		// }

		// // Delete the respective attachments
		// foreach ($bug->attachments as $attachment) {
		// 	$attachmentService->delete($attachment);
		// }

		return response($val, 204);
	}

	public function triggerInterfaces(BugResource $bug, $trigger_id, $project_id)
	{
		$clients = Client::where('client_url', '!=', '')->get();
		foreach ($clients as $item) {
			(new ApiCallService)->callAPI("POST", $item->client_url . "/trigger/" . $trigger_id, json_encode($bug), getBsHeader($item->client_key, $project_id));
		}
		// Fehlerprüfung?
		return $bug;
	}
}
