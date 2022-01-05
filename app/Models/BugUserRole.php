<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema()
 */
class BugUserRole extends Model
{
	use HasFactory, SoftDeletes;

	/**
	 * @OA\Property(
	 * 	property="bug_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the bug."
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
	 */

	protected $fillable = ["bug_id", "user_id", "role_id"];

	public $timestamps = false;

	public function bug()
	{
		return $this->belongsTo(Bug::class);
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
