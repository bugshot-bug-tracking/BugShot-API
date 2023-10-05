<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @OA\Schema()
 */
class History extends Model
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
	 * 	property='id',
	 * 	type='integer',
	 *  format='int64',
	 * )
	 *
	 * @OA\Property(
	 * 	property='historyable_id',
	 * 	type='string',
	 *  maxLength=255,
	 * 	description='The id of the historyable resource.'
	 * )
	 *
	 * @OA\Property(
	 * 	property='historyable_type',
	 * 	type='string',
	 *  maxLength=255,
	 * 	description='The type of the historyable resource.'
	 * )
	 *
	 * @OA\Property(
	 * 	property='action_id',
	 * 	type='integer',
	 *  format='int64',
	 * 	description='The id of the action that was performed.'
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

	protected $fillable = ['action_id'];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
	public function historyable()
	{
		return $this->morphTo();
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function action()
	{
		return $this->belongsTo(Action::class, 'action_id');
	}
}
