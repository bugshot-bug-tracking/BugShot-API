<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;

// Models
use App\Models\Status;

// Requests
use App\Http\Requests\FeedbackStoreRequest;

/**
 * @OA\Tag(
 *     name="Feedback",
 * )
 */
class FeedbackController extends Controller
{
    /**
	 * Create a new feedback
	 *
	 * @param  FeedbackStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/feedbacks",
	 *	tags={"Feedback"},
	 *	summary="Create a new feedback element",
	 *	operationId="createFeedback",
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
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The feedbacks name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The feedbacks description",
	 *                  property="description",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The feedbacks url",
	 *                  property="url",
	 *                  type="string",
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
	 *              required={"designation","url"}
	 *          )
	 *      )
	 *  ),
     * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
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
	public function store(FeedbackStoreRequest $request)
	{  
		// Get the default status for the feedback backlog
		$status = Status::find('Backlog0-0000-0000-0000-000000000000'); // ID of Backlog status

		// Check if the the request already contains a UUID for the bug
		$id = $this->setId($request);
		
		// Get the max order number in this status and increase it by one
		$order_number = $status->bugs->isEmpty() ? 0 : $status->bugs->max('order_number') + 1;

		// Determine the number of bugs in the project to generate the $ai_id
		$allBugsQuery = $status->project->bugs()->withTrashed();
		$numberOfBugs = $allBugsQuery->count();
		$ai_id = $allBugsQuery->get()->isEmpty() ? 0 : $numberOfBugs + 1;
		
		// Store the new bug in the database
		$status->bugs()->create([
			"id" => $id,
			"project_id" => $status->project_id,
			"priority_id" => 2,
			"designation" => $request->designation,
			"description" => $request->description,
			"url" => $request->url,
			"operating_system" => $request->operating_system,
			"browser" => $request->browser,
			"selector" => $request->selector,
			"resolution" => $request->resolution,
			"order_number" => $order_number,
			"ai_id" => $ai_id
		]);

        return response()->json(["message" => __('application.feedback-sent-successfully')], 200);
	}
}
