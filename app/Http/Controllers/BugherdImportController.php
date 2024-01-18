<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\BugherdListRequest;
use App\Http\Requests\BugherdImportRequest;
use App\Jobs\ImportBugherdProject;
use App\Traits\Bugherd;

/**
 * @OA\Tag(
 *     name="BugherdImport",
 * )
 */
class BugherdImportController extends Controller
{
	use Bugherd;

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/import/bugherd/list-projects",
	 *	tags={"BugherdImport"},
	 *	summary="List all Bugherd projects.",
	 *	operationId="allBugherdProjects",
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
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The bugherd api token",
	 *                  property="bugherd_api_token",
	 *                  type="string",
	 *              ),
	 *              required={"bugherd_api_token"}
	 *          )
	 *      )
	 *  ),
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
	public function getProjects(BugherdListRequest $request)
	{
		$apiToken = $request->bugherd_api_token;
		$response = Bugherd::sendBugherdRequest($apiToken, 'projects.json');

		return response()->json(
			$response->json()
		);
	}

	public function importProject(BugherdImportRequest $request)
	{
		$apiToken = $request->bugherd_api_token;

		foreach($request->ids as $projectId)
		{
			// Queue the job
			ImportBugherdProject::dispatch($apiToken, $projectId);
		}
	}

	public function getBugs(Project $project)
	{

	}

	public function importBug(Project $project)
	{

	}
}
