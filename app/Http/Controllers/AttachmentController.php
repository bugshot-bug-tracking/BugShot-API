<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachmentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Models\Bug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Attachment",
 * )
 */
class AttachmentController extends Controller
{
	/**
	 * @OA\Get(
	 *	path="/attachment",
	 *	tags={"Attachment"},
	 *	summary="All attachments.",
	 *	operationId="allAttachments",
	 *	security={ {"sanctum": {} }},
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
	public function index()
	{
		return AttachmentResource::collection(Attachment::all());
	}

	/**
	 * @OA\Post(
	 *	path="/attachment",
	 *	tags={"Attachment"},
	 *	summary="Store one attachment.",
	 *	operationId="storeAttachment",
	 *	security={ {"sanctum": {} }},
	 *
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="multipart/form-data",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="bug_id",
	 *                  type="integer",
	 *                  format="int64",
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
	public function store(AttachmentRequest $request)
	{
		$storagePath = "/uploads/attachments";

		$bug = Bug::where("id", $request->bug_id)->with("project")->get()->first();
		$project = $bug->project;
		$company = $project->company;

		$filePath = $storagePath . "/$company->id/$project->id/$bug->id";

		$savedPath = $request->file->store($filePath);

		$attachment = Attachment::create([
			"bug_id" => $request->bug_id,
			"designation" => $request->file->getClientOriginalName(),
			"url" => $savedPath,
		]);

		return new AttachmentResource($attachment);
	}

	/**
	 * @OA\Get(
	 *	path="/attachment/{id}",
	 *	tags={"Attachment"},
	 *	summary="Show one attachment.",
	 *	operationId="showAttachment",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
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
	public function show(Attachment $attachment)
	{
		return new AttachmentResource($attachment);
	}

	/**
	 * @OA\Post(
	 *	path="/attachment/{id}",
	 *	tags={"Attachment"},
	 *	summary="Update one attachment.",
	 *	operationId="updateAttachment",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
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
	 *                  type="integer",
	 *                  format="int64",
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
	public function update(AttachmentRequest $request, Attachment $attachment)
	{
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
	 *	path="/attachment/{id}",
	 *	tags={"Attachment"},
	 *	summary="Delete one attachment.",
	 *	operationId="deleteAttachment",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
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
	public function destroy(Attachment $attachment)
	{
		$val = $attachment->delete();
		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/attachment/{id}/download",
	 *	tags={"Attachment"},
	 *	summary="Download one attachment. (Not Working In Swagger.)",
	 *	operationId="downloadAttachment",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
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
	public function download(Attachment $attachment)
	{
		return Storage::download($attachment->url, $attachment->designation);
	}
}
