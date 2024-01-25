<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\BugherdListRequest;
use App\Http\Requests\BugherdImportRequest;
use App\Jobs\ImportBugherdProject;
use App\Traits\Bugherd;
use App\Models\ImportStatus;

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

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/import/bugherd/import-projects",
	 *	tags={"BugherdImport"},
	 *	summary="Import a list of Bugherd projects.",
	 *	operationId="importBugherdProjects",
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
	 *   			@OA\Property(
	 *                  property="projects",
	 *                  type="array",
	 * 					@OA\Items(
	 * 	   					@OA\Property(
	 *              		    property="bugherdProjectId",
	 *              		    type="string"
	 *              		),
	 *  					@OA\Property(
	 *              		    property="bugshotCompanyId",
	 *              		    type="string"
	 *              		)
	 * 					)
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
	public function importProject(BugherdImportRequest $request)
	{
		$apiToken = $request->bugherd_api_token;

		foreach($request->projects as $project)
		{
			// Create the import
			$import = Import::create([
				"status_id" => ImportStatus::PENDING,
				"imported_by" => $this->user,
				"source" => json_encode([
					'Bugherd',
					'project',
					$project->bugherdProjectId
				]),
				"target" => json_encode([
					'group',
					$project->bugshotCompanyId
				])
			]);

			// Queue the job
			ImportBugherdProject::dispatch($import->id, $apiToken, $project);
		}
	}

	public function getBugs(Project $project)
	{

	}

	public function importBug(Project $project)
	{

	}
}
