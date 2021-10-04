<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class Project extends Model
{
	use HasFactory;

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="integer",
	 *  format="int64",
	 * )
	 *
	 * @OA\Property(
	 * 	property="company_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the company to which the object belongs."
	 * )
	 *
	 * @OA\Property(
	 * 	property="image_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	nullable=true,
	 * 	description="The id of the image that belongs to the project."
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
	 * 	property="url",
	 * 	type="string",
	 * 	description="The url of the project. (Validated)"
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

	protected $fillable = ["designation", "url", "company_id", "image_id"];

	protected $touches = ['company'];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function statuses()
	{
		return $this->hasMany(Status::class);
	}

	public function bugs()
	{
		return $this->hasMany(Bug::class);
	}

	public function images()
	{
		return $this->belongsTo(Image::class, "image_id");
	}

	public function invitations()
	{
		return $this->morphMany(Invitation::class, "invitable");
	}
}
