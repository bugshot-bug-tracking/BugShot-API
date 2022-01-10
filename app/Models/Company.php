<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;

/**
 * @OA\Schema()
 */
class Company extends Model
{
	use HasFactory, SoftDeletes, CascadeSoftDeletes;

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
	 * 	description="The company name."
	 * )
	 *
	 * @OA\Property(
	 * 	property="color_hex",
	 * 	type="string",
	 *  maxLength=255,
	 * 	nullable=true,
	 * 	description="The colorcode for the company."
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

	protected $fillable = ["id", "designation", "color_hex"];

	// Cascade the soft deletion to the given child resources
	protected $cascadeDeletes = ['projects', 'invitations', 'image'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user_roles')->withPivot('role_id');
    }

	/**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function projects()
	{
		return $this->hasMany(Project::class)->orderBy('updated_at', 'desc');
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
	public function invitations()
	{
		return $this->morphMany(Invitation::class, "invitable");
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
	public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
