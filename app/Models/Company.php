<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Laravel\Scout\Searchable;

/**
 * @OA\Schema()
 */
class Company extends Model
{
	use HasFactory, Searchable, SoftDeletes, CascadeSoftDeletes;

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
	 * Get the indexable data array for the model.
	 *
	 * @return array
	 */
	public function toSearchableArray()
	{
		return [
			'id' => $this->id,
			'designation' => $this->designation
		];
	}

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="string",
	 *  maxLength=255,
	 * )
	 *
	 * @OA\Property(
	 * 	property="user_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user that created the object."
	 * )
	 *
	 * @OA\Property(
	 * 	property="organization_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the organization to which the object belongs."
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

	protected $fillable = ["id", "user_id", "designation", "organization_id", "color_hex"];

	// Cascade the soft deletion to the given child resources
	protected $cascadeDeletes = ['projects', 'invitations', 'image'];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function creator()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function organization()
	{
		return $this->belongsTo(Organization::class);
	}

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
