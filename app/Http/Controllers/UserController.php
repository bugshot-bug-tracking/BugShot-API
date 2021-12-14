<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\CompanyUserRoleResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\ProjectUserRoleResource;

// Models
use App\Models\Company;
use App\Models\CompanyUserRole;
use App\Models\Invitation;
use App\Models\InvitationStatus;
use App\Models\Project;
use App\Models\ProjectUserRole;

/**
 * @OA\Tag(
 *     name="User",
 * )
 */
class UserController extends Controller
{

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

	/**
	 * @OA\Get(
	 *	path="/user/invitations",
	 *	tags={"User"},
	 *	summary="Show all invitations that the user has received.",
	 *	operationId="showUserInvitations",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Invitation")
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
	public function invitations()
	{
		return InvitationResource::collection(
			Invitation::where([
				["target_email", Auth::id()],
				["status_id", 1]
			])->get()
		);
	}
}
