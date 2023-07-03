<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...

use App\Events\ProjectCreated;
use App\Events\ProjectDeleted;
use App\Events\ProjectUserRemoved;
use App\Events\ProjectUserUpdated;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

// Resources
use App\Http\Resources\NotificationResource;
use App\Http\Resources\ArchivedBugResource;
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
use App\Models\Notification;
use App\Models\Bug;
use App\Models\ProjectUserRole;
use App\Models\Status;

// Requests
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Requests\ProjectUserRoleUpdateRequest;

/**
 * @OA\Tag(
 *     name="Notification",
 * )
 */
class NotificationController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/users/{user_id}/notifications",
	 *	tags={"Notification"},
	 *	summary="All notifications.",
	 *	operationId="allNotifications",
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
	 *		name="user_id",
	 *		required=true,
	 *      example="1",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success"
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
	public function index(Request $request, User $user)
	{
		// Check if the user is authorized to list the notifications of the user
		$this->authorize('viewAny', [Notification::class, $user]);

		return NotificationResource::collection($user->notifications);
	}


		/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Notification  $notification
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/users/{user_id}/notifications/{notification_id}",
	 *	tags={"Notification"},
	 *	summary="Delete a notification.",
	 *	operationId="deleteNotification",
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
	 *		name="user_id",
	 *      example="1",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Parameter(
	 *		name="notification_id",
	 *		required=true,
	 *		in="path"
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
	public function destroy(User $user, $notification)
	{
		// Check if the user is authorized to delete the notification
		$this->authorize('delete', [Notification::class, $notification]);

		// Softdelete the notification
		$val = $user->notifications()->find($notification)->delete();

		return response($val, 204);
	}
}
