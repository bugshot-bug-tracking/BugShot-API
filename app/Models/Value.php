<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class Value extends Model
{
	use HasFactory;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

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
	 * )
	 */
	protected $fillable = ["designation"];

	public $timestamps = false;
}
