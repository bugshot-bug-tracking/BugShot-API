<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...

use App\Events\ProjectCreated;
use App\Events\ProjectDeleted;
use App\Events\ProjectUserRemoved;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

// Resources
use App\Http\Resources\BugResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUserRoleResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ProjectMarkerResource;

// Services
use App\Services\ImageService;
use App\Services\InvitationService;
use App\Services\ProjectService;
use App\Services\ApiCallService;

// Models
use App\Models\User;
use App\Models\Project;
use App\Models\Company;
use App\Models\ProjectUserRole;
use App\Models\Status;

// Requests
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Requests\ProjectUserRoleUpdateRequest;

/**
 * @OA\Tag(
 *     name="Project",
 * )
 */
class ProjectController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}/projects",
	 *	tags={"Project"},
	 *	summary="All projects.",
	 *	operationId="allProjects",
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
	 *
	 * 	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *      example="BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="timestamp",
	 *		required=false,
	 *		in="header"
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
	 *		name="include-markers",
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
	 *		name="include-project-users-roles",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-project-role",
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
	public function index(Request $request, Company $company)
	{
		// Check if the user is authorized to list the projects of the company
		$this->authorize('viewAny', [Project::class, $company]);

		// Get timestamp
		$timestamp = $request->header('timestamp');
		$userIsPriviliegated = $this->user->isPriviliegated('companies', $company);

		// Check if the request includes a timestamp and query the projects accordingly
		if ($timestamp == NULL) {
			if ($userIsPriviliegated) {
				$projects = $company->projects;
			} else {
				$projects = Auth::user()->projects->where('company_id', $company->id);
				$createdProjects = $this->user->createdProjects->where('company_id', $company->id);
				// Combine the two collections
				$projects = $projects->concat($createdProjects);
			}
		} else {
			if ($userIsPriviliegated) {
				$projects = $company->projects->where("projects.updated_at", ">", date("Y-m-d H:i:s", $timestamp));
			} else {
				$projects = Auth::user()->projects
					->where("projects.updated_at", ">", date("Y-m-d H:i:s", $timestamp))
					->where('company_id', $company->id);
				$createdProjects = $this->user->createdProjects
					->where("projects.updated_at", ">", date("Y-m-d H:i:s", $timestamp))
					->where('company_id', $company->id);

				// Combine the two collections
				$projects = $projects->concat($createdProjects);
			}
		}

		return ProjectResource::collection($projects);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  ProjectStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/companies/{company_id}/projects",
	 *	tags={"Project"},
	 *	summary="Store one project.",
	 *	operationId="storeProject",
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
	 *
	 *	@OA\Parameter(
	 *		name="company_id",
	 *      example="BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB",
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
	 *    			@OA\Property(
	 *                  property="invitations",
	 *                  type="array",
	 * 					@OA\Items(
	 *              		@OA\Property(
	 *              		    description="The invited user email.",
	 *              		    property="target_email",
	 *							type="string"
	 *              		),
	 *              		@OA\Property(
	 *              		    description="The invited user role.",
	 *              		    property="role_id",
	 *              		    type="integer",
	 *              		    format="int64"
	 *              		),
	 * 					)
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
	public function store(ProjectStoreRequest $request, Company $company, ImageService $imageService, InvitationService $invitationService)
	{
		// Check if the user is authorized to create the project
		$this->authorize('create', [Project::class, $company]);

		// Check if the the request already contains a UUID for the project
		$id = $this->setId($request);

		// Store the new project in the database
		$project = $company->projects()->create([
			"id" => $id,
			"user_id" => Auth::user()->id,
			"designation" => $request->designation,
			"color_hex" => $request->color_hex,
			"url" => substr($request->url, -1) == '/' ? substr($request->url, 0, -1) : $request->url // Check if the given url has "/" as last char and if so, store url without it
		]);

		// Check if the project comes with an image (or a color)
		$image = NULL;
		if ($request->base64 != NULL) {
			$image = $imageService->store($request->base64, $image);
			$project->image()->save($image);
		}

		// Send the invitations
		$invitations = $request->invitations;
		if ($invitations != NULL) {
			foreach ($request->invitations as $invitation) {
				$invitationService->send((object) $invitation, $project, (string) Str::uuid(), $invitation['target_email']);
			}
		}

		// Create the default statuses for the new project
		$defaultStatuses = [__('data.backlog'), __('data.todo'), __('data.doing'), __('data.done')];

		foreach ($defaultStatuses as $key => $status) {
			Status::create([
				"id" => (string) Str::uuid(),
				"designation" => $status,
				"order_number" => $key == 3 ? 9999 : $key,
				"project_id" => $project->id,
				"permanent" => $key == 0 || $key == 3 ? ($key == 0 ? 'backlog' : 'done') : NULL, // Check wether the status is backlog or done
			]);
			$key++;
		}

		broadcast(new ProjectCreated($project))->toOthers();

		return new ProjectResource($project);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}/projects/{project_id}",
	 *	tags={"Project"},
	 *	summary="Show one project.",
	 *	operationId="showProject",
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
	 *
	 * 	@OA\Parameter(
	 *		name="company_id",
	 *      example="BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
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
	 *		name="include-markers",
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
	 *		name="include-project-role",
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
	public function show(Company $company, Project $project)
	{
		// Check if the user is authorized to view the project
		$this->authorize('view', $project);

		return new ProjectResource($project);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/interface/projects",
	 *	tags={"Interface"},
	 *	summary="Show one project.",
	 *	operationId="showProjectViaApiKey",
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="api-token",
	 *		required=true,
	 *		in="header",
	 * 		example="d1359f79-ce2d-45b1-8fd8-9566c606aa6c"
	 *	),
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
	 *
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
	 *		name="include-markers",
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
	 *		name="include-project-role",
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
	public function showViaApiKey(Request $request)
	{
		return new ProjectResource($request->get('project'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  ProjectUpdateRequest  $request
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/companies/{company_id}/projects/{project_id}",
	 *	tags={"Project"},
	 *	summary="Update a project.",
	 *	operationId="updateProject",
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
	 *
	 * 	@OA\Parameter(
	 *		name="company_id",
	 *      example="BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
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
	public function update(ProjectUpdateRequest $request, Company $company, Project $project, ImageService $imageService, ProjectService $projectService, ApiCallService $apiCallService)
	{
		// Check if the user is authorized to update the project
		$this->authorize('update', $project);

		return $projectService->update($request, $company, $project, $imageService, $apiCallService);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  ProjectUpdateRequest  $request
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/interface/projects",
	 *	tags={"Interface"},
	 *	summary="Update a project.",
	 *	operationId="updateProjectViaApiKey",
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="api-token",
	 *		required=true,
	 *		in="header",
	 * 		example="d1359f79-ce2d-45b1-8fd8-9566c606aa6c"
	 *	),
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
	 *
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
	 *              required={"designation","url"}
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
	public function updateViaApiKey(ProjectUpdateRequest $request, ImageService $imageService, ProjectService $projectService, ApiCallService $apiCallService)
	{
		$project = $request->get('project');
		$company = Company::find($project->company_id);
		return $projectService->update($request, $company, $project, $imageService, $apiCallService);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/companies/{company_id}/projects/{project_id}",
	 *	tags={"Project"},
	 *	summary="Delete a project.",
	 *	operationId="deleteProject",
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
	 *		name="company_id",
	 *      example="BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
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
	public function destroy(Company $company, Project $project, ImageService $imageService)
	{
		// Check if the user is authorized to delete the project
		$this->authorize('delete', $project);

		// Softdelete the project
		$val = $project->delete();
		broadcast(new ProjectDeleted($project))->toOthers();

		// Delete the respective image if present
		$imageService->delete($project->image);

		return response($val, 204);
	}

	/**
	 * Display the image that belongs to the project.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/image",
	 *	tags={"Project"},
	 *	summary="Project image.",
	 *	operationId="showProjectImage",
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
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
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
	public function image(Project $project, ImageService $imageService)
	{
		// Check if the user is authorized to view the image of the project
		$this->authorize('view', $project);

		return new ImageResource($project->image);
	}

	/**
	 * Display a list of bugs that belong to the project.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/bugs",
	 *	tags={"Project"},
	 *	summary="All project bugs.",
	 *	operationId="allProjectsBugs",
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
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
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
	 *		name="include-markers",
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
	 *		name="include-project-role",
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
	public function bugs(Request $request, Project $project)
	{
		// Check if the user is authorized to list the bugs of the project
		$this->authorize('viewAny', [Bug::class, $project]);

		// Check if the request includes a timestamp and query the bugs accordingly
		if ($request->timestamp == NULL) {
			$bugs = $project->bugs;
		} else {
			$bugs = $project->bugs->where("bugs.updated_at", ">", date("Y-m-d H:i:s", $request->timestamp));
		}

		return BugResource::collection($bugs);
	}

	/**
	 * Display a list of the markers that belong to the project according to a given url.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/markers",
	 *	tags={"Project"},
	 *	summary="All project markers according to a given url.",
	 *	operationId="allProjectMarkers",
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
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="url",
	 *		required=true,
	 *		in="query",
	 *		@OA\Schema(
	 *			type="string"
	 *		)
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
	public function markers(Request $request, Project $project)
	{
		// Check if the user is authorized to list the bugs of the project
		$this->authorize('viewAny', [Bug::class, $project]);

		// Get the bugs that belong to the given url
		$bugs = $project->bugs()->where("url", "=", $request->url)->has('screenshots.markers')->get();

		return ProjectMarkerResource::collection($bugs);
	}

	/**
	 * Display a list of users that belongs to the project.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/users",
	 *	tags={"Project"},
	 *	summary="All project users.",
	 *	operationId="allProjectsUsers",
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
	 *	@OA\Parameter(
	 *		name="project_id",
	 *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
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
	public function users(Project $project, ProjectService $projectService)
	{
		// Check if the user is authorized to view the users of the project
		$this->authorize('view', $project);

		return $projectService->users($project);
	}

	/**
	 * Display a list of users that belongs to the project.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/interface/projects/users",
	 *	tags={"Interface"},
	 *	summary="All project users.",
	 *	operationId="allProjectsUsersViaApiKey",
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="api-token",
	 *		required=true,
	 *		in="header",
	 * 		example="d1359f79-ce2d-45b1-8fd8-9566c606aa6c"
	 *	),
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
	public function usersViaApiKey(Request $request, ProjectService $projectService)
	{
		$project = $request->get('project');

		return $projectService->users($project, true);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  ProjectUserRoleUpdateRequest  $request
	 * @param  Project  $project
	 * @param  User  $user
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/projects/{project_id}/users/{user_id}",
	 *	tags={"Project"},
	 *	summary="Update a users role in a given project.",
	 *	operationId="updateProjectUserRole",
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
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="user_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
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
	 *                  description="The id of the new role",
	 *                  property="role_id",
	 *                  type="integer",
	 *              ),
	 *              required={"role_id"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/ProjectUserRole"
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
	public function updateUserRole(ProjectUserRoleUpdateRequest $request, Project $project, User $user)
	{
		// Check if the user is authorized to update the users role in the given project
		$this->authorize('updateUserRole', $project);

		// Update the companies user role
		$project->users()->updateExistingPivot($user->id, [
			'role_id' => $request->role_id
		]);

		return new ProjectUserRoleResource(ProjectUserRole::where('project_id', $project->id)->where('user_id', $user->id)->first());
	}

	/**
	 * Remove a user from the project
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/projects/{project_id}/users/{user_id}",
	 *	tags={"Project"},
	 *	summary="Remove user from the project.",
	 *	operationId="removeProjectUser",
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
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="user_id",
	 *      example=1,
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
	 *		)
	 *	),
	 *
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
	 *)
	 *
	 **/
	public function removeUser(Project $project, User $user)
	{
		// replace with approval request procedure
		if ((Auth::id() !== $user->id))
			// Check if the user is authorized to view the users of the project
			$this->authorize('removeUser', $project);

		$val = $project->users()->detach($user);
		broadcast(new ProjectUserRemoved($user, $project))->toOthers();

		return response($val, 204);
	}

	/**
	 * Display a list of invitations that belongs to the project.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/invitations",
	 *	tags={"Project"},
	 *	summary="All project invitations.",
	 *	operationId="allProjectInvitations",
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
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
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
	public function invitations(Project $project)
	{
		// Check if the user is authorized to view the invitations of the project
		$this->authorize('viewInvitations', $project);
		$invitations = $project->invitations->where('status_id', '=', 1);
		return InvitationResource::collection($invitations);
	}

	/**
	 * @OA\Post(
	 *	path="/projects/{project_id}/invite",
	 *	tags={"Project"},
	 *	summary="Invite a user to the project and asign it a role",
	 *	operationId="inviteProject",
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
	 *
	 *	@OA\Parameter(
	 *		name="project_id",
	 *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
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
	public function invite(InvitationRequest $request, Project $project, InvitationService $invitationService, ProjectService $projectService)
	{
		// Check if the user is authorized to invite users to the project
		$this->authorize('invite', $project);

		return $projectService->invite($request, $project, $invitationService, $this);
	}

	/**
	 * @OA\Post(
	 *	path="/interface/projects/users/invite",
	 *	tags={"Interface"},
	 *	summary="Invite a user to the project and asign it a role",
	 *	operationId="inviteProjectViaApiKey",
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="api-token",
	 *		required=true,
	 *		in="header",
	 * 		example="d1359f79-ce2d-45b1-8fd8-9566c606aa6c"
	 *	),
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
	 *
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
	public function inviteViaApiKey(InvitationRequest $request, InvitationService $invitationService, ProjectService $projectService)
	{
		$project = $request->get('project');

		return $projectService->invite($request, $project, $invitationService, $this);
	}
}
