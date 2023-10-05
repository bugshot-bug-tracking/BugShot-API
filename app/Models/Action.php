<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @OA\Schema()
 */
class Action extends Model
{
	use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="integer",
	 *  format="int64",
	 * )
	 *
	 * @OA\Property(
	 * 	property="designation",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The role name."
	 * )
	 *
	 * @OA\Property(
	 * 	property='created_at',
	 * 	type='string',
	 *  format='date-time',
	 * 	description='The creation date.'
	 * )
	 *
	 * @OA\Property(
	 * 	property='updated_at',
	 * 	type='string',
	 *  format='date-time',
	 * 	description='The last date when the resource was changed.'
	 * )
	 *
	 * @OA\Property(
	 * 	property='deleted_at',
	 * 	type='string',
	 *  format='date-time',
	 * 	description='The deletion date.'
	 * )
	 *
	 */

	protected $fillable = ['designation'];
}
