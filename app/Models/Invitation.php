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
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="string",
	 *  maxLength=255,
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
	 * @OA\Property(
	 * 	property="deleted_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The deletion date."
	 * )
	 * 
	 */
	protected $fillable = ["id", "sender_id", "target_email", "role_id", "status_id", "deleted_at"];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
	public function invitable()
	{
		return $this->morphTo();
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function sender()
	{
		return $this->belongsTo(User::class, "sender_id");
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function target()
	{
		return $this->belongsTo(User::class, "target_id");
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function role()
	{
		return $this->belongsTo(Role::class, "role_id");
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function status()
	{
		return $this->belongsTo(InvitationStatus::class, "status_id");
	}
}
