<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;

/**
 * @OA\Schema()
 */
class Screenshot extends Model
{
	use HasFactory, SoftDeletes, CascadeSoftDeletes;

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="integer",
	 *  format="int64",
	 * )
	 *
	 * @OA\Property(
	 * 	property="bug_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the but to which the object belongs."
	 * )
	 *
	 * @OA\Property(
	 * 	property="designation",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The screenshot name."
	 * )
	 *
	 * @OA\Property(
	 * 	property="url",
	 * 	type="string",
	 * 	description="The lacal path where the file is stored. "
	 * )
	 *
	 * @OA\Property(
	 * 	property="position_x",
	 * 	type="integer",
	 *  format="int32",
	 *  nullable=true,
	 * 	description="The x coordinate value for the screenshot marker."
	 * )
	 *
	 * @OA\Property(
	 * 	property="position_y",
	 * 	type="integer",
	 *  format="int32",
	 *  nullable=true,
	 * 	description="The y coordinate value for the screenshot marker."
	 * )
	 *
	 * @OA\Property(
	 * 	property="web_position_x",
	 * 	type="integer",
	 *  format="int32",
	 *  nullable=true,
	 * 	description="The x coordinate value of the marker relative to web page top."
	 * )
	 *
	 * @OA\Property(
	 * 	property="web_position_y",
	 * 	type="integer",
	 *  format="int32",
	 *  nullable=true,
	 * 	description="The y coordinate value of the marker relative to web page top."
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

	protected $fillable = ["bug_id", "url", "position_x", "position_y", "web_position_x", "web_position_y"];

	protected $touches = ["bug"];

	// Cascade the soft deletion to the given child resources
	protected $cascadeDeletes = ['markers'];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function bug()
	{
		return $this->belongsTo(Bug::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function markers()
	{
		return $this->hasMany(Marker::class);
	}
}
