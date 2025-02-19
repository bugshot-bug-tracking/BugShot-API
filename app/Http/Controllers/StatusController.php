<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...

use App\Events\StatusCreated;
use App\Events\StatusDeleted;
use App\Events\StatusUpdated;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\BugResource;
use App\Http\Resources\StatusResource;

// Models
use App\Models\Status;
use App\Models\Project;

// Requests
use App\Http\Requests\StatusStoreRequest;
use App\Http\Requests\StatusUpdateRequest;

/**
 * @OA\Tag(
 *     name="Status",
 * )
 */
class StatusController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/statuses",
	 *	tags={"Status"},
	 *	summary="All statuses.",
	 *	operationId="allStatuses",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-bugs",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-assigned",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-deadline",
	 *		required=false,
	 *		in="header",
	 *      example=">|1693393188"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-creator-id",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-priority",
	 *		required=false,
	 *		in="header",
	 *      example="Minor"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-markers",
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
	 *			@OA\Items(ref="#/components/schemas/Status")
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
	public function index(Request $request, Project $project)
	{
		// Check if the user is authorized to list the statuses of the project
		$this->authorize('viewAny', [Status::class, $project]);

		$timestamp = $request->timestamp;
		$statuses = $project->statuses->when($timestamp, function ($query, $timestamp) {
			return $query->where("statuses.updated_at", ">", date("Y-m-d H:i:s", $timestamp));
		});

		return StatusResource::collection($statuses);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/interface/statuses",
	 *	tags={"Interface"},
	 *	summary="All statuses.",
	 *	operationId="allStatusesViaApiKey",
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="api-token",
	 *		required=true,
	 *		in="header",
	 * 		example="d1359f79-ce2d-45b1-8fd8-9566c606aa6c"
	 *	),
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 *
	 * 	@OA\Parameter(
	 *		name="include-bugs",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-assigned",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-markers",
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
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Status")
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
	public function indexViaApiKey(Request $request)
	{
		return StatusResource::collection($request->get('project')->statuses);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  StatusStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/projects/{project_id}/statuses",
	 *	tags={"Status"},
	 *	summary="Store one status.",
	 *	operationId="storeStatus",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The message",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              required={"designation"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Status"
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
	public function store(StatusStoreRequest $request, Project $project)
	{
		// Check if the user is authorized to create the status
		$this->authorize('create', [Status::class, $project]);

		// Check if the the request already contains a UUID for the status
		$id = $this->setId($request);

		// Get order_number for last status and move in front of the status
		$order_number = $project->statuses->count() - 1;

		// Store the new status in the database
		$status = $project->statuses()->create([
			"id" => $id,
			"designation" => $request->designation,
			"order_number" => $order_number
		]);

		broadcast(new StatusCreated($status))->toOthers();

		return new StatusResource($status);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Status  $status
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/statuses/{status_id}",
	 *	tags={"Status"},
	 *	summary="Show one status.",
	 *	operationId="showStatus",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="status_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-bugs",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-assigned",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-markers",
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
	 *			ref="#/components/schemas/Status"
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
	public function show(Project $project, Status $status)
	{
		// Check if the user is authorized to view the status
		$this->authorize('view', [Status::class, $project]);

		return new StatusResource($status);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Status  $status
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/interface/statuses/{status_id}",
	 *	tags={"Interface"},
	 *	summary="Show one status.",
	 *	operationId="showStatusViaApiKey",
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="api-token",
	 *		required=true,
	 *		in="header",
	 * 		example="d1359f79-ce2d-45b1-8fd8-9566c606aa6c"
	 *	),
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 *
	 *	@OA\Parameter(
	 *		name="status_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-bugs",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-assigned",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-markers",
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
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Status"
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
	public function showViaApiKey(Request $request, Status $status)
	{
		//Check if user has access to bug
		$tempProject = $request->get('project');
		if ($status->project_id == $tempProject->id) {
			return new StatusResource($status);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  StatusUpdateRequest  $request
	 * @param  Status  $status
	 * @return Response
	 */
	/**
	 * @OA\Patch(
	 *	path="/projects/{project_id}/statuses/{status_id}",
	 *	tags={"Status"},
	 *	summary="Update a status.",
	 *	operationId="updateStatus",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="status_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
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
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The message",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 * 	 	  		@OA\Property(
	 *                  property="order_number",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *              required={"designation", "order_number"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Status"
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
	public function update(StatusUpdateRequest $request, Project $project, Status $status)
	{
		// Check if the user is authorized to update the status
		$this->authorize('update', [Status::class, $project]);

		if($status->permanent === NULL)
		{
			// Check if the order of the status has to be synchronized
			$order_number =  $request->order_number;
			if ($request->order_number != $status->getOriginal('order_number')) {

				//Prevent higher order numbers
				$order_number = $request->order_number > $project->statuses->count() ? $project->statuses->count() - 2 : $request->order_number;

				$this->synchronizeStatusOrder($order_number, $status, $project);
			}

			// Update the status
			$status->update([
				"designation" => isset($request->designation) ? $request->designation : $status->designation,
				"order_number" => isset($request->order_number) ? $order_number : $status->order_number
			]);
		}
		else{
			// Update the status
			$status->update([
				"designation" => isset($request->designation) ? $request->designation : $status->designation,
			]);
		}

		broadcast(new StatusUpdated($status))->toOthers();

		return new StatusResource($status);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Status  $status
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/projects/{project_id}/statuses/{status_id}",
	 *	tags={"Status"},
	 *	summary="Delete a status.",
	 *	operationId="deleteStatus",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="move",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="status_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
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
	public function destroy(Request $request, Project $project, Status $status)
	{
		// Check if the user is authorized to delete the status
		$this->authorize('delete', [Status::class, $project]);

		// Move the bugs into a new status
		if ($request->header('move') != NULL) {
			$this->moveBugsIntoNewStatus($status->bugs, $request->header('move'));
		}

		//synchronize the statuses -> order // SET ORIGINAL ORDER WHEN RESTORING IS POSSIBLE AND SYCHRONIZE
		$this->synchronizeStatusDeletedOrder($status, $project);
		$val = $status->delete();

		broadcast(new StatusDeleted($status))->toOthers();

		return response($val, 204);
	}

	// Synchronize the order numbers of all the statuses, that are affected by the updated status
	//	Might need rebuild -> update, sort by order_number, from 0 to count -> reset order_numbers
	private function synchronizeStatusOrder($newOrderNumber, $status, $project)
	{
		$originalOrderNumber = $status->getOriginal('order_number');

		// Check wether the original or new order_number is bigger because ->whereBetween only works when the first array parameter is smaller than the second
		if ($originalOrderNumber < $newOrderNumber) {
			$statuses = $project->statuses->whereBetween('order_number', [$originalOrderNumber, $newOrderNumber]);
		} else {
			$statuses = $project->statuses->whereBetween('order_number', [$newOrderNumber, $originalOrderNumber]);
		}

		// Increase all the order numbers that are greater than the original status order number
		foreach ($statuses as $statusItem) {
			if ($statusItem->permanent == 'done' || $statusItem->id == $status->id) {
				continue;
			}
			$statusItem->update([
				"order_number" => $originalOrderNumber < $newOrderNumber ? $statusItem->order_number - 1 : $statusItem->order_number + 1
			]);
		}
	}

	private function synchronizeStatusDeletedOrder($status, $project)
	{
		$statuses = $project->statuses->whereBetween('order_number', [$status->order_number, $project->statuses->last()->order_number]);
		foreach ($statuses as $statusItem){
			if ($statusItem->permanent == 'done' || $statusItem->id == $status->id) {
				continue;
			}
			$statusItem->update([
				"order_number" => $statusItem->order_number - 1
			]);
		}
	}

	// Synchronize the order numbers of all the bugs within a status that is to be deleted
	private function moveBugsIntoNewStatus($bugs, $status_id)
	{
		$status = Status::find($status_id);
		$orderNumber = $status->bugs->max('order_number') + 1;

		foreach ($bugs as $bug) {
			$bug->update([
				'status_id' => $status->id,
				'order_number' => $orderNumber++
			]);

			if($status->permanent == 'done') {
				$bug->update([
					"done_at" => now()
				]);
			}
		}

	}
}
