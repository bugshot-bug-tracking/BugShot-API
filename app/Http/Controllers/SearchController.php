<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Company;
use App\Models\Project;
use App\Models\Bug;

/**
 * @OA\Tag(
 *     name="Search",
 * )
 */
class SearchController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/search",
	 *	tags={"Search"},
	 *	summary="Search by string.",
	 *	operationId="search",
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
	 *		name="search-string",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="resource",
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
	public function search(Request $request)
	{
		$resource = array_key_exists("resource", $request->header()) ? $request->header()["resource"][0] : NULL;
		$searchString = array_key_exists("search-string", $request->header()) ? $request->header()["search-string"][0] : NULL;

		switch ($resource) {
			case 'companies':
				$searchResults = Company::search($searchString)
				->query(function ($query) {
					$query->join('company_user_roles', 'companies.id', 'company_user_roles.company_id')
						->where('company_user_roles.user_id', '=', Auth::id());
				})
				->paginate(3);
				break;

			case 'projects':
				$searchResults = Project::search($searchString)
				->query(function ($query) {
					$query->join('project_user_roles', 'projects.id', 'project_user_roles.project_id')
						->where('project_user_roles.user_id', '=', Auth::id());
				})
				->paginate(3);
				break;

			case 'bugs':
				$projectIds = Auth::user()->projects()->select("id");
				$searchResults = Bug::whereIn("project_id", $projectIds)
					->where(function($query) use ($searchString) {
						$query->where("bugs.id", "LIKE", "%" . $searchString . "%")
						->orWhere("bugs.designation", "LIKE", "%" . $searchString . "%")
						->orWhere("bugs.description", "LIKE", "%" . $searchString . "%")
						->orWhere("bugs.url", "LIKE", "%" . $searchString . "%");
					})
					->paginate(3);
				break;

			default:
				$searchResults = array(
					"companies" => Company::search($searchString)->paginate(3),
					"projects" => Project::search($searchString)->paginate(3),
					"bugs" => Bug::search($searchString)->paginate(3),
				);
				break;
		}

		return response()->json(["data" => $searchResults], 200);
	}
}
