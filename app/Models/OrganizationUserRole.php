<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class OrganizationUserRole extends Model
{
	use HasFactory;

	/**
	 * @OA\Property(
	 * 	property="organization_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the organization."
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

	protected $fillable = ["organization_id", "user_id", "role_id"];

	public $timestamps = false;

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function organization()
	{
		return $this->belongsTo(Organization::class);
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
	public function role()
	{
		return $this->belongsTo(Role::class);
	}
}
