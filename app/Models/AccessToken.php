<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCustomEvents;

/**
 * @OA\Schema()
 */
class AccessToken extends Model
{
	use HasFactory, HasCustomEvents;

	/**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

	protected $table = 'project_access_tokens';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

	protected $observables = [
		'accessTokenCreated',
		'accessTokenUpdated',
		'accessTokenDeleted',
		'accessTokenRestored',
		'accessTokenForceDeleted'
	];

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="string",
	 *  maxLength=255,
	 * )
	 *
	 * @OA\Property(
	 * 	property="access_token",
	 * 	type="string",
	 * 	description="The token that lets anonymous users/clients send bugs to the project"
	 * )
	 *
	 * @OA\Property(
	 * 	property="description",
	 * 	type="string",
	 * 	description="The description of the bug."
	 * )
	 *
	 * @OA\Property(
	 * 	property="user_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The user that generated the access token."
	 * )
	 *
	 * @OA\Property(
	 * 	property="project_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the project to which the access token belongs."
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

	protected $fillable = ["id", "description", "user_id", "access_token"];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function project()
	{
		return $this->belongsTo(Project::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function creator()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
