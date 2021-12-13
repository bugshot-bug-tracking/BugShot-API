<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class Bug extends Model
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
	 * 	property="project_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the project to which the object belongs."
	 * )
	 *
	 * @OA\Property(
	 * 	property="user_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user that created the object."
	 * )
	 *
	 * @OA\Property(
	 * 	property="designation",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The bug title."
	 * )
	 *
	 * @OA\Property(
	 * 	property="description",
	 * 	type="string",
	 * 	description="The description of the bug."
	 * )
	 *
	 * @OA\Property(
	 * 	property="url",
	 * 	type="string",
	 * 	description="The url where the bug was spoted. (Validated)"
	 * )
	 *
	 * @OA\Property(
	 * 	property="status_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the status to which the object belongs."
	 * )
	 *
	 * @OA\Property(
	 * 	property="priority_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the priority that the bug has."
	 * )
	 *
	 * @OA\Property(
	 * 	property="operating_system",
	 * 	type="string",
	 *  maxLength=255,
	 *  nullable=true,
	 * 	description="The operating system used to report the bug."
	 * )
	 *
	 * @OA\Property(
	 * 	property="browser",
	 * 	type="string",
	 *  maxLength=255,
	 *  nullable=true,
	 * 	description="The browser used to report the bug."
	 * )
	 *
	 * @OA\Property(
	 * 	property="selector",
	 * 	type="string",
	 *  nullable=true,
	 * 	description="The path to the HTML element from root."
	 * )
	 *
	 * @OA\Property(
	 * 	property="resolution",
	 * 	type="string",
	 *  maxLength=255,
	 *  nullable=true,
	 * 	description="The resolution of the display."
	 * )
	 *
	 * @OA\Property(
	 * 	property="deadline",
	 * 	type="string",
	 *  format="date-time",
	 *  nullable=true,
	 * 	description="The deadline of the bug."
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

	protected $fillable = ["id", "project_id", "user_id", "designation", "description", "url", "status_id", "priority_id", "order_number", "operating_system", "browser", "selector", "resolution", "deadline", "deleted_at"];

	protected $touches = ["project", "status"];

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
    public function users()
    {
        return $this->belongsToMany(User::class, 'bug_user_roles')->withPivot('role_id');
    }

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function status()
	{
		return $this->belongsTo(Status::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function priority()
	{
		return $this->belongsTo(Priority::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function screenshots()
	{
		return $this->hasMany(Screenshot::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function attachments()
	{
		return $this->hasMany(Attachment::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function comments()
	{
		return $this->hasMany(Comment::class);
	}
}
