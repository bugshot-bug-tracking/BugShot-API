<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;

/**
 * @OA\Schema()
 */
class Organization extends Model
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
	 * 	property="user_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user that created the organization."
	 * )
	 *
	 * @OA\Property(
	 * 	property="designation",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The organization name."
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

	protected $fillable = ["id", "user_id", "designation"];

	// Cascade the soft deletion to the given child resources
	protected $cascadeDeletes = ['organizations', 'invitations', 'image'];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function creator()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user_roles')->withPivot('role_id');
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
	public function billingAddress()
	{
		return $this->morphOne(BillingAddress::class, "billing_addressable");
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function companies()
	{
		return $this->hasMany(Company::class)->orderBy('updated_at', 'desc');
	}

	/**
     * Get all of the projects for the organization.
     */
    public function projects()
    {
        return $this->hasManyThrough(Project::class, Company::class);
    }
}
