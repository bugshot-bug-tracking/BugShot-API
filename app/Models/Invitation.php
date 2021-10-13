<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class Invitation extends Model
{
	use HasFactory;

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="integer",
	 *  format="int64",
	 * )
	 *
	 * @OA\Property(
	 * 	property="sender_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user who initiated the invitation."
	 * )
	 *
	 * @OA\Property(
	 * 	property="target_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user whom the invitation is for."
	 * )
	 *
	 * @OA\Property(
	 * 	property="invitable_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the invitable resource."
	 * )
	 *
	 * @OA\Property(
	 * 	property="invitable_type",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The type of the invitable resource."
	 * )
	 *
	 * @OA\Property(
	 * 	property="role_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The role id for the invited user."
	 * )
	 *
	 * @OA\Property(
	 * 	property="status_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the status of the invitation."
	 * )
	 *
	 * @OA\Property(
	 * 	property="created_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The creation date."
	 * )
	 *
	 * @OA\Property(
	 * 	property="updated_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The last date when the resource was changed."
	 * )
	 *
	 */
	protected $fillable = ["sender_id",	"target_id", "role_id", "status_id"];


	public function invitable()
	{
		return $this->morphTo();
	}

	public function sender()
	{
		return $this->belongsTo(User::class, "sender_id");
	}

	public function target()
	{
		return $this->belongsTo(User::class, "target_id");
	}

	public function role()
	{
		return $this->belongsTo(Role::class, "role_id");
	}

	public function status()
	{
		return $this->belongsTo(InvitationStatus::class, "status_id");
	}
}
