<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class ProjectUserRole extends Model
{
	use HasFactory;

	/**
	 * @OA\Property(
	 * 	property="project_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the project."
	 * )
	 *
	 * @OA\Property(
	 * 	property="user_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user."
	 * )
	 *
	 * @OA\Property(
	 * 	property="role_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the role."
	 * )
	 *
	 * @OA\Property(
	 * 	property="is_favorite",
	 * 	type="bool",
	 * 	description="Marks the project as favorite."
	 * )
	 *
	 */

	protected $fillable = ["project_id", "user_id", "role_id", "is_favorite"];

	public $timestamps = false;

	public function project()
	{
		return $this->belongsTo(Project::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function role()
	{
		return $this->belongsTo(Role::class);
	}
}
