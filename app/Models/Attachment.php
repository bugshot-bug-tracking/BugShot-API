<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
	use HasFactory, SoftDeletes;

	/** @OA\Schema(
     *     schema="Attachment",
     *	   required={"id", "designation", "url", "email", "password"},
	 * 	   @OA\Property(
	 * 	   	   property="id",
	 * 	   	   type="integer",
	 * 	       format="int64",
	 * 	   ),
	 *
	 * @OA\Property(
	 * 	property="bug_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the bug to which the object belongs."
	 * ),
	 *
	 * @OA\Property(
	 * 	property="designation",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The file name."
	 * ),
	 *
	 * @OA\Property(
	 * 	property="url",
	 * 	type="string",
	 * 	description="The lacal path where the file is stored."
	 * ),
	 *
	 * @OA\Property(
	 * 	property="created_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The creation date."
	 * ),
	 *
	 * @OA\Property(
	 * 	property="updated_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The last date when the resource was changed."
	 * ),
	 *
	 * 		@OA\Property(
	 * 			property="deleted_at",
	 * 			type="string",
	 * 		 	format="date-time",
	 * 			description="The deletion date."
	 * 		)
     * ),
     */
	protected $fillable = ["bug_id", "designation", "url"];

	protected $touches = ["bug"];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function bug()
	{
		return $this->belongsTo(Bug::class);
	}
}
