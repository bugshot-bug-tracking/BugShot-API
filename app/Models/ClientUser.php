<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class ClientUser extends Model
{
	use HasFactory;

	/**
	 * @OA\Property(
	 * 	property="client_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the client."
	 * )
	 *
	 * @OA\Property(
	 * 	property="user_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user."
	 * )
	 *
	 * @OA\Property(
	 * 	property="last_active_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The last time the user was active."
	 * )
	 * 
	 * @OA\Property(
	 * 	property="login_counter",
	 * 	type="integer",
	 *  format="int64",
	 * )
	 *
	 * @OA\Property(
	 * 	property="deleted_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The deletion date."
	 * )
	 */

	protected $fillable = ["client_id", "user_id", "last_active_at", "login_counter", "deleted_at"];

	public $timestamps = true;

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
