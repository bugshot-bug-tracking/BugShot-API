<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Laravel\Scout\Searchable;

/**
 * @OA\Schema()
 */
class Bug extends Model
{
	use HasFactory, Searchable, SoftDeletes, CascadeSoftDeletes;

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
	 * Get the indexable data array for the model.
	 *
	 * @return array
	 */
	public function toSearchableArray()
	{
		return [
			'id' => $this->id,
			'designation' => $this->designation,
			'description' => $this->description,
			'url' => $this->url
		];
	}

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
	 * @OA\Property(
	 * 	property="approval_status_id",
	 * 	type="string",
	 * 	description="The id of the approval status that the bug has."
	 * )
	 * @OA\Property(
	 * 	property="time_estimation",
	 * 	type="string",
	 * 	description="The time estimation that of the bug."
	 * )
	 * @OA\Property(
	 * 	property="time_estimation_type",
	 * 	type="char",
	 * 	description="The type of the time estimation, like w(week), d(day), h(hour), m(minute)."
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
	 * 	property="order_number",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The order number."
	 * )
	 *
	 * @OA\Property(
	 * 	property="ai_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="Auto-Incrementing Id."
	 * )
	 *
	 * * @OA\Property(
	 * 	property="client_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The client that was used to create the bug."
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
	 * 	property="done_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The date when the bug was moved to status done."
	 * )
	 *
	 * @OA\Property(
	 * 	property="archived_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The date when the bug was archived."
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
	 *
	 */

	protected $fillable = ["id", "project_id", "user_id", "designation", "description", "url", "time_estimation", "time_estimation_type", "approval_status_id", "status_id", "priority_id", "order_number", "ai_id", "client_id", "operating_system", "browser", "selector", "resolution", "deadline", "done_at", "archived_at"];

	protected $touches = ["project", "status"];

	// Cascade the soft deletion to the given child resources
	protected $cascadeDeletes = ['screenshots', 'attachments', 'comments'];

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

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function approvalStatus()
	{
		return $this->belongsTo(BugExportStatus::class, "approval_status_id");
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
		return $this->hasMany(Comment::class)->orderBy('created_at');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function jiraLink()
	{
		return $this->hasOne(JiraBugLink::class);
	}
}
