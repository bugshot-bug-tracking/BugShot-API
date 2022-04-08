<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\MarkerResource;

// Services
use App\Services\MarkerService;

// Models
use App\Models\Screenshot;
use App\Models\Marker;

// Requests
use App\Http\Requests\MarkerStoreRequest;
use App\Http\Requests\MarkerUpdateRequest;

class MarkerController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/screenshots/{screenshot_id}/markers",
	 *	tags={"Marker"},
	 *	summary="All markers of the screenshot.",
	 *	operationId="allMarkers",
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
	 *		name="screenshot_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Screenshot/properties/id"
	 *		)
	 *	),
	 * 
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Marker")
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
    public function index(Screenshot $screenshot)
    {
		// Check if the user is authorized to list the markers of the screenshot
		$this->authorize('viewAny', [Marker::class, $screenshot->bug->project]);
  
		return MarkerResource::collection($screenshot->markers);
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  MarkerStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/screenshots/{screenshot_id}/markers",
	 *	tags={"Marker"},
	 *	summary="Store one marker.",
	 *	operationId="storeMarker",
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
	 *		name="screenshot_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Screenshot/properties/id"
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
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="position_y",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="web_position_x",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="web_position_y",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="target_x",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="target_y",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="target_height",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="target_width",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="scroll_x",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="scroll_y",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="screenshot_height",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="screenshot_width",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 * 	   			@OA\Property(
	 *                  property="target_full_selector",
	 *                  type="string"
	 *              ),
     * 	   			@OA\Property(
	 *                  property="target_short_selector",
	 *                  type="string"
	 *              ),
     * 	   			@OA\Property(
	 *                  property="target_html",
	 *                  type="string"
	 *              ),
	 *              required={}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Marker"
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
    public function store(MarkerStoreRequest $request, Screenshot $screenshot, MarkerService $markerService)
    {
		// Check if the user is authorized to create the marker
		$this->authorize('create', [Screenshot::class, $screenshot->bug->project]);

		// Check if the the request already contains a UUID for the marker
		$id = $this->setId($request);

		$marker = $markerService->store($screenshot, $request, $id);

		return new MarkerResource($marker);
    }

    /**
     * Display the specified resource.
     *
     * @param  Marker  $marker
     * @return Response
     */
	/**
	 * @OA\Get(
	 *	path="/screenshots/{screenshot_id}/markers/{marker_id}",
	 *	tags={"Marker"},
	 *	summary="Show one marker.",
	 *	operationId="showMarker",
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
	 *		name="screenshot_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Screenshot/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="marker_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Marker/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Marker"
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
    public function show(Screenshot $screenshot, Marker $marker)
    {
		// Check if the user is authorized to view the marker
		$this->authorize('view', [Marker::class, $screenshot->bug->project]);

		return new MarkerResource($marker);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  MarkerUpdateRequest  $request
     * @param  Marker  $marker
     * @return Response
     */
	/**
	 * @OA\Put(
	 *	path="/screenshots/{screenshot_id}/markers/{marker_id}",
	 *	tags={"Marker"},
	 *	summary="Update a marker.",
	 *	operationId="updateMarker",
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
	 *		name="screenshot_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Screenshot/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="marker_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Marker/properties/id"
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
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="position_y",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="web_position_x",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="web_position_y",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="target_x",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="target_y",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="target_height",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="target_width",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="scroll_x",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="scroll_y",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="screenshot_height",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 *  			@OA\Property(
	 *                  property="screenshot_width",
	 *                  type="number",
	 *                  format="float",
	 *              ),
	 * 	   			@OA\Property(
	 *                  property="target_full_selector",
	 *                  type="string"
	 *              ),
     * 	   			@OA\Property(
	 *                  property="target_short_selector",
	 *                  type="string"
	 *              ),
     * 	   			@OA\Property(
	 *                  property="target_html",
	 *                  type="string"
	 *              ),
	 *              required={}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Marker"
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
    public function update(MarkerUpdateRequest $request, Screenshot $screenshot, Marker $marker)
    {
		// Check if the user is authorized to update the marker
		$this->authorize('update', [$marker, $screenshot->bug->project]);

		// Update the marker
		$marker->update($request->all());

		return new MarkerResource($marker);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Marker  $marker
     * @return Response
     */
	/**
	 * @OA\Delete(
	 *	path="/screenshots/{screenshot_id}/markers/{marker_id}",
	 *	tags={"Marker"},
	 *	summary="Delete a marker.",
	 *	operationId="deleteMarker",
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
	 *		name="screenshot_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Screenshot/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="marker_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Marker/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Marker"
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
    public function destroy(Screenshot $screenshot, Marker $marker, MarkerService $markerService)
    {
		// Check if the user is authorized to delete the marker
		$this->authorize('update', [$marker, $screenshot->bug->project]);

		$val = $markerService->delete($marker);

		return response($val, 204);
    }
}
