<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class Status extends Model
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
	 * 	property="designation",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The status name."
	 * )
	 * 
	 * @OA\Property(
	 * 	property="order_number",
	 * 	type="integer",
	 *  format="int32",
	 * 	description="The status name."
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

	protected $fillable = ["id", "designation", "order_number", "project_id", "deleted_at"];

	protected $touches = ['project'];

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
	public function bugs()
	{
		return $this->hasMany(Bug::class)->orderBy("order_number");
	}
}
