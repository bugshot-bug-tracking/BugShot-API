<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScreenshotRequest;
use App\Http\Resources\ScreenshotResource;
use App\Services\ScreenshotService;
use App\Models\Bug;
use App\Models\Screenshot;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Screenshot",
 * )
 */
class ScreenshotController extends Controller
{
	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/screenshots",
	 *	tags={"Screenshot"},
	 *	summary="All screenshots of the bug.",
	 *	operationId="allScreenshots",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Bug $bug)
	{
		return ScreenshotResource::collection($bug->screenshots);
	}

	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/screenshots",
	 *	tags={"Screenshot"},
	 *	summary="Store one screenshots.",
	 *	operationId="storeScreenshot",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\ScreenshotRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(ScreenshotRequest $request, Bug $bug, ScreenshotService $screenshotService)
	{
		$screenshot = $screenshotService->store($bug, $request);

		return new ScreenshotResource($screenshot);
	}

	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/screenshots/{screenshot_id}",
	 *	tags={"Screenshot"},
	 *	summary="Show one screenshots.",
	 *	operationId="showScreenshot",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Screenshot  $screenshot
	 * @return \Illuminate\Http\Response
	 */
	public function show(Bug $bug, Screenshot $screenshot)
	{
		return new ScreenshotResource($screenshot);
	}

	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/screenshots/{screenshot_id}",
	 *	tags={"Screenshot"},
	 *	summary="Update one screenshots.",
	 *	operationId="updateScreenshot",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\ScreenshotRequest  $request
	 * @param  \App\Models\Screenshot  $screenshot
	 * @return \Illuminate\Http\Response
	 */
	public function update(ScreenshotRequest $request, Bug $bug, Screenshot $screenshot)
	{
		$storagePath = "/uploads/screenshots";

		$bug = Bug::where("id", $screenshot->bug_id)->with("project")->get()->first();
		$project = $bug->project;
		$company = $project->company;

		$filePath = $storagePath . "/$company->id/$project->id/$bug->id";

		$savedPath = $request->file->store($filePath);

		Storage::delete($screenshot->url);

		$screenshot->update([
			"designation" => $request->file->getClientOriginalName(),
			"url" => $savedPath,
			"position_x" => $request->position_x,
			"position_y" => $request->position_y,
			"web_position_x" =>  $request->web_position_x,
			"web_position_y" =>  $request->web_position_y,
		]);

		return new ScreenshotResource($screenshot);
	}

	/**
	 * @OA\Delete(
	 *	path="/bugs/{bug_id}/screenshots/{screenshot_id}",
	 *	tags={"Screenshot"},
	 *	summary="Delete one screenshots.",
	 *	operationId="deleteScreenshot",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Screenshot  $screenshot
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Bug $bug, Screenshot $screenshot)
	{
		$val = $screenshot->delete();
		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/screenshots/{id}/download",
	 *	tags={"Screenshot"},
	 *	summary="Download one screenshots. (Not Working In Swagger.)",
	 *	operationId="downloadScreenshot",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Screenshot/properties/id"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\Schema(
	 * 			type="string",
	 * 			format="binary",
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
	 * Download the specified resource.
	 *
	 * @param  \App\Models\Screenshot  $screenshot
	 * @return \Illuminate\Http\Response
	 */
	public function download(Screenshot $screenshot)
	{
		return Storage::download($screenshot->url, $screenshot->designation);
	}
}
