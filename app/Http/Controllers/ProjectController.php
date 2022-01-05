<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\BugResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUserRoleResource;
use App\Http\Resources\ImageResource;

// Services
use App\Services\ImageService;
use App\Services\InvitationService;

// Models
use App\Models\User;
use App\Models\Project;
use App\Models\Company;
use App\Models\ProjectUserRole;
use App\Models\Status;

// Requests
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\ProjectRequest;

/**
 * @OA\Tag(
 *     name="Project",
 * )
 */
class ProjectController extends Controller
{

	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}/projects",
	 *	tags={"Project"},
	 *	summary="All projects.",
	 *	operationId="allProjects",
	 *	security={ {"sanctum": {} }},
	 * 
	 * 	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-statuses",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-bugs",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachments",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-comments",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-project-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-project-image",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-bug-users",
	 *		required=false,
	 *		in="header"
	 *	),
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
	public function index(Request $request, Company $company)
	{
		// Check if the user is authorized to list the projects of the company
		$this->authorize('viewAny', [Project::class, $company]);

		// Check if the request includes a timestamp and query the projects accordingly
		if($request->timestamp == NULL) {
            $projects = $company->projects;
        } else {
            $projects = $company->projects->where(
                ["projects.updated_at", ">", date("Y-m-d H:i:s", $request->timestamp)]
			);
        }

		return ProjectResource::collection($projects);
	}

	/**
	 * @OA\Post(
	 *	path="/companies/{company_id}/projects",
	 *	tags={"Project"},
	 *	summary="Store one project.",
	 *	operationId="storeProject",
	 *	security={ {"sanctum": {} }},
	 *
	 *	 @OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
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
	public function store(ProjectRequest $request, Company $company, ImageService $imageService)
	{
		// Check if the user is authorized to create the project
		$this->authorize('create', [Project::class, $company]);

		// Check if the the request already contains a UUID for the project
		$id = $this->setId($request);
		
		// Store the new project in the database
		$project = $company->projects()->create([
			"id" => $id,
			"designation" => $request->designation,
			"color_hex" => $request->color_hex,
			"url" => $request->url
		]);

		// Check if the project comes with an image (or a color)
		$image = NULL;
		if($request->base64 != NULL) {
			$image = $imageService->store($request->base64, $image);
			$project->image()->save($image);
		}

		// Store the respective role
		Auth::user()->projects()->attach($project->id, ['role_id' => 1]);

		$defaultStatuses = ['Backlog', 'ToDo', 'Doing', 'Done'];
		foreach ($defaultStatuses as $key=>$status) {
			Status::create([
				"id" => (string) Str::uuid(),
				"designation" => $status,
				"order_number" => $key++,
				"project_id" => $project->id
			]);
		}

		return new ProjectResource($project);
	}

	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}/projects/{project_id}",
	 *	tags={"Project"},
	 *	summary="Show one project.",
	 *	operationId="showProject",
	 *	security={ {"sanctum": {} }},
	 *
	 * 	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
	 *		)
	 *	),
	 * 
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-statuses",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-bugs",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachments",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-comments",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-project-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-bug-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-project-image",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachment-base64",
	 *		required=false,
	 *		in="header"
	 *	),
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
	public function show(Company $company, Project $project)
	{
		// Check if the user is authorized to view the project
		$this->authorize('view', $project);

		return new ProjectResource($project);
	}

	/**
	 * @OA\Put(
	 *	path="/companies/{company_id}/projects/{project_id}",
	 *	tags={"Project"},
	 *	summary="Update a project.",
	 *	operationId="updateProject",
	 *	security={ {"sanctum": {} }},
	 *
	 * 	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="project_id",
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
	public function update(ProjectRequest $request, Company $company, Project $project, ImageService $imageService)
	{
		// Check if the user is authorized to update the project
		$this->authorize('update', $project);

		// Check if the project comes with an image (or a color)
		$image = $project->image;
		if($request->base64 != NULL && $request->base64 != 'true') {
			$image = $imageService->store($request->base64, $image);
			$image != false ? $project->image()->save($image) : true;
			$color_hex = $company->color_hex;
		} else {
			$imageService->delete($image);
			$color_hex = $request->color_hex;
		}

		// Update the project
		$project->update([
			"company_id" => $company->id,
			"designation" => $request->designation,
			"color_hex" => $color_hex,
			"url" => $request->url
		]);

		return new ProjectResource($project);
	}

	/**
	 * @OA\Delete(
	 *	path="/companies/{company_id}/projects/{project_id}",
	 *	tags={"Project"},
	 *	summary="Delete a project.",
	 *	operationId="deleteProject",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="project_id",
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
	public function destroy(Company $company, Project $project, ImageService $imageService)
	{
		// Check if the user is authorized to delete the project
		$this->authorize('delete', $project);

		// Softdelete the project
		$val = $project->delete();

		// Delete the respective image if present
		$imageService->delete($project->image);

		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/image",
	 *	tags={"Project"},
	 *	summary="Project image.",
	 *	operationId="showProjectImage",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
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
	 *			@OA\Items(ref="#/components/schemas/Image")
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
	 * Display the image that belongs to the project.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function image(Project $project, ImageService $imageService)
	{
		// Check if the user is authorized to view the image of the project
		$this->authorize('viewImage', $project);

		return new ImageResource($project->image);
	}

	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/bugs",
	 *	tags={"Project"},
	 *	summary="All project bugs.",
	 *	operationId="allProjectsBugs",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachments",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-comments",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-project-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-bug-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachment-base64",
	 *		required=false,
	 *		in="header"
	 *	),
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
	 * Display a list of users that belongs to the project.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function bugs(Request $request, Project $project)
	{
		// Check if the user is authorized to list the bugs of the project
		$this->authorize('viewAny', [Bug::class, $project]);

		// Check if the request includes a timestamp and query the bugs accordingly
		if($request->timestamp == NULL) {
            $bugs = $project->bugs;
        } else {
            $bugs = $project->bugs->where([
                ["bugs.updated_at", ">", date("Y-m-d H:i:s", $request->timestamp)]
			]);
        }
		
		return BugResource::collection($bugs);
	}

	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/users",
	 *	tags={"Project"},
	 *	summary="All project users.",
	 *	operationId="allProjectsUsers",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
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
		// Check if the user is authorized to view the users of the project
		$this->authorize('viewUsers', $project);

		return ProjectUserRoleResource::collection(
			ProjectUserRole::where("project_id", $project->id)
				->with('project')
				->with('user')
				->with("role")
				->get()
		);
	}

	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/invitations",
	 *	tags={"Project"},
	 *	summary="All project invitations.",
	 *	operationId="allProjectInvitations",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
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
	 *)
	 *
	 **/
	/**
	 * Display a list of invitations that belongs to the project.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function invitations(Project $project)
	{
		// Check if the user is authorized to view the invitations of the project
		$this->authorize('viewInvitations', $project);
		
		return InvitationResource::collection($project->invitations);
	}

	/**
	 * @OA\Post(
	 *	path="/projects/{project_id}/invite",
	 *	tags={"Project"},
	 *	summary="Invite a user to the project and asign it a role",
	 *	operationId="inviteProject",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
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
	 *                  description="The invited user email.",
	 *                  property="target_email",
	 *					type="string"
	 *              ),
	 *              @OA\Property(
	 *                  description="The invited user role.",
	 *                  property="role_id",
	 *					type="integer",
	 *                  format="int64",
	 *              ),
	 *              required={"target_email","role_id"}
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
	public function invite(InvitationRequest $request, Project $project, InvitationService $invitationService)
	{
		// Check if the user is authorized to invite users to the project
		$this->authorize('invite', $project);
		
		// Check if the user has already been invited to the project or is already part of it
		$targetUser = User::where('email', $request->target_email)->first();
		if($project->invitations->contains('target_email', $request->target_email) || $project->users->contains($targetUser)) {
			return response()->json(["data" => [
				"message" => "User has already been invited to the project or is already part of it."
			]], 409);
		}

		$id = $this->setId($request);

		$invitation = $invitationService->send($request, $project, $id);

		return new InvitationResource($invitation);
	}
}
