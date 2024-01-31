<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class ImportStatus extends Model
{
	use HasFactory;

	const PENDING = 1;
	const IMPORTED = 2;
	const IMPORT_FAILED = 3;

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
	 * 	description="The status name."
	 * )
	 *
	 */
	protected $fillable = ["designation"];

	public $timestamps = false;
}
