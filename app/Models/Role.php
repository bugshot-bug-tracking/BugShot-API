<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class Role extends Model
{
	use HasFactory;

	const OWNER = 0;
	const MANAGER = 1;
	const TEAM = 2;
	const CLIENT = 3;

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
	 */

	protected $fillable = ["designation"];

	public $timestamps = false;
}
