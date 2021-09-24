<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyUserRoleResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUserRoleResource;
use App\Models\Company;
use App\Models\CompanyUserRole;
use App\Models\Project;
use App\Models\ProjectUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\Tag(
 *     name="User",
 * )
 */
class UserController extends Controller
{

	/**
	 * @OA\Get(
	 *	path="/user/companies",
	 *	tags={"User"},
	 *	summary="Show all companies that a use works at.",
	 *	operationId="showUserCompanies",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/CompanyUserRole"
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
	/**
	 * Display all companies whom the user is affiliated with
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function companies()
	{
		return CompanyUserRoleResource::collection(
			CompanyUserRole::where("user_id", Auth::user()->id)
				->with('company')
				->with('user')
				->with("role")
				->get()
		);

		return response()->json("", 200);
	}

	/**
	 * @OA\Get(
	 *	path="/user/company/{id}/projects",
	 *	tags={"User"},
	 *	summary="Show all projects from a company that a use is a part of.",
	 *	operationId="showUserCompanyProjects",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/ProjectUserRole")
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
	/**
	 * Display all projects from a company where the user is a part of
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function companyProjects(Company $company)
	{
		$projects = ProjectUserRoleResource::collection(
			ProjectUserRole::where([
				["user_id", Auth::id()]
			])
				->with(
					'project',
					function ($query) use ($company) {
						$query->where('company_id', $company->id);
					}
				)
				->with('user')
				->with("role")
				->get()
		)->whereNotNull('project');

		return response()->json($projects, 200);
	}

	/**
	 * @OA\Post(
	 *	path="/check-project",
	 *	tags={"User"},
	 *	summary="Return a project with the specified url where the user is a part of",
	 *	operationId="checkProject",
	 *	security={ {"sanctum": {} }},
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *              required={"url"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/ProjectUserRole")
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
	public function checkProject(Request $request)
	{
		$request->validate([
			"url" => ["required", "url"]
		]);

		$projects = Project::where("url", $request->url)->get();

		if ($projects->count() == 0) return response()->json([
			"errors" => [
				"status" => 404,
				"source" => $request->getPathInfo(),
				"detail" => "Project not found."
			]
		], 404);

		$foundProjects = [];
		foreach ($projects as $project) {
			$val = 	ProjectUserRole::where([
				["project_id", $project->id],
				["user_id", Auth::id()],
			])->get();

			if ($val->count() > 0) array_push($foundProjects, $val->first());
		}

		if (count($foundProjects) == 0) return response()->json([
			"errors" => [
				"status" => 404,
				"source" => $request->getPathInfo(),
				"detail" => "Project not found."
			]
		], 404);

		return ProjectUserRoleResource::collection($foundProjects);
	}
}
