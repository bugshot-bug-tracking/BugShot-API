<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class LoadingTime extends Model
{
	use HasFactory;


	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="integer",
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
	 * 	property="client_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the client that created the object."
	 * )
	 *
	 * @OA\Property(
	 * 	property="url",
	 * 	type="string",
	 * 	description="The url of the loaded site"
	 * )
	 *
	 * @OA\Property(
	 * 	property="loading_duration_raw",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The duration in ms of the loading time of the page itself."
	 * )
	 *
	 * @OA\Property(
	 * 	property="loading_duration_fetched",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The duration in ms of the loading time of the page including the fetched content."
	 * )
	 *
	 *
	 */

	protected $fillable = ["user_id", "client_id", "url", "loading_duration_raw", "loading_duration_fetched"];

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
	public function client()
	{
		return $this->belongsTo(Client::class, 'client_id');
	}
}
