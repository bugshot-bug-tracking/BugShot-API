<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;

// Models
use App\Models\Company;
use App\Models\Project;
use App\Models\Bug;

// Resources
use App\Http\Resources\BugSearchCollection;
use App\Http\Resources\ProjectSearchCollection;
use App\Http\Resources\CompanySearchCollection;

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
				$searchResults = $this->searchCompanies($searchString);
				break;

			case 'projects':
				$searchResults = $this->searchProjects($searchString);
				break;

			case 'bugs':
				$searchResults = $this->searchBugs($searchString);
				break;

			default:
				$searchResults = array(
					"companies" => $this->searchCompanies($searchString),
					"projects" => $this->searchProjects($searchString),
					"bugs" => $this->searchBugs($searchString),
				);
				break;
		}

		return $searchResults;
	}

    public function searchBugs($searchString) {

		$searchResults = Bug::select('id', 'project_id', 'designation', 'description', 'url')->where(
			function ($query) {
				$query->whereIn('id', Auth::user()->bugs()->select('id'))
				->orWhere('user_id', Auth::id())
				->orWhereHas('project', function ($subQuery) {
					$subQuery->whereHas('users', function ($subSubQuery) {
						$subSubQuery->where('user_id', Auth::id())
							->where('role_id', '<' , 999); // Basically just part of the project
					})
					->orWhere('user_id', Auth::id())
					->orWhereHas('company', function ($subQuery) {
						$subQuery->whereHas('users', function ($subSubQuery) {
							$subSubQuery->where('user_id', Auth::id())
								->where('role_id', '<' , 2);
						})
						->orWhere('user_id', Auth::id())
						->orWhereHas('organization', function ($subQuery) {
							$subQuery->whereHas('users', function ($subSubQuery) {
								$subSubQuery->where('user_id', Auth::id())
									->where('role_id', '<' , 2);
							})
							->orWhere('user_id', Auth::id());
						});
					});
				});
			}
		)
		->where(function($query) use ($searchString) {
			$query->where("bugs.id", "LIKE", "%" . $searchString . "%")
			->orWhere("bugs.designation", "LIKE", "%" . $searchString . "%")
			->orWhere("bugs.description", "LIKE", "%" . $searchString . "%")
			->orWhere("bugs.url", "LIKE", "%" . $searchString . "%");
		})
		->paginate(3);

		return new BugSearchCollection($searchResults);
    }

    public function searchProjects($searchString) {

		$searchResults = Project::select('id', 'designation', 'url', 'company_id')->where(
			function ($query) {
				$query->whereIn('id', Auth::user()->projects()->select('id'))
				->orWhere('user_id', Auth::id())
				->orWhereHas('company', function ($subQuery) {
					$subQuery->whereHas('users', function ($subSubQuery) {
						$subSubQuery->where('user_id', Auth::id())
							->where('role_id', '<' , 2);
					})
					->orWhere('user_id', Auth::id())
					->orWhereHas('organization', function ($subQuery) {
						$subQuery->whereHas('users', function ($subSubQuery) {
							$subSubQuery->where('user_id', Auth::id())
								->where('role_id', '<' , 2);
						})
						->orWhere('user_id', Auth::id());
					});
				});
			}
		)
		->where(function($query) use ($searchString) {
			$query->where("projects.id", "LIKE", "%" . $searchString . "%")
			->orWhere("projects.designation", "LIKE", "%" . $searchString . "%")
			->orWhere("projects.url", "LIKE", "%" . $searchString . "%");
		})
		->paginate(3);

        return new ProjectSearchCollection($searchResults);
    }

    public function searchCompanies($searchString) {

		$searchResults = Company::select('id', 'designation', 'organization_id')->where(
			function ($query) {
				$query->whereIn('id', Auth::user()->companies()->select('id'))
				->orWhere('user_id', Auth::id())
				->orWhereHas('organization', function ($subQuery) {
					$subQuery->whereHas('users', function ($subSubQuery) {
						$subSubQuery->where('user_id', Auth::id())
							->where('role_id', '<' , 2);
					})
					->orWhere('user_id', Auth::id());
				});
			}
		)
		->where(function($query) use ($searchString) {
			$query->where("companies.id", "LIKE", "%" . $searchString . "%")
			->orWhere("companies.designation", "LIKE", "%" . $searchString . "%");
		})
		->paginate(3);

        return new CompanySearchCollection($searchResults);
    }
}
