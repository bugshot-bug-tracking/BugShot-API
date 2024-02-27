<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\NotificationResource;

// Services
use App\Services\NotificationService;

// Models
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

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
	 * @param User $user
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
		$this->authorize('viewAny', [DatabaseNotification::class, $user]);

		return NotificationResource::collection($user->notifications);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  User  $user
	 * @param  DatabaseNotification  $notification
	 * @param  NotificationService  $notificationService
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
	public function destroy(User $user, DatabaseNotification $notification, NotificationService $notificationService)
	{
		// Check if the user is authorized to delete the notification
		$this->authorize('delete', [DatabaseNotification::class, $notification]);

		// Delete the notification
		$val = $notificationService->delete($user, $notification);

		return response($val, 204);
	}

	/**
	 * Remove all notifications of a user
	 *
	 * @param  Notification  $notification
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/users/{user_id}/notifications",
	 *	tags={"Notification"},
	 *	summary="Delete all notifications.",
	 *	operationId="deleteAllNotification",
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
	public function destroyAll(User $user)
	{
		// Check if the user is authorized to delete the notifications
		$this->authorize('deleteAll', [DatabaseNotification::class, $user]);

		// Delete the notifications
		$val = $user->notifications()->delete();

		return response($val, 204);
	}
}
