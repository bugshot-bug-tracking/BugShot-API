<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class JiraProjectLink extends Model
{
	use HasFactory;

	/**
	 * The "type" of the auto-incrementing ID.
	 *
	 * @var string
	 */
	protected $keyType = 'integer';

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="integer",
	 *  format="int64",
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
	 * 	property="token_type",
	 * 	type="string",
	 * 	description="Jira token type ex. Bearer"
	 * )
	 *
	 * @OA\Property(
	 * 	property="access_token",
	 * 	type="string",
	 * 	description="Jira bearer token"
	 * )
	 *
	 * @OA\Property(
	 * 	property="refresh_token",
	 * 	type="string",
	 * 	description="Jira refresh token"
	 * )
	 *
	 * @OA\Property(
	 * 	property="expires_in",
	 * 	type="integer",
	 * 	description="Jira access token validity period"
	 * )
	 *
	 * @OA\Property(
	 * 	property="scope",
	 * 	type="string",
	 * 	description="Jira token scopes"
	 * )
	 *
	 * @OA\Property(
	 * 	property="jira_project_id",
	 * 	type="string",
	 * 	description="The id of the project from Jira's side"
	 * )
	 *
	 * @OA\Property(
	 * 	property="sync_bugs",
	 * 	type="string",
	 * 	description="Set if the bugs from BugShot are synced to Jira"
	 * )
	 *
	 * @OA\Property(
	 * 	property="sync_comments",
	 * 	type="string",
	 * 	description="Set if the bugs from BugShot that are synced to Jira have their soments also sent to Jira"
	 * )
	 *
	 * @OA\Property(
	 * 	property="update_status",
	 * 	type="string",
	 * 	description="Set if when a bug is marked as done in Jira the same thing happens in BugShot"
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

	protected $fillable = ["project_id", "token_type", "access_token", "refresh_token", "expires_in", "expires_at", "scope", "site_id", "site_name", "site_url", "jira_project_id", "jira_project_name", "jira_project_key", "sync_bugs_to_jira", "sync_bugs_from_jira", "sync_comments_to_jira", "sync_comments_from_jira", "update_status_to_jira", "update_status_from_jira"];

	protected $touches = ["project"];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function project()
	{
		return $this->belongsTo(Project::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function bugLinks()
	{
		return $this->hasMany(JiraBugLink::class);
	}
}
