<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema()
 */
class Comment extends Model
{
	use HasFactory, SoftDeletes;

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
	 * 	property="bug_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the bug to which the object belongs."
	 * )
	 *
	 * @OA\Property(
	 * 	property="user_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user to which the object belongs."
	 * )
	 *
	 * @OA\Property(
	 * 	property="client_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The client that was used to create the bug."
	 * )
	 *
	 * @OA\Property(
	 * 	property="content",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The message."
	 * )
	 *
	 * @OA\Property(
	 * 	property="is_internal",
	 * 	type="boolean"
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
	 */
	protected $fillable = ["id", "bug_id", "user_id", "client_id", "content", "is_internal"];

	protected $touches = ['bug'];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function bug()
	{
		return $this->belongsTo(Bug::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function client()
	{
		return $this->belongsTo(Client::class);
	}
}
