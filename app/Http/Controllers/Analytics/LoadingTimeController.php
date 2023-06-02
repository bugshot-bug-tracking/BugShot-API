<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoadingTimeStoreRequest;
use App\Models\Client;
use App\Models\LoadingTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * @OA\Tag(
 *     name="LoadingTime",
 * )
 */
class LoadingTimeController extends Controller
{
	/**
	 * Display a list of the resource
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/analytics/loading-times",
	 *	tags={"LoadingTime"},
	 *	summary="List the loading times.",
	 *	operationId="allLoadingTimes",
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
	 *	@OA\Response(
	 *		response=200,
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
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 *)
	 *
	 **/
	public function index()
	{
		$output = new stdClass();
		$output->clients = array();
		$clients = Client::all();
		foreach ($clients as $client) {
			$avg = $client->getAvgLoadingTime();
			if(isset($avg)) {
				$clientOutput = new stdClass();
				$clientOutput->client = $client->designation;
				$clientOutput->avg = $avg;
				$output->clients[] = $clientOutput;
			}
		}

		return response()->json($output, 200);
	}

	/**
	 * Display a list of the resource
	 *
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/analytics/loading-times",
	 *	tags={"LoadingTime"},
	 *	summary="Stores a loading time.",
	 *	operationId="storeLoadingTime",
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
	 *                  description="The loaded url",
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug description",
	 *                  property="loading_duration_raw",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug url",
	 *                  property="loading_duration_fetched",
	 *                  type="string",
	 *              ),
	 *              required={"url","loading_duration_raw","loading_duration_fetched"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
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
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 *)
	 *
	 **/
	public function store(LoadingTimeStoreRequest $request)
	{
		return LoadingTime::create([
			"user_id" => Auth::id(),
			"client_id" => $request->get('client_id'),
			"url" => $request->url,
			"loading_duration_raw" => $request->loading_duration_raw,
			"loading_duration_fetched" => $request->loading_duration_fetched
		]);
	}
}
