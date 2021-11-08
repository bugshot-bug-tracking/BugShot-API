<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectInviteRequest;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\BugResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUserRoleResource;
use App\Http\Resources\StatusResource;
use App\Services\ImageService;
use App\Models\Project;
use App\Models\ProjectUserRole;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Project",
 * )
 */
class ProjectController extends Controller
{

	/**
	 * @OA\Get(
	 *	path="/project",
	 *	tags={"Project"},
	 *	summary="All projects.",
	 *	operationId="allProjects",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Project")
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
	 *)
	 *
	 **/
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return ProjectResource::collection(Project::all());
	}

	/**
	 * @OA\Post(
	 *	path="/project",
	 *	tags={"Project"},
	 *	summary="Store one project.",
	 *	operationId="storeProject",
	 *	security={ {"sanctum": {} }},
	 *
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The project name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The project url",
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="company_id",
	 * 					type="string",
	 *  				maxLength=255,
	 *              ),
	 *  			@OA\Property(
	 *                  description="The hexcode of the color (optional)",
	 *                  property="color_hex",
	 * 					type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The base64 string of the image belonging to the project (optional)",
	 *                  property="base64",
	 *                  type="string",
	 *              ),
	 *              required={"designation","url","company_id"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Project"
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
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\ProjectRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(ProjectRequest $request, ImageService $imageService)
	{
		// Check if the project comes with an image (or a color)
		$image_path = NULL;
		if($request->base64 != NULL) {
			$image_path = $imageService->store($request->base64);
		}

		// Check if the the request already contains a UUID for the project
        if($request->id == NULL) {
            $id = (string) Str::uuid();
        } else {
            $id = $request->id;
        }

		// Store the new project in the database
		$project = Project::create([
			"id" => $id,
			"company_id" => $request->company_id,
			"designation" => $request->designation,
			"image_path" => $image_path,
			"color_hex" => $request->color_hex,
			"url" => $request->url
		]);

		// Store the respective role
		$projectUserRole = ProjectUserRole::create([
			"project_id" => $project->id,
			"user_id" => Auth::id(),
			"role_id" => 1 // Owner
		]);

		$defaultStatuses = ['Backlog', 'ToDo', 'Doing', 'Done'];
		foreach ($defaultStatuses as $status) {
			Status::create([
				"id" => (string) Str::uuid(),
				"designation" => $status,
				"project_id" => $project->id
			]);
		}

		return new ProjectResource($project);
	}

	/**
	 * @OA\Get(
	 *	path="/project/{id}",
	 *	tags={"Project"},
	 *	summary="Show one project.",
	 *	operationId="showProject",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Project"
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
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function show(Project $project)
	{
		return new ProjectResource($project);
	}

	/**
	 * @OA\Put(
	 *	path="/project/{id}",
	 *	tags={"Project"},
	 *	summary="Update a project.",
	 *	operationId="updateProject",
	 *	security={ {"sanctum": {} }},

	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="_method",
	 *		required=true,
	 *		in="query",
	 *		@OA\Schema(
	 *			type="string",
	 *			default="PUT"
	 *		)
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The project name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The project url",
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="company_id",
	 * 					type="string",
	 *  				maxLength=255,
	 *              ),
	 *  			@OA\Property(
	 *                  description="The hexcode of the color (optional)",
	 *                  property="color_hex",
	 * 					type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The base64 string of the image belonging to the company (optional)",
	 *                  property="base64",
	 *                  type="string",
	 *              ),
	 *              required={"designation","url","company_id"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Project"
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
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\ProjectRequest  $request
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function update(ProjectRequest $request, Project $project, ImageService $imageService)
	{
		// Check if the project comes with an image (or a color)
		$image_path = NULL;
		if($request->base64 != NULL) {
			$image_path = $imageService->store($request->base64);
			$color_hex = NULL;
		} else {
			$color_hex = $request->color_hex;
			$image_path = NULL;
		}

		// Store the new project in the database
		$project->update([
			"company_id" => $request->company_id,
			"designation" => $request->designation,
			"image_path" => $image_path,
			"color_hex" => $request->color_hex,
			"url" => $request->url
		]);

		return new ProjectResource($project);
	}

	/**
	 * @OA\Delete(
	 *	path="/project/{id}",
	 *	tags={"Project"},
	 *	summary="Delete a project.",
	 *	operationId="deleteProject",
	 *	security={ {"sanctum": {} }},

	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=204,
	 *		description="Success",
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
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Project $project)
	{
		$val = $project->delete();
		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/project/{id}/statuses",
	 *	tags={"Project"},
	 *	summary="All project statuses.",
	 *	operationId="allProjectsStatuses",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Status")
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
	 *)
	 *
	 **/
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function statuses(Project $project)
	{
		return StatusResource::collection($project->statuses);
	}

	/**
	 * @OA\Get(
	 *	path="/project/{id}/bugs",
	 *	tags={"Project"},
	 *	summary="All project bugs.",
	 *	operationId="allProjectsBugs",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Bug")
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
	 *)
	 *
	 **/
	/**
	 * Display a list of bugs that belongs to the project.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function bugs(Project $project)
	{
		return BugResource::collection($project->bugs);
	}

	/**
	 * @OA\Get(
	 *	path="/project/{id}/users",
	 *	tags={"Project"},
	 *	summary="All project users.",
	 *	operationId="allProjectsUsers",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
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
	 *)
	 *
	 **/
	/**
	 * Display a list of users that belongs to the project.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function users(Project $project)
	{
		return ProjectUserRoleResource::collection(
			ProjectUserRole::where("project_id", $project->id)
				->with('project')
				->with('user')
				->with("role")
				->get()
		);
	}

	/**
	 * @OA\Post(
	 *	path="/project/{id}/invite",
	 *	tags={"Project"},
	 *	summary="Invite a user to the project and asign it a role",
	 *	operationId="inviteProject",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The invited user id.",
	 *                  property="target_id",
	 *					type="integer",
	 *                  format="int64",
	 *              ),
	 *              @OA\Property(
	 *                  description="The invited user role.",
	 *                  property="role_id",
	 *					type="integer",
	 *                  format="int64",
	 *              ),
	 *              required={"target_id","role_id"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Invitation"
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
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function invite(Project $project, ProjectInviteRequest $request)
	{
		$inputs = $request->all();
		$inputs['sender_id'] = Auth::id();
		$inputs['status_id'] = 1;

		return new InvitationResource($project->invitations()->create($inputs));
	}
}
