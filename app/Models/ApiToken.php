<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema()
 */
class ApiToken extends Model
{
	use HasFactory, SoftDeletes;

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="integer",
	 *  format="int64",
	 * )
	 *
	 * @OA\Property(
	 * 	property="api_tokenable_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the corresponding model of this token."
	 * )
	 *
	 * @OA\Property(
	 * 	property="api_tokenable_type",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The type of the corresponding model of this token."
	 * )
	 *
	 * @OA\Property(
	 * 	property="token",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The token string."
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

	protected $fillable = ["token"];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
	public function apiTokenable()
	{
		return $this->morphTo();
	}
}
