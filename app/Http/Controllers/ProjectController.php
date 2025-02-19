<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\BugResource;
use App\Http\Resources\ArchivedBugResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUserRoleResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ProjectMarkerResource;
use App\Http\Resources\JiraProjectLinkResource;
use App\Http\Resources\UserResource;

// Services
use App\Services\ImageService;
use App\Services\InvitationService;
use App\Services\ProjectService;
use App\Services\ApiCallService;
use App\Services\AtlassianService;

// Events
use App\Events\ProjectCreated;
use App\Events\ProjectDeleted;
use App\Events\ProjectUserRemoved;
use App\Events\ProjectUserUpdated;
use App\Events\JiraProjectLinkUpdated;
use App\Events\ProjectJiraConnected;
use App\Events\ProjectJiraDisconnected;

// Models
use App\Models\User;
use App\Models\Project;
use App\Models\Company;
use App\Models\Bug;
use App\Models\ProjectUserRole;
use App\Models\Status;
use App\Models\OrganizationUserRole;
use App\Models\Priority;

// Requests
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Requests\ProjectUserRoleUpdateRequest;
use App\Http\Requests\UpdateJiraSettings;

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
	 *		name="filter-bugs-by-assigned",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-deadline",
	 *		required=false,
	 *		in="header",
	 *      example=">|1693393188"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-creator-id",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-priority",
	 *		required=false,
	 *		in="header",
	 *      example="Minor"
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
	 * 	@OA\Parameter(
	 *		name="only-favorites",
	 *		required=false,
	 *		in="header"
	 *	),
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

		$timestamp = $request->header('timestamp');
		$userIsPriviliegated = $this->user->isPriviliegated('companies', $company);

		if ($userIsPriviliegated) {
			$projects = $company->projects->when($timestamp, function ($query, $timestamp) {
				return $query->where("projects.updated_at", ">", date("Y-m-d H:i:s", $timestamp));
			});
		} else {
			$projects = Auth::user()->projects
				->when($timestamp, function ($query, $timestamp) {
					return $query->where("projects.updated_at", ">", date("Y-m-d H:i:s", $timestamp));
				})
				->where('company_id', $company->id);
			$createdProjects = $this->user->createdProjects
				->when($timestamp, function ($query, $timestamp) {
					return $query->where("projects.updated_at", ">", date("Y-m-d H:i:s", $timestamp));
				})
				->where('company_id', $company->id);

			// Combine the two collections
			$projects = $projects->concat($createdProjects);
		}

		if ($request->header('only-favorites')) {
			$projects = $projects->filter(function ($value, $key) {
				$isFavorite = ProjectUserRole::where('project_id', $value->id)->where('user_id', $this->user->id)->pluck('is_favorite');
				return $isFavorite[0] == 1;
			});
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

		$project = new Project();
		$project->id = $id;
		$project->user_id = Auth::user()->id;
		$project->designation = $request->designation;
		$project->color_hex = $request->color_hex;
		$project->url = substr($request->url, -1) == '/' ? substr($request->url, 0, -1) : $request->url; // Check if the given url has "/" as last char and if so, store url without it

		$project->company()->associate($company);

		// Do the save and fire the custom event
		$project->fireCustomEvent('projectCreated');
		$project->save();

		// Also add the owner to the project user role table
		$this->user->projects()->attach($project->id, ['role_id' => 0]);

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
	 *		name="filter-bugs-by-assigned",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-deadline",
	 *		required=false,
	 *		in="header",
	 *      example=">|1693393188"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-creator-id",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-priority",
	 *		required=false,
	 *		in="header",
	 *      example="Minor"
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
	 *		name="filter-bugs-by-assigned",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-deadline",
	 *		required=false,
	 *		in="header",
	 *      example=">|1693393188"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-creator-id",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-priority",
	 *		required=false,
	 *		in="header",
	 *      example="Minor"
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

		// Do the delete and fire the custom event
		$project->fireCustomEvent('projectDeleted');

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
	 *		name="filter-bugs-by-assigned",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-deadline",
	 *		required=false,
	 *		in="header",
	 *      example=">|1693393188"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-creator-id",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="filter-bugs-by-priority",
	 *		required=false,
	 *		in="header",
	 *      example="Minor"
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

		$header = $request->header();

		// Check if the request includes a timestamp and query the bugs accordingly
		if ($request->timestamp == NULL) {
			if (array_key_exists('filter-bugs-by-assigned', $header) && $header['filter-bugs-by-assigned'][0] == "true") {
				$bugs = Auth::user()->bugs()
					->where("project_id", $project->id)
					->where("archived_at", NULL);
			} else {
				$bugs = $project->bugs()->where("bugs.archived_at", NULL);
			}
		} else {
			if (array_key_exists('filter-bugs-by-assigned', $header) && $header['filter-bugs-by-assigned'][0] == "true") {
				$bugs = Auth::user()->bugs()
					->where("project_id", $project->id)
					->where("updated_at", ">", date("Y-m-d H:i:s", $request->timestamp))
					->where("archived_at", NULL);
			} else {
				$bugs = $project->bugs()->where("bugs.updated_at", ">", date("Y-m-d H:i:s", $request->timestamp))->where("bugs.archived_at", NULL);
			}
		}

		if (array_key_exists('filter-bugs-by-assigned', $header) && $header['filter-bugs-by-assigned'][0] == "true") {
			$searchTermPrefix = "";
		} else {
			$searchTermPrefix = "bugs.";
		}

		// Add filters
		$bugs = $bugs->when(array_key_exists('filter-bugs-by-deadline', $header) && !empty($header['filter-bugs-by-deadline'][0]), function ($query) use ($header, $searchTermPrefix) {
			$deadline = $header['filter-bugs-by-deadline'][0];
			$array = explode('|', $deadline);
			$operator = $array[0];
			$date = date("Y-m-d H:i:s", $array[1]);

			return $query->where($searchTermPrefix . "deadline", $operator, $date);
		})
			->when(array_key_exists('filter-bugs-by-creator-id', $header) && !empty($header['filter-bugs-by-creator-id'][0]), function ($query) use ($header, $searchTermPrefix) {
				$creatorId = $header['filter-bugs-by-creator-id'][0];

				return $query->where($searchTermPrefix . "user_id", $creatorId);
			})
			->when(array_key_exists('filter-bugs-by-priority', $header) && !empty($header['filter-bugs-by-priority'][0]), function ($query) use ($header, $searchTermPrefix) {
				$designation = $header['filter-bugs-by-priority'][0];
				$priority = Priority::where('designation', $designation)->firstOrFail();

				return $query->where($searchTermPrefix . "priority_id", $priority->id);
			})
			->get();

		return BugResource::collection($bugs);
	}

	/**
	 * Display a list of bugs that belong to the project and were archived.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/archived-bugs",
	 *	tags={"Project"},
	 *	summary="All project archived bugs.",
	 *	operationId="allProjectsArchivedBugs",
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
	 *		name="filter-bugs-by-assigned",
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
	public function archivedBugs(Request $request, Project $project)
	{
		// Check if the user is authorized to list the bugs of the project
		$this->authorize('viewAny', [Bug::class, $project]);

		$header = $request->header();

		if (array_key_exists('filter-bugs-by-assigned', $header) && $header['filter-bugs-by-assigned'][0] == "true") {
			$bugs = Auth::user()->bugs()
				->where("project_id", $project->id)
				->whereNot("archived_at", NULL)
				->withTrashed()
				->get();
		} else {
			// Get all archived bugs
			$bugs = $project->bugs()->whereNot("archived_at", NULL)
				->withTrashed()
				->get();
		}

		return ArchivedBugResource::collection($bugs);
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
		$bugs = $project->bugs()->where("url", "=", $request->url)->whereNull('done_at')->has('screenshots.markers')->get();

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
	 * Display a list of assignable users that have access to the project.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/assignable-users",
	 *	tags={"Project"},
	 *	summary="All assignable users.",
	 *	operationId="allAssignableProjectUsers",
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
	 *			@OA\Items(ref="#/components/schemas/User")
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
	public function assignableUsers(Project $project)
	{
		// Check if the user is authorized to view the users of the project
		$this->authorize('view', $project);

		$assignableUsers = $project->users;

		// Add company users
		$companyUsers = $project->company->users()->whereNotIn('id', $assignableUsers->pluck('id')->toArray())->wherePivot('role_id', '<=', 1)->get();
		if (!$companyUsers->isEmpty()) {
			foreach ($companyUsers as $companyUser) {
				$assignableUsers->push($companyUser);
			}
		}

		// Add organization users
		$organizationUsers = $project->company->organization->users()->whereNotIn('id', $assignableUsers->pluck('id')->toArray())->wherePivot('role_id', '<=', 1)->get();
		if (!$organizationUsers->isEmpty()) {
			foreach ($organizationUsers as $organizationUser) {
				$assignableUsers->push($organizationUser);
			}
		}

		return UserResource::collection($assignableUsers);
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

		// Update the projects user role
		$project->users()->updateExistingPivot($user->id, [
			'role_id' => $request->role_id
		]);

		broadcast(new ProjectUserUpdated($user, $project))->toOthers();

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


	/**
	 * Move bugs to new project.
	 *
	 * @param  Request  $request
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/projects/{project_id}/bugs/move-to-new-project",
	 *	tags={"Project"},
	 *	summary="Move bugs to new project.",
	 *	operationId="moveBugsToNewProject",
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
	 *                  description="The id of the new project",
	 *                  property="target_project_id",
	 *                  type="string",
	 *              ),
	 *   			@OA\Property(
	 *                  property="bugs",
	 *                  type="array",
	 * 					@OA\Items(
	 * 	   					@OA\Property(
	 *    						description="The id of the bug",
	 *              		    property="id",
	 *              		    type="string"
	 *              		),
	 * 					)
	 *              ),
	 *              required={"target_project_id"}
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
	public function moveBugsToDifferentProject(Request $request, Project $project)
	{
		// Check if the user is authorized to move bugs to another project
		$this->authorize('moveBugs', $project);

		$targetProject = Project::find($request->target_project_id);
		$bugs = $request->bugs;
		$targetProjetMembers = $targetProject->users;

		foreach ($bugs as $bug) {
			$bug = Bug::find($bug["id"]);

			// Check if the bug is not part of the original project anymore
			if ($project->bugs->contains($bug)) {
				$bugAssignees = $bug->users;
				$targetStatusId = $targetProject->statuses()->where("permanent", "backlog")->pluck("id")->first();

				// Remove the assignees from the bug that are not part of the new project
				$diffUsers = $bugAssignees->diff($targetProjetMembers)->pluck("id");
				$bug->users()->detach($diffUsers);

				$bug->update([
					"project_id" => $targetProject->id,
					"status_id" => $targetStatusId,
					"ai_id" => 	$targetProject->bugs()->max("ai_id") + 1
				]);
			}
		}

		return new ProjectResource($project);
	}

	/**
	 * Move project to a new company.
	 *
	 * @param  Request  $request
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/projects/{project_id}/move-to-new-company",
	 *	tags={"Project"},
	 *	summary="Move project to new company.",
	 *	operationId="moveProjectToNewCompany",
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
	 *                  description="The id of the new company",
	 *                  property="target_company_id",
	 *                  type="string",
	 *              ),
	 *              required={"target_company_id"}
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
	public function moveProjectToNewCompany(Request $request, Project $project)
	{
		// Check if the user is authorized to move the project to another company
		$this->authorize('moveProject', $project);

		$targetCompany = Company::find($request->target_company_id);

		// Check if the user is authorized to move the project to this exact company
		$this->authorize('create', [Project::class, $targetCompany]);

		// Check if the target company lies in a new organization
		if ($project->company->organization_id !== $targetCompany->organization_id) {
			// Check which of the project members is not part of the new company
			$usersNotInTargetCompany = $project->users->diff($targetCompany->users);
			foreach ($usersNotInTargetCompany as $user) {
				// Check if the user is already part of this company
				if ($user->companies->find($targetCompany) == NULL) {
					$user->companies()->attach($targetCompany->id, ['role_id' => 2]); // Team
				}
			}

			$targetOrganization = $targetCompany->organization;
			// Check which of the project members is not part of the new organization
			$usersNotInTargetOrga = $project->users->diff($targetOrganization->users);
			foreach ($usersNotInTargetOrga as $user) {
				// Check if the user is already part of this organization
				if ($user->organizations->find($project->company->organization) == NULL) {
					$organizationUserRole = OrganizationUserRole::where("user_id", $user->id)->whereNot("subscription_item_id", NULL)->first();

					if ($organizationUserRole != NULL) {
						$user->organizations()->attach($targetOrganization->id, ['role_id' => 2, "subscription_item_id" => $organizationUserRole->subscription_item_id]); // Adding the subscription is only for the current state. Later, when subscriptions should be restricted, we need to change that
					} else {
						$user->organizations()->attach($targetOrganization->id, ['role_id' => 2]); // Adding the subscription is only for the current state. Later, when subscriptions should be restricted, we need to change that
					}
				}
			}
		}

		// Do the update and fire the custom event
		$project->company_id = $targetCompany->id;
		$project->fireCustomEvent('movedToNewGroup');

		$project->withoutEvents(function () use ($project) {
			$project->save();
		});

		return new ProjectResource($project);
	}

	/**
	 * Mark the specified resource as favorite.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/mark-as-favorite",
	 *	tags={"Project"},
	 *	summary="Mark one project as favorite.",
	 *	operationId="markProjectAsFavorite",
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
	public function markAsFavorite(Project $project)
	{
		// Check if the user is authorized to view the project
		$this->authorize('view', $project);

		$projectUserRole = ProjectUserRole::where('project_id', $project->id)
			->where('user_id', $this->user->id)
			->firstOrFail();

		// Update the project user role
		$project->users()->updateExistingPivot($this->user->id, [
			'is_favorite' => !$projectUserRole->is_favorite
		]);

		return new ProjectResource($project);
	}

	public function createJiraLink(Request $request, Project $project, AtlassianService $atlassian)
	{
		// Check if the user is authorized for the action
		$this->authorize('update', $project);

		$validated = $request->validate([
			"code" => ["required", "string"],
		]);

		if ($project->jiraLink) {
			broadcast(new ProjectJiraConnected($project));
			return response()->json(['message' => 'Link already exists.'], 200);
		}

		$atlassian->createJiraLink($request, $project);

		broadcast(new ProjectJiraConnected($project));

		return response()->json(['message' => 'Link created successfully.'], 201);
	}

	public function deleteJiraLink(Project $project, AtlassianService $atlassian)
	{
		// Check if the user is authorized for the action
		$this->authorize('update', $project);

		$project->jiraLink->delete();

		broadcast(new ProjectJiraDisconnected($project))->toOthers();

		return response(null, 204);
	}

	public function getJiraSettings(Project $project, AtlassianService $atlassian)
	{
		// Check if the user is authorized for the action
		$this->authorize('update', $project);

		if (!$project->jiraLink) {
			return response()->json(['message' => 'No link exists.'], 404);
		}

		return new JiraProjectLinkResource($project->jiraLink);
	}

	public function getJiraSites(Project $project, AtlassianService $atlassian)
	{
		// Check if the user is authorized for the action
		$this->authorize('update', $project);

		return $atlassian->getSites($project);
	}

	public function setJiraSite(Request $request, Project $project, AtlassianService $atlassian)
	{
		// Check if the user is authorized for the action
		$this->authorize('update', $project);

		//TODO create case for site delete that implies clearing the jira project selected
		$project->jiraLink->update([
			"site_id" => $request->id,
			"site_name" => $request->name,
			"site_url" => $request->url
		]);

		broadcast(new JiraProjectLinkUpdated($project))->toOthers();

		return new JiraProjectLinkResource($project->jiraLink);
	}

	public function deleteJiraSite(Project $project, AtlassianService $atlassian)
	{
		// Check if the user is authorized for the action
		$this->authorize('update', $project);

		//TODO create case for site delete that implies clearing the jira project selected
		$project->jiraLink->update([
			"site_id" => null,
			"site_name" => null,
			"site_url" => null,

			"jira_project_id" => null,
			"jira_project_name" => null,
			"jira_project_key" => null,
		]);

		broadcast(new JiraProjectLinkUpdated($project))->toOthers();

		return new JiraProjectLinkResource($project->jiraLink);
	}

	public function getJiraProjects(Request $request, Project $project, AtlassianService $atlassian)
	{
		// Check if the user is authorized for the action
		$this->authorize('update', $project);

		return $atlassian->getProjects($request, $project);
	}

	public function setJiraProject(Request $request, Project $project, AtlassianService $atlassian)
	{
		// Check if the user is authorized for the action
		$this->authorize('update', $project);

		$project->jiraLink->update([
			"jira_project_id" => $request->id,
			"jira_project_name" => $request->name,
			"jira_project_key" => $request->key,
			"jira_issuetype_id" => $request->issuetype
		]);

		broadcast(new JiraProjectLinkUpdated($project))->toOthers();

		return new JiraProjectLinkResource($project->jiraLink);
	}

	public function deleteJiraProject(Project $project, AtlassianService $atlassian)
	{
		// Check if the user is authorized for the action
		$this->authorize('update', $project);

		$project->jiraLink->update([
			"jira_project_id" => null,
			"jira_project_name" => null,
			"jira_project_key" => null,
		]);

		broadcast(new JiraProjectLinkUpdated($project))->toOthers();

		return new JiraProjectLinkResource($project->jiraLink);
	}

	public function updateJiraSettings(UpdateJiraSettings $request, Project $project, AtlassianService $atlassian)
	{
		// Check if the user is authorized for the action
		$this->authorize('update', $project);

		if (!$project->jiraLink) {
			return response()->json(['message' => 'Project link does not exists.'], 404);
		}

		$validatedData = $request->validated();

		$project->jiraLink->update($validatedData);

		if (isset($validatedData['sync_comments_from_jira'])) {
			$atlassian->refreshWebhook('sync_comments_from_jira', $project);
		}

		if (isset($validatedData['update_status_from_jira'])) {
			$atlassian->refreshWebhook('update_status_from_jira', $project);
		}


		broadcast(new JiraProjectLinkUpdated($project))->toOthers();

		return new JiraProjectLinkResource($project->jiraLink);
	}
}
