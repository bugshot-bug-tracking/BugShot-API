<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;

/**
 * @OA\Tag(
 *     name="Analytic",
 * )
 */
class AnalyticController extends Controller
{
	/**
	 * Display an overview of the most important statistics
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/analytics/overview",
	 *	tags={"Analytic"},
	 *	summary="Get an overview of the most important statistics.",
	 *	operationId="getOverview",
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
	public function getOverview() {
		//
	}
}
