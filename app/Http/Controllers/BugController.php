<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\BugResource;

// Services
use App\Services\ScreenshotService;
use App\Services\AttachmentService;
use App\Services\CommentService;

// Models
use App\Models\Bug;
use App\Models\User;
use App\Models\Status;

// Requests
use App\Http\Requests\BugRequest;


/**
 * @OA\Tag(
 *     name="Bug",
 * )
 */
class BugController extends Controller
{

	/**
	 * @OA\Get(
	 *	path="/statuses/{status_id}/bugs",
	 *	tags={"Bug"},
	 *	summary="All bugs.",
	 *	operationId="allBugs",
	 *	security={ {"sanctum": {} }},
	 *	@OA\Parameter(
	 *		name="status_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachments",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-comments",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-bug-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachment-base64",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Bug")
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 *)
	 *
	 **/
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request, Status $status)
	{
		// Check if the user is authorized to list the bugs of the project
		$this->authorize('viewAny', [Bug::class, $status->project]);

		// Get timestamp
		$timestamp = $request->header('timestamp');

		// Check if the request includes a timestamp and query the bugs accordingly
		if($timestamp == NULL) {
            $bugs = $status->bugs;
        } else {
            $bugs = $status->bugs->where(["bugs.updated_at", ">", date("Y-m-d H:i:s", $timestamp)]);
        }
		
		return BugResource::collection($bugs);
	}

	/**
	 * @OA\Post(
	 *	path="/statuses/{status_id}/bugs",
	 *	tags={"Bug"},
	 *	summary="Store one bug.",
	 *	operationId="storeBug",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="status_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
	 *		)
	 *	),
	 * 
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The bug name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug description",
	 *                  property="description",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug url",
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="priority_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *  			@OA\Property(
	 *                  property="operating_system",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="browser",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="selector",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="resolution",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="deadline",
	 *                  type="string",
	 * 					format="date-time",
	 *              ),
	 *   			@OA\Property(
	 *                  property="screenshots",
	 *                  type="array",
	 * 					@OA\Items(
	 * 	   					@OA\Property(
	 *              		    property="base64",
	 *              		    type="string"
	 *              		),
	 *  					@OA\Property(
	 *              		    property="position_x",
	 *              		    type="integer",
	 *              		    format="int32",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="position_y",
	 *              		    type="integer",
	 *              		    format="int32",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="web_position_x",
	 *              		    type="integer",
	 *              		    format="int32",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="web_position_y",
	 *              		    type="integer",
	 *              		    format="int32",
	 *              		)
	 * 					)
	 *              ),
	 *   			@OA\Property(
	 *                  property="attachments",
	 *                  type="array",
	 * 					@OA\Items(
	 * 	   					@OA\Property(
	 *              		    property="base64",
	 *              		    type="string"
	 *              		),
	 *  					@OA\Property(
	 *              		    property="designation",
	 *              		    type="string"
	 *              		)
	 * 					)
	 *              ),
	 *              required={"designation","url","status_id","priority_id",}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Bug"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\BugRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(BugRequest $request, Status $status, ScreenshotService $screenshotService, AttachmentService $attachmentService)
	{
		// Check if the user is authorized to create the bug 
		$this->authorize('create', [Bug::class, $status->project]);

		// Check if the the request already contains a UUID for the bug
		$id = $this->setId($request);
		
		// Get the max order number in this status and increase it by one
		$order_number = $status->bugs->max('order_number') + 1;

		// Determine the number of bugs in the project to generate the $ai_id
		$numberOfBugs = $status->project->bugs()->withTrashed()->count();
		$ai_id = $numberOfBugs + 1;

		// Store the new bug in the database
		$bug = $status->bugs()->create([
			"id" => $id,
			"project_id" => $status->project_id,
			"user_id" => Auth::user()->id,
			"priority_id" => $request->priority_id,
			"designation" => $request->designation,
			"description" => $request->description,
			"url" => $request->url,
			"operating_system" => $request->operating_system,
			"browser" => $request->browser,
			"selector" => $request->selector,
			"resolution" => $request->resolution,
			"deadline" => $request->deadline,
			"order_number" => $order_number,
			"ai_id" => $ai_id
		]);

		// Check if the bug comes with a screenshot (or multiple) and if so, store it/them
		$screenshots = $request->screenshots;
		if($screenshots != NULL) {
			foreach($screenshots as $screenshot) {
				$screenshot = (object) $screenshot;
				$screenshotService->store($bug, $screenshot);
			}
		}

		// Check if the bug comes with a attachment (or multiple) and if so, store it/them
		$attachments = $request->attachments;
		if($attachments != NULL) {
			foreach($attachments as $attachment) {
				$attachment = (object) $attachment;
				$attachmentService->store($bug, $attachment);
			}
		}
		
		// Store the respective role
		Auth::user()->bugs()->attach($bug->id, ['role_id' => 1]);

		return new BugResource($bug);
	}

	/**
	 * @OA\Get(
	 *	path="/statuses/{status_id}/bugs/{bug_id}",
	 *	tags={"Bug"},
	 *	summary="Show one bug.",
	 *	operationId="showBug",
	 *	security={ {"sanctum": {} }},
	 *
	 * 	@OA\Parameter(
	 *		name="status_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
	 *		)
	 *	),
	 * 
	 *	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachments",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-comments",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-bug-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachment-base64",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Bug"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 * )
	 **/
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Bug  $bug
	 * @return \Illuminate\Http\Response
	 */
	public function show(Status $status, Bug $bug)
	{
		// Check if the user is authorized to view the bug
		$this->authorize('view', [Bug::class, $status->project]);

		return new BugResource($bug);
	}

	/**
	 * @OA\Put(
	 *	path="/statuses/{status_id}/bugs/{bug_id}",
	 *	tags={"Bug"},
	 *	summary="Update a bug.",
	 *	operationId="updateBug",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="status_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="_method",
	 *		required=true,
	 *		in="query",
	 *		@OA\Schema(
	 *			type="string",
	 *			default="PUT"
	 *		)
	 *	),
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="user_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *  			@OA\Property(
	 *                  property="project_id",
	 * 					type="string",
	 *  				maxLength=255,
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug description",
	 *                  property="description",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug url",
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="status_id",
	 *                  type="string",
	 *              ),
	 * 	 	  		@OA\Property(
	 *                  property="order_number",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *  			@OA\Property(
	 *                  property="priority_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *  			@OA\Property(
	 *                  property="operating_system",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="browser",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="selector",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="resolution",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="deadline",
	 *                  type="string",
	 * 					format="date-time",
	 *              ),
	 *              required={"user_id","project_id","designation","url","status_id","priority_id",}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Bug"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\BugRequest  $request
	 * @param  \App\Models\Bug  $bug
	 * @return \Illuminate\Http\Response
	 */
	public function update(BugRequest $request, Status $status, Bug $bug)
	{
		// Check if the user is authorized to update the bug
		$this->authorize('update', [Bug::class, $status->project]);

		// Check if the order of the bugs has to be synchronized
		if($request->order_number != $bug->getOriginal('order_number') || $request->status_id != $bug->getOriginal('status_id') ) {
			$this->synchronizeBugOrder($request, $bug);
		}

		// Update the bug
		$bug->update([
			"project_id" => $status->project_id,
			"status_id" => $request->status_id,
			"priority_id" => $request->priority_id,
			"designation" => $request->designation,
			"description" => $request->description,
			"url" => $request->url,
			"operating_system" => $request->operating_system,
			"browser" => $request->browser,
			"selector" => $request->selector,
			"resolution" => $request->resolution,
			"deadline" => $request->deadline,
			"order_number" => $request->order_number,
			"ai_id" => $request->ai_id
		]);

		return new BugResource($bug);
	}

	/**
	 * @OA\Delete(
	 *	path="/statuses/{status_id}/bugs/{bug_id}",
	 *	tags={"Bug"},
	 *	summary="Delete a bug.",
	 *	operationId="deleteBug",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="status_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=204,
	 *		description="Success",
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 * )
	 **/
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Bug  $bug
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Status $status, Bug $bug, ScreenshotService $screenshotService, CommentService $commentService, AttachmentService $attachmentService)
	{
		// Check if the user is authorized to delete the bug
		$this->authorize('delete', [Bug::class, $status->project]);

		$val = $bug->delete();

		// Delete the respective screenshots
		foreach($bug->screenshots as $screenshot) {
			$screenshotService->delete($screenshot);
		}

		// Delete the respective comments
		foreach($bug->comments as $comment) {
			$commentService->delete($comment);
		}

		// Delete the respective attachments
		foreach($bug->attachments as $attachment) {
			$attachmentService->delete($attachment);
		}

		return response($val, 204);
	}

	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/assign-user",
	 *	tags={"Bug"},
	 *	summary="Assign user to bug.",
	 *	operationId="assignUser",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The id of the user",
	 *                  property="user_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *              required={"user_id"}
	 *          )
	 *      )
	 *  ),
	 *	@OA\Response(
	 *		response=204,
	 *		description="Success",
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 * )
	 **/
	/**
	 * Assign a user to the bug
	 *
	 * @param  \App\Models\Bug  $bug
	 * @return \Illuminate\Http\Response
	 */
	public function assignUser(Request $request, Bug $bug)
	{ 
		// Check if the user is authorized to assign a user to the bug
		$this->authorize('assignUser', $bug);

		$targetUser = User::find($request->user_id);
		$targetUser->bugs()->attach($bug->id, ['role_id' => 4]);

		return response()->json("", 204);
	}

	// Synchronize the order numbers of all the bugs, that are affected by the updated bug
	private function synchronizeBugOrder($request, $bug)
	{
		// Check if the bug also changed it's status
		if($request->status_id != $bug->getOriginal('status_id')) {
			$originalStatus = Status::find($bug->getOriginal('status_id'));
			$originalStatusBugs = $originalStatus->bugs->where('order_number', '>', $bug->getOriginal('order_number'));

			// Descrease all the order numbers that war greater than the original bug order number
			foreach($originalStatusBugs as $originalStatusBug) {
				$originalStatusBug->update([
					"order_number" => $originalStatusBug->order_number - 1
				]);
			}

			$newStatus = Status::find($request->status_id);
			$newStatusBugs = $newStatus->bugs->where('order_number', '>=', $request->order_number);
		} else {
			$newStatus = Status::find($request->status_id);
			$newStatusBugs = $newStatus->bugs->whereBetween('order_number', [$request->order_number, $bug->getOriginal('order_number')]);
		}

		// Increase all the order numbers that are greater than the original bug order number
		foreach($newStatusBugs as $newStatusBug) {
			$newStatusBug->update([
				"order_number" => $newStatusBug->order_number + 1
			]);
		}
	}
}
