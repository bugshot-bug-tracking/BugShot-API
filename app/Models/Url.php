<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema()
 */
class Url extends Model
{
	use HasFactory, SoftDeletes;

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
	 * 	property="url",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The url."
	 * )
	 *
	 * @OA\Property(
	 * 	property="primary",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="Send true if the url is supposed to be the primary url for this project. Else send false"
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
	 */
	protected $fillable = ["id", "url", "primary"];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
	public function urlable()
	{
		return $this->morphTo();
	}
}
