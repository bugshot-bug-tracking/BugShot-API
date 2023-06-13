<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PDF;

// Services
use App\Services\GetUserLocaleService;

// Resources
use App\Http\Resources\ExportResource;

// Models
use App\Models\Bug;
use App\Models\User;
use App\Models\Project;
use App\Models\Export;
use App\Models\Report;

// Requests
use App\Http\Requests\ExportStoreRequest;
use App\Http\Requests\ExportUpdateRequest;
use App\Models\BugExport;

// Notifications
use App\Notifications\ImplementationApprovalFormNotification;
use App\Notifications\ImplementationApprovalFormUnregisteredUserNotification;
use App\Notifications\ApprovalReportNotification;
use App\Notifications\ApprovalReportUnregisteredUserNotification;

// Only owners and managers of the project are allowed to work with the exports

/**
 * @OA\Tag(
 *     name="Export",
 * )
 */
class ExportController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/exports",
	 *	tags={"Export"},
	 *	summary="All exports.",
	 *	operationId="allExports",
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
	 *		name="project_id",
	 *		required=true,
     *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
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
	 *			@OA\Items(ref="#/components/schemas/Export")
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
	public function index(Request $request, Project $project)
	{
		// Check if the user is authorized to list the exports of the project
		// $this->authorize('viewAny', [Export::class, $project]);

		return ExportResource::collection($project->exports);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  ExportStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/projects/{project_id}/exports",
	 *	tags={"Export"},
	 *	summary="Store one export.",
	 *	operationId="storeExport",
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
	 *		name="project_id",
	 *		required=true,
     *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *     			@OA\Property(
	 *                  property="bugs",
	 *                  type="array",
	 * 					@OA\Items(
	 *              		@OA\Property(
	 *              		    description="The id of the bug.",
	 *              		    property="id",
	 *							type="string"
	 *              		),
	 * 					)
	 *              ),
	 *     			@OA\Property(
	 *                  property="recipients",
	 *                  type="array",
	 * 					@OA\Items(
	 *              		@OA\Property(
	 *              		    description="The email of the recipient.",
	 *              		    property="email",
	 *							type="string"
	 *              		),
	 *              		@OA\Property(
	 *              		    description="The name of the recipient.",
	 *              		    property="name",
	 *              		    type="string"
	 *              		),
	 * 					)
	 *              )
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Export"
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
	public function store(Request $request, Project $project) // TODO: Validierung mit ExportStoreRequest
	{
		// Check if the user is authorized to create the export
		// $this->authorize('create', [Export::class, $project]);

		// Check if the the request already contains a UUID for the export
		$id = $this->setId($request);

		// Store the new export in the database
		$export = $project->exports()->create([
			"id" => $id,
			"exported_by" => $this->user->id
		]);

		// Attach the bugs to the export
		foreach($request->bugs as $bug) {
			$export->bugs()->attach($bug["id"]);
			$bug = Bug::find($bug["id"]);
			$bug->update([
				"approval_status_id" => 1
			]);
		}

		foreach($request->recipients as $recipient) {
			// Check if the recipient is a registered user or not
			$user = User::where('email', $recipient["email"])->first();

			if ($user != null) {
				$user->notify((new ImplementationApprovalFormNotification($export, $user))->locale(GetUserLocaleService::getLocale($user)));
			} else {
				Notification::route('email', $recipient["email"])
					->notify((new ImplementationApprovalFormUnregisteredUserNotification($export, $recipient))->locale(GetUserLocaleService::getLocale(Auth::user()))); // Using the sender (Auth::user()) to get the locale because there is not locale setting for an unregistered user. The invitee is most likely to have the same language as the sender
			}
		}

		// Notify the owner as well
		$project->creator->notify((new ImplementationApprovalFormNotification($export, $project->creator))->locale(GetUserLocaleService::getLocale($project->creator)));

		return new ExportResource($export);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Export  $export
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/exports/{export_id}",
	 *	tags={"Export"},
	 *	summary="Show one export.",
	 *	operationId="showExport",
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
	 *		name="include-bugs",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-project-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 *
	 *	@OA\Parameter(
	 *		name="export_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Export/properties/id"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Export"
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
	public function show(Export $export)
	{
		// Check if the user is authorized to view the export
		// $this->authorize('view', $export);

		return new ExportResource($export);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  ExportUpdateRequest  $request
	 * @param  Export  $export
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/projects/{project_id}/exports/{export_id}",
	 *	tags={"Export"},
	 *	summary="Update a export.",
	 *	operationId="updateExport",
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
	 *		name="project_id",
	 *		required=true,
     *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="export_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Export/properties/id"
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
	 * 				@OA\Property(
	 * 					description="The name of the evaluator.",
	 * 					property="evaluator",
	 * 					type="string"
	 * 				),
	 *     			@OA\Property(
	 *                  property="bugs",
	 *                  type="array",
	 * 					@OA\Items(
	 *              		@OA\Property(
	 *              		    description="The id of the bug.",
	 *              		    property="id",
	 *							type="string"
	 *              		),
	 *               		@OA\Property(
	 *              		    description="The status the bug was switched to.",
	 *              		    property="status_id",
	 *							type="string"
	 *              		),
	 * 					)
	 *              ),
	 *     			@OA\Property(
	 *                  property="recipients",
	 *                  type="array",
	 * 					@OA\Items(
	 *              		@OA\Property(
	 *              		    description="The email of the recipient.",
	 *              		    property="email",
	 *							type="string"
	 *              		),
	 *              		@OA\Property(
	 *              		    description="The name of the recipient.",
	 *              		    property="name",
	 *              		    type="string"
	 *              		),
	 * 					)
	 *              )
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Export"
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
	public function update(Request $request, Project $project, Export $export)
	{
		// Check if the user is authorized to update the export
		// $this->authorize('update', $export);

		foreach($request->bugs as $bug) {
			$dbBug = Bug::find($bug["id"]);

			$dbBug->update([
				"approval_status_id" => $bug["status_id"],
			]);
		}

		$report = $this->generateExportPDF($request, $project, $export, $request->bugs, $request->evaluator);

		foreach($request->recipients as $recipient) {
			// Check if the recipient is a registered user or not
			$user = User::where('email', $recipient["email"])->first();

			if ($user != null) {
				$user->notify((new ApprovalReportNotification($report, $export, $request->evaluator, $user))->locale(GetUserLocaleService::getLocale($user)));
			} else {
				Notification::route('email', $recipient["email"])
					->notify((new ApprovalReportUnregisteredUserNotification($report))->locale(GetUserLocaleService::getLocale($export->exporter))); // Using the sender (Auth::user()) to get the locale because there is not locale setting for an unregistered user. The invitee is most likely to have the same language as the sender
			}
		}

		// Notify the owner as well
		$project->creator->notify((new ApprovalReportNotification($report, $export, $request->evaluator))->locale(GetUserLocaleService::getLocale($project->creator)));

		return response()->json([
			"data" => [
				"pdf-download-path" => config("app.url") . "/storage" . $report->url
			]
		], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Export  $export
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/projects/{project_id}/exports/{export_id}",
	 *	tags={"Export"},
	 *	summary="Delete a export.",
	 *	operationId="deleteExport",
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
	 *		name="project_id",
	 *		required=true,
     *      example="CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="export_id",
	 *		required=true,
	 *		example="BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Export/properties/id"
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
	public function destroy(Project $project, Export $export)
	{
		// Check if the user is authorized to delete the export
		$this->authorize('delete', $export);

		$val = $export->delete();
		broadcast(new ExportDeleted($export))->toOthers();

		return response($val, 204);
	}

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function generateExportPDF($request, $project, $export, $bugs, $evaluator)
    {
		$reportId = $this->setId($request);

		$dbBugs = array();
		foreach($bugs as $bug) {
			array_push($dbBugs, Bug::find($bug["id"]));
		};

        $data = [
            'evaluator' => $evaluator,
			'company' => $project->company,
            'project' => $project,
            'bugs' => $dbBugs,
			'reportId' => $reportId
        ];

        $pdf = PDF::loadView('pdfs/export-report', $data);

		$fileName = (preg_replace("/[^0-9]/", "", microtime(true)) . rand(0, 99)) . ".pdf";
		$filePath = "/uploads/reports/" . $project->company->id . "/" . $project->id. "/" . $fileName;
		Storage::disk('public')->put($filePath, $pdf->output());

		$report = Report::create([
			"id" => $reportId,
			"export_id" => $export->id,
			"generated_by" => $evaluator,
			"url" => $filePath
		]);

        return $report;
    }
}
