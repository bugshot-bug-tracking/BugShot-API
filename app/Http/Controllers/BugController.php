<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

// Resources
use App\Http\Resources\BugResource;
use App\Http\Resources\ArchivedBugResource;
use App\Http\Resources\BugUserRoleResource;

// Services
use App\Services\ScreenshotService;
use App\Services\AttachmentService;
use App\Services\CommentService;
use App\Services\BugService;
use App\Services\ApiCallService;

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
use App\Events\BugMembersUpdated;

/**
 * @OA\Tag(
 *     name="Bug",
 * )
 */
class BugController extends Controller
{


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/statuses/{status_id}/bugs",
	 *	tags={"Bug"},
	 *	summary="All bugs.",
	 *	operationId="allBugs",
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
	public function index(Request $request, Status $status)
	{
		// Check if the user is authorized to list the bugs of the project
		$this->authorize('viewAny', [Bug::class, $status->project]);

		// Get timestamp
		$timestamp = $request->header('timestamp');

		// Check if the request includes a timestamp and query the bugs accordingly
		if ($timestamp == NULL) {
			$bugs = $status->bugs()->where("bugs.archived_at", NULL)->get();
		} else {
			$bugs = $status->bugs()->where("bugs.updated_at", ">", date("Y-m-d H:i:s", $timestamp))->where("bugs.archived_at", NULL)->get();
		}

		return BugResource::collection($bugs);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  BugStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/statuses/{status_id}/bugs",
	 *	tags={"Bug"},
	 *	summary="Store one bug.",
	 *	operationId="storeBug",
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
	 *              		),
	 *  					@OA\Property(
	 *              		    property="device_pixel_ratio",
	 *              		    type="integer",
	 *              		    format="int32",
	 *              		),
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
	public function store(BugStoreRequest $request, Status $status, ScreenshotService $screenshotService, AttachmentService $attachmentService, BugService $bugService, ApiCallService $apiCallService)
	{
		// Check if the user is authorized to create the bug
		$this->authorize('create', [Bug::class, $status->project]);

		// Check if the the request already contains a UUID for the bug
		$id = $this->setId($request);

		return $bugService->store($request, $status, $id, $screenshotService, $attachmentService, $apiCallService);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  BugStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/interface/bugs",
	 *	tags={"Interface"},
	 *	summary="Store one bug.",
	 *	operationId="storeBugViaApiKey",
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
	 *              		),
	 *  					@OA\Property(
	 *              		    property="device_pixel_ratio",
	 *              		    type="integer",
	 *              		    format="int32",
	 *              		),
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
	 *              required={"designation","url","priority_id",}
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
	public function storeViaApiKey(BugStoreRequest $request, ScreenshotService $screenshotService, AttachmentService $attachmentService, BugService $bugService, ApiCallService $apiCallService)
	{
		//get backlog of sent project (api key)
		$tempProject = $request->get('project');
		$statuses = $tempProject->statuses;
		$returnStatus = $statuses[0];

		// Check if the the request already contains a UUID for the bug
		$id = $this->setId($request);

		return $bugService->store($request, $returnStatus, $id, $screenshotService, $attachmentService, $apiCallService);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Bug  $bug
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/statuses/{status_id}/bugs/{bug_id}",
	 *	tags={"Bug"},
	 *	summary="Show one bug.",
	 *	operationId="showBug",
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
	public function show(Status $status, Bug $bug)
	{
		// Check if the user is authorized to view the bug
		$this->authorize('view', [Bug::class, $status->project]);

		return new BugResource($bug);
	}

		/**
	 * Display the specified resource.
	 *
	 * @param  Bug  $bug
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/statuses/{status_id}/archived-bugs/{bug_id}",
	 *	tags={"Bug"},
	 *	summary="Show one archived bug.",
	 *	operationId="showArchivedBug",
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
	public function showArchivedBug($status_id, $bug_id)
	{
		$status = Status::where("id", $status_id)
			->withTrashed()
			->first();

		// Check if the user is authorized to view the bug
		$this->authorize('view', [Bug::class, $status->project]);

		$bug = Bug::where("id", $bug_id)
			->withTrashed()
			->first();

		return new ArchivedBugResource($bug);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Bug  $bug
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/interface/bugs/{bug}",
	 *	tags={"Interface"},
	 *	summary="Show one bug.",
	 *	operationId="showBugViaApiKey",
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
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
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
	public function showViaApiKey(Request $request, Bug $bug)
	{
		//Check if user has access to bug
		$tempProject = $request->get('project');
		if ($bug->project_id == $tempProject->id) {
			return new BugResource($bug);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  BugUpdateRequest  $request
	 * @param  Bug  $bug
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/statuses/{status_id}/bugs/{bug_id}",
	 *	tags={"Bug"},
	 *	summary="Update a bug.",
	 *	operationId="updateBug",
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
	 *                  property="ai_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *  			@OA\Property(
	 *                  property="deadline",
	 *                  type="string",
	 * 					format="date-time",
	 *              ),
	 *              required={"designation","status_id","priority_id",}
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

	public function update(BugUpdateRequest $request, Status $status, Bug $bug, BugService $bugService, ApiCallService $apiCallService)
	{
		// Check if the user is authorized to update the bug
		$this->authorize('update', [Bug::class, $status->project]);

		return $bugService->update($request, $status, $bug, $apiCallService);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  BugUpdateRequest  $request
	 * @param  Bug  $bug
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/interface/bugs/{bug_id}",
	 *	tags={"Interface"},
	 *	summary="Update a bug.",
	 *	operationId="updateBugViaApiKey",
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
	 *                  property="ai_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *  			@OA\Property(
	 *                  property="deadline",
	 *                  type="string",
	 * 					format="date-time",
	 *              ),
	 *              required={"designation","priority_id",}
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
	public function updateViaApiKey(BugUpdateRequest $request, Bug $bug, BugService $bugService, ApiCallService $apiCallService)
	{
		//Find  bug in project and get status
		$tempProject = $request->get('project');
		if ($bug->project_id == $tempProject->id) {
			$tempstatus = Status::find($bug->status_id);
			return $bugService->update($request, $tempstatus, $bug, $apiCallService);
		}

		$response = [
			'success' => false,
			'message' => 'The bug was not found or is not available to the user!',
		];

		return response()->json($response, 404);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Bug  $bug
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/statuses/{status_id}/bugs/{bug_id}",
	 *	tags={"Bug"},
	 *	summary="Delete a bug.",
	 *	operationId="deleteBug",
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

	public function destroy(Status $status, Bug $bug, ScreenshotService $screenshotService, CommentService $commentService, AttachmentService $attachmentService, BugService $bugService)
	{
		// Check if the user is authorized to delete the bug
		$this->authorize('delete', [Bug::class, $status->project]);

		return $bugService->destroy($status, $bug, $screenshotService, $commentService, $attachmentService);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Bug  $bug
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/interface/bugs/{bug_id}",
	 *	tags={"Interface"},
	 *	summary="Delete a bug.",
	 *	operationId="deleteBugViaApiKey",
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
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
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
	public function destroyViaApiKey(Request $request, Bug $bug, ScreenshotService $screenshotService, CommentService $commentService, AttachmentService $attachmentService, BugService $bugService)
	{
		//Find bug in project
		$tempProject = $request->get('project');
		foreach ($tempProject->statuses as $status) {
			foreach ($status->bugs as $searchbug) {
				if ($bug->id == $searchbug->id) {
					return $bugService->destroy($status, $searchbug, $screenshotService, $commentService, $attachmentService);
				}
			}
		}
		$response = [
			'success' => false,
			'message' => 'The bug was not found or is not available to the user!',
		];

		return response()->json($response, 404);
	}

	/**
	 * Assign a user to the bug
	 *
	 * @param  Bug  $bug
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/assign-user",
	 *	tags={"Bug"},
	 *	summary="Assign user to bug.",
	 *	operationId="assignUser",
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

	public function assignUser(Request $request, Bug $bug)
	{
		// Check if the user is authorized to assign a user to the bug
		$this->authorize('assignUser', [Bug::class, $bug->project]);

		$targetUser = User::find($request->user_id);
		$targetUser->bugs()->attach($bug->id, ['role_id' => 2]);

		//AssignedToBug::dispatch($targetUser, $bug);
		broadcast(new BugMembersUpdated($bug))->toOthers();

		return response()->json("", 204);
	}

	/**
	 * Display a list of users that belongs to the bug.
	 *
	 * @param  Bug  $bug
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/users",
	 *	tags={"Bug"},
	 *	summary="All bug users.",
	 *	operationId="allBugUsers",
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
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/BugUserRole")
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

	public function users(Bug $bug)
	{
		// Check if the user is authorized to view the users of the bug
		$this->authorize('view', [Bug::class, $bug->project]);

		return BugUserRoleResource::collection(
			BugUserRole::where("bug_id", $bug->id)
				->with('bug')
				->with('user')
				->with("role")
				->get()
		);
	}

	/**
	 * Remove a user from the bug
	 *
	 * @param  Bug  $bug
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/bugs/{bug_id}/users/{user_id}",
	 *	tags={"Bug"},
	 *	summary="Remove user from the bug.",
	 *	operationId="removeBugUser",
	 *	security={ {"sanctum": {} }},
	 *	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="user_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
	 *		)
	 *	),
	 *
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
	 *)
	 *
	 **/

	public function removeUser(Bug $bug, User $user)
	{
		// Check if the user is authorized to view the users of the bug
		$this->authorize('removeUser', [Bug::class, $bug->project]);

		$val = $bug->users()->detach($user);
		broadcast(new BugMembersUpdated($bug))->toOthers();

		return response($val, 204);
	}
}
