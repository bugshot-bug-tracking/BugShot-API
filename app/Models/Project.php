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
	 * 	property="company_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the company to which the object belongs."
	 * )
	 *
	 * @OA\Property(
	 * 	property="image_id",
	 * 	type="string",
	 *  maxLength=255,
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

	protected $fillable = ["id", "designation", "url", "company_id", "image_path", "color_hex", "deleted_at"];

	protected $touches = ['company'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user_roles');
    }

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function statuses()
	{
		return $this->hasMany(Status::class)->where('deleted_at', NULL)->orderBy("order");
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function bugs()
	{
		return $this->hasMany(Bug::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function images()
	{
		return $this->belongsTo(Image::class, "image_id");
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
	public function invitations()
	{
		return $this->morphMany(Invitation::class, "invitable");
	}
}
