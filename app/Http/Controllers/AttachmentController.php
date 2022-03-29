<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Resources
use App\Http\Resources\AttachmentResource;

// Services
use App\Services\AttachmentService;

// Models
use App\Models\Attachment;
use App\Models\Bug;

// Requests
use App\Http\Requests\AttachmentStoreRequest;
use App\Http\Requests\AttachmentUpdateRequest;

/**
 * @OA\Tag(
 *     name="Attachment",
 * )
 */
class AttachmentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/attachments",
	 *	tags={"Attachment"},
	 *	summary="All attachments.",
	 *	operationId="allAttachments",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
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
	 *			@OA\Items(ref="#/components/schemas/Attachment")
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
	public function index(Bug $bug)
	{
		// Check if the user is authorized to list the attachments of the bug
		$this->authorize('viewAny', [Attachment::class, $bug->project]);

		return AttachmentResource::collection($bug->attachments);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  AttachmentStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/attachments ",
	 *	tags={"Attachment"},
	 *	summary="Store one attachment.",
	 *	operationId="storeAttachment",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 * 
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  	   		@OA\Property(
	 *                  property="designation",
	 *                  type="string"
	 *              ),
	 * 	   			@OA\Property(
	 *                  property="base64",
	 *                  type="string"
	 *              ),
	 *              required={"base64", "designation"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Attachment"
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
	public function store(AttachmentStoreRequest $request, Bug $bug, AttachmentService $attachmentService)
	{
		// Check if the user is authorized to create the attachment
		$this->authorize('create', [Attachment::class, $bug->project]);

		$attachment = $attachmentService->store($bug, $request);

		return new AttachmentResource($attachment);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Attachment  $attachment
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/attachments/{attachment_id}",
	 *	tags={"Attachment"},
	 *	summary="Show one attachment.",
	 *	operationId="showAttachment",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="attachment_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Attachment/properties/id"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Attachment"
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
	public function show(Bug $bug, Attachment $attachment)
	{
		// Check if the user is authorized to view the attachment
		$this->authorize('view', [Attachment::class, $bug->project]);

		return new AttachmentResource($attachment);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  AttachmentUpdateRequest  $request
	 * @param  Attachment  $attachment
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/bugs/{bug_id}/attachments/{attachment_id}",
	 *	tags={"Attachment"},
	 *	summary="Update one attachment.",
	 *	operationId="updateAttachment",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachment-base64",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="attachment_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Attachment/properties/id"
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
	 *  	   		@OA\Property(
	 *                  property="designation",
	 *                  type="string"
	 *              ),
	 * 	   			@OA\Property(
	 *                  property="base64",
	 *                  type="string"
	 *              ),
	 *              required={"base64", "designation"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Attachment"
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
	 *)
	 *
	 **/
	public function update(AttachmentUpdateRequest $request, Bug $bug, Attachment $attachment)
	{
		// Check if the user is authorized to update the attachment
		$this->authorize('update', [Attachment::class, $bug->project]);

		$storagePath = "/uploads/attachments";

		$bug = Bug::where("id", $attachment->bug_id)->with("project")->get()->first();
		$project = $bug->project;
		$company = $project->company;

		$filePath = $storagePath . "/$company->id/$project->id/$bug->id";

		$savedPath = $request->file->store($filePath);

		Storage::delete($attachment->url);

		$attachment->update([
			"designation" => $request->file->getClientOriginalName(),
			"url" => $savedPath,
		]);

		return new AttachmentResource($attachment);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Attachment  $attachment
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/bugs/{bug_id}/attachments/{attachment_id}",
	 *	tags={"Attachment"},
	 *	summary="Delete one attachment.",
	 *	operationId="deleteAttachment",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="attachment_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Attachment/properties/id"
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
	 *)
	 *
	 **/
	public function destroy(Bug $bug, Attachment $attachment, AttachmentService $attachmentService)
	{
		// Check if the user is authorized to delete the attachment
		$this->authorize('delete', [Attachment::class, $attachment->bug->project]);

		$val = $attachmentService->delete($attachment);

		return response($val, 204);
	}

	/**
	 * Download the specified resource.
	 *
	 * @param  Attachment  $attachment
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/attachments/{attachment_id}/download",
	 *	tags={"Attachment"},
	 *	summary="Download one attachment. (Not Working In Swagger.)",
	 *	operationId="downloadAttachment",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="attachment_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Attachment/properties/id"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\Schema(
	 * 			type="string",
	 * 			format="binary",
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
	public function download(Attachment $attachment)
	{
		return Storage::download($attachment->url, $attachment->designation);
	}
}
