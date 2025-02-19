<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

// Resources
use App\Http\Resources\ScreenshotResource;

// Services
use App\Services\ScreenshotService;
use App\Services\MarkerService;
use App\Services\ApiCallService;

// Models
use App\Models\Bug;
use App\Models\Screenshot;

// Requests
use App\Http\Requests\ScreenshotStoreRequest;
use App\Http\Requests\ScreenshotUpdateRequest;

/**
 * @OA\Tag(
 *     name="Screenshot",
 * )
 */
class ScreenshotController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/screenshots",
	 *	tags={"Screenshot"},
	 *	summary="All screenshots of the bug.",
	 *	operationId="allScreenshots",
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
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-markers",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Screenshot")
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
	public function index(Bug $bug)
	{
		// Check if the user is authorized to list the screenshots of the bug
		$this->authorize('viewAny', [Screenshot::class, $bug->project]);

		return ScreenshotResource::collection($bug->screenshots);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  ScreenshotStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/screenshots",
	 *	tags={"Screenshot"},
	 *	summary="Store one screenshots.",
	 *	operationId="storeScreenshot",
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
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="position_x",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="position_y",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="web_position_x",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="web_position_y",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="device_pixel_ratio",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 * 	   			@OA\Property(
	 *                  property="base64",
	 *                  type="string"
	 *              ),
	 *   			@OA\Property(
	 *                  property="markers",
	 *                  type="array",
	 * 					@OA\Items(
	 *  					@OA\Property(
	 *              		    property="position_x",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="position_y",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="web_position_x",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="web_position_y",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="target_x",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="target_y",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="target_height",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="target_width",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="scroll_x",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="scroll_y",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="screenshot_height",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="screenshot_width",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="device_pixel_ratio",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 * 	   					@OA\Property(
	 *              		    property="target_full_selector",
	 *              		    type="string"
	 *              		),
     * 	   					@OA\Property(
	 *              		    property="target_short_selector",
	 *              		    type="string"
	 *              		),
     * 	   					@OA\Property(
	 *              		    property="target_html",
	 *              		    type="string"
	 *              		),
	 * 					)
	 *              ),
	 *              required={"base64"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Screenshot"
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
	public function store(ScreenshotStoreRequest $request, Bug $bug, ScreenshotService $screenshotService, MarkerService $markerService, ApiCallService $apiCallService)
	{
		// Check if the user is authorized to create the screenshot
		$this->authorize('create', [Screenshot::class, $bug->project]);

		$client_id = $request->get('client_id');
		$screenshot = $screenshotService->store($request, $bug, $request, $client_id, $apiCallService);

		// Check if the bug comes with a screenshot (or multiple) and if so, store it/them
		$markers = $request->markers;
		if($markers != NULL) {
			foreach($markers as $marker) {
				$marker = (object) $marker;
				$markerService->store($screenshot, $marker);
			}
		}

		return new ScreenshotResource($screenshot);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  ScreenshotStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/interface/bugs/{bug}/screenshots",
	 *	tags={"Interface"},
	 *	summary="Store one screenshots.",
	 *	operationId="storeScreenshotViaApiKey",
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
	 * 	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="position_x",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="position_y",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="web_position_x",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="web_position_y",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 * 	   			@OA\Property(
	 *                  property="base64",
	 *                  type="string"
	 *              ),
	 *   			@OA\Property(
	 *                  property="markers",
	 *                  type="array",
	 * 					@OA\Items(
	 *  					@OA\Property(
	 *              		    property="position_x",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="position_y",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="web_position_x",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="web_position_y",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="target_x",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="target_y",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="target_height",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="target_width",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="scroll_x",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="scroll_y",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="screenshot_height",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 *  					@OA\Property(
	 *              		    property="screenshot_width",
	 *              		    type="number",
	 *              		    format="float",
	 *              		),
	 * 	   					@OA\Property(
	 *              		    property="target_full_selector",
	 *              		    type="string"
	 *              		),
     * 	   					@OA\Property(
	 *              		    property="target_short_selector",
	 *              		    type="string"
	 *              		),
     * 	   					@OA\Property(
	 *              		    property="target_html",
	 *              		    type="string"
	 *              		),
	 * 					)
	 *              ),
	 *              required={"base64"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Screenshot"
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
	public function storeViaApiKey(ScreenshotStoreRequest $request, Bug $bug, ScreenshotService $screenshotService, MarkerService $markerService, ApiCallService $apiCallService)
	{
		$client_id = $request->get('client_id');
		$screenshot = $screenshotService->store($request, $bug, $request, $client_id, $apiCallService);

		// Check if the bug comes with a screenshot (or multiple) and if so, store it/them
		$markers = $request->markers;
		if($markers != NULL) {
			foreach($markers as $marker) {
				$marker = (object) $marker;
				$markerService->store($screenshot, $marker);
			}
		}

		return json_encode($screenshot->withoutRelations());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Screenshot  $screenshot
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/screenshots/{screenshot_id}",
	 *	tags={"Screenshot"},
	 *	summary="Show one screenshots.",
	 *	operationId="showScreenshot",
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
 	 * 	@OA\Parameter(
	 *		name="screenshot_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Screenshot/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-markers",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Screenshot"
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
	public function show(Bug $bug, Screenshot $screenshot)
	{
		// Check if the user is authorized to view the screenshot
		$this->authorize('view', [Screenshot::class, $bug->project]);

		return new ScreenshotResource($screenshot);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  ScreenshotUpdateRequest  $request
	 * @param  Screenshot  $screenshot
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/bugs/{bug_id}/screenshots/{screenshot_id}",
	 *	tags={"Screenshot"},
	 *	summary="Update one screenshots.",
	 *	operationId="updateScreenshot",
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
	 *	@OA\Parameter(
	 *		name="screenshot_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Screenshot/properties/id"
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
	 *  			@OA\Property(
	 *                  property="position_x",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="position_y",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="web_position_x",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="web_position_y",
	 *                  type="integer",
	 *                  format="int32",
	 *              ),
	 *  			@OA\Property(
	 *                  property="device_pixel_ratio",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 * 	   			@OA\Property(
	 *                  property="base64",
	 *                  type="string"
	 *              ),
	 *              required={"position_x","position_y","base64"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Screenshot"
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
	 *)
	 *
	 **/
	public function update(ScreenshotUpdateRequest $request, Bug $bug, Screenshot $screenshot)
	{
		// Check if the user is authorized to update the screenshot
		$this->authorize('update', [Screenshot::class, $bug->project]);

		$storagePath = "/uploads/screenshots";

		$bug = Bug::where("id", $screenshot->bug_id)->with("project")->get()->first();
		$project = $bug->project;
		$company = $project->company;

		$filePath = $storagePath . "/$company->id/$project->id/$bug->id";

		$savedPath = $request->file->store($filePath);

		Storage::delete($screenshot->url);

		// Update the screenshot
		$screenshot->update($request->all());
		$screenshot->update([
			"designation" => $request->file->getClientOriginalName(),
			"url" => $savedPath
		]);

		return new ScreenshotResource($screenshot);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Screenshot  $screenshot
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/bugs/{bug_id}/screenshots/{screenshot_id}",
	 *	tags={"Screenshot"},
	 *	summary="Delete one screenshots.",
	 *	operationId="deleteScreenshot",
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
	 *	@OA\Parameter(
	 *		name="screenshot_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Screenshot/properties/id"
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
	 *)
	 *
	 **/
	public function destroy(Bug $bug, Screenshot $screenshot, ScreenshotService $screenshotService, MarkerService $markerService)
	{
		// Check if the user is authorized to delete the screenshot
		$this->authorize('update', [Screenshot::class, $screenshot->bug->project]);

		$val = $screenshotService->delete($screenshot);

		// Delete the respective markers
		foreach($screenshot->markers as $marker) {
			$markerService->delete($marker);
		}

		return response($val, 204);
	}
}
