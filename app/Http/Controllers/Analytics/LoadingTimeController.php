<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LoadingTime;

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
	public function index() {

		$loadingTimes = array();
		foreach(Client::all() as $client) {
			$loadingTimes[$client->designation] = $client->loadingTimes->avg("load_duration");
		}

		return response()->json([
			"loadingTimes" => $loadingTimes
		], 200);
	}
}
