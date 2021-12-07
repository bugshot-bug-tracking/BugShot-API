<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
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
use App\Http\Requests\AttachmentRequest;

/**
 * @OA\Tag(
 *     name="Attachment",
 * )
 */
class AttachmentController extends Controller
{
	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/attachments ",
	 *	tags={"Attachment"},
	 *	summary="All attachments.",
	 *	operationId="allAttachments",
	 *	security={ {"sanctum": {} }},
	 *
	 * 	@OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 * 
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
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Bug $bug)
	{
		// Check if the user is authorized to list the attachments of the bug
		$this->authorize('viewAny', [Attachment::class, $bug]);

		return AttachmentResource::collection($bug->attachments);
	}

	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/attachments ",
	 *	tags={"Attachment"},
	 *	summary="Store one attachment.",
	 *	operationId="storeAttachment",
	 *	security={ {"sanctum": {} }},
	 *
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
	 *          mediaType="multipart/form-data",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="bug_id",
	 * 					type="string",
	 *  				maxLength=255,
	 *              ),
	 *              @OA\Property(
	 *                  description="Binary content of file",
	 *                  property="file",
	 *                  type="string",
	 *                  format="binary",
	 *              ),
	 *              required={"bug_id","file"}
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
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\AttachmentRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(AttachmentRequest $request, Bug $bug, AttachmentService $attachmentService)
	{
		// Check if the user is authorized to create the attachment
		$this->authorize('create', [Attachment::class, $bug]);

		$attachment = $attachmentService->store($bug, $request);

		return new AttachmentResource($attachment);
	}

	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/attachments/{attachment_id}",
	 *	tags={"Attachment"},
	 *	summary="Show one attachment.",
	 *	operationId="showAttachment",
	 *	security={ {"sanctum": {} }},
	 *
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
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Attachment  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function show(Bug $bug, Attachment $attachment)
	{
		// Check if the user is authorized to view the attachment
		$this->authorize('view', [Attachment::class, $bug]);

		return new AttachmentResource($attachment);
	}

	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/attachments/{attachment_id}",
	 *	tags={"Attachment"},
	 *	summary="Update one attachment.",
	 *	operationId="updateAttachment",
	 *	security={ {"sanctum": {} }},
	 *
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
	 *          mediaType="multipart/form-data",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  description="Binary content of file",
	 *                  property="bug_id",
	 * 					type="string",
	 *  				maxLength=255,
	 *              ),
	 *              @OA\Property(
	 *                  description="Binary content of file",
	 *                  property="file",
	 *                  type="string",
	 *                  format="binary",
	 *              ),
	 *              required={"bug_id","file"}
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
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\AttachmentRequest  $request
	 * @param  \App\Models\Attachment  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function update(AttachmentRequest $request, Bug $bug, Attachment $attachment)
	{
		// Check if the user is authorized to update the attachment
		$this->authorize('update', [Attachment::class, $bug]);

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
	 * @OA\Delete(
	 *	path="/bugs/{bug_id}/attachments/{attachment_id}",
	 *	tags={"Attachment"},
	 *	summary="Delete one attachment.",
	 *	operationId="deleteAttachment",
	 *	security={ {"sanctum": {} }},
	 *
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
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Attachment  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Bug $bug, Attachment $attachment, AttachmentService $attachmentService)
	{
		// Check if the user is authorized to delete the attachment
		$this->authorize('delete', [Attachment::class, $bug]);

		$val = $attachmentService->delete($attachment);

		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/attachments/{attachment_id}/download",
	 *	tags={"Attachment"},
	 *	summary="Download one attachment. (Not Working In Swagger.)",
	 *	operationId="downloadAttachment",
	 *	security={ {"sanctum": {} }},
	 *
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
	/**
	 * Download the specified resource.
	 *
	 * @param  \App\Models\Attachment  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function download(Bug $bug, Attachment $attachment)
	{
		return Storage::download($attachment->url, $attachment->designation);
	}
}
