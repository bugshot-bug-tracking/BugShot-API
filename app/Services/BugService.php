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
	public function store(BugStoreRequest $request, Status $status, $bugId, ScreenshotService $screenshotService, AttachmentService $attachmentService, ApiCallService $apiCallService)
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
				$screenshotService->store($bug, $screenshot, $client_id, $apiCallService);
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

		return $apiCallService->triggerInterfaces(new BugResource($bug), "bug-created", $status->project_id);
	}

	public function update(BugUpdateRequest $request, Status $status, Bug $bug, ApiCallService $apiCallService)
	{
		$oldStatus = $bug->getOriginal('status_id');
		$newStatus = isset($request->status_id) && $request->status_id != null ? $request->status_id : $oldStatus;
		
		// Check if the order of the bugs or the status has to be synchronized
		if (($request->order_number != $bug->getOriginal('order_number') && $request->has('order_number')) || ($newStatus != $oldStatus && $request->has('status_id'))) {
			$this->synchronizeBugOrder($request, $bug, $status);
		}

		// Update the bug
		$bug->update($request->all());
		$bug->update([
			"project_id" => $status->project_id,
			"deadline" => $request->deadline ? new Carbon($request->deadline) : null,
		]);

		// if status equal to old one send normal update Trigger else send status update trigger
		if ($newStatus == $oldStatus) {
			return $apiCallService->triggerInterfaces(new BugResource($bug), "bug-updated-info", $status->project_id);
		} else {
			$request->headers->set('include-status-info', 'true');
			$sendBug = json_decode(((new BugResource($bug))->response($request))->content());
			return $apiCallService->triggerInterfaces($sendBug, "bug-updated-status", $status->project_id);
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

	// Synchronize the order numbers of all the bugs, that are affected by the updated bug
	private function synchronizeBugOrder($request, $bug, $status)
	{
		$originalOrderNumber = $bug->getOriginal('order_number');
		$newOrderNumber = $request->order_number;

		// Check if the bug also changed it's status
		if ($request->status_id != $bug->getOriginal('status_id') && $request->has('status_id')) {
			$originalStatusBugs = $status->bugs->where('order_number', '>', $originalOrderNumber);

			// Descrease all the order numbers that were greater than the original bug order number
			foreach ($originalStatusBugs as $originalStatusBug) {
				$originalStatusBug->update([
					"order_number" => $originalStatusBug->order_number - 1
				]);
			}

			$newStatus = Status::find($request->status_id);
			$newStatusBugs = $newStatus->bugs->where('order_number', '>=', $newOrderNumber);

			// Increase all the order numbers that are greater than the original bug order number
			foreach ($newStatusBugs as $newStatusBug) {
				$newStatusBug->update([
					"order_number" => $newStatusBug->order_number + 1
				]);
			}
		} else {
			// Check wether the original or new order_number is bigger because ->whereBetween only works when the first array parameter is smaller than the second
			if ($originalOrderNumber < $newOrderNumber) {
				$statusBugs = $status->bugs->whereBetween('order_number', [$originalOrderNumber, $newOrderNumber]);
			} else {
				$statusBugs = $status->bugs->whereBetween('order_number', [$newOrderNumber, $originalOrderNumber]);
			}

			// Change the order number of all affected bugs
			foreach ($statusBugs as $statusBug) {
				$statusBug->update([
					"order_number" => $originalOrderNumber < $newOrderNumber ? $statusBug->order_number - 1 : $statusBug->order_number + 1
				]);
			}
		}
	}
}
