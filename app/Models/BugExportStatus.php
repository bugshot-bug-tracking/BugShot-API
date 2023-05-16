<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class BugExportStatus extends Model
{
	use HasFactory;

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="string",
	 *  maxLength=255,
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

	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'exported_bugs_statuses';

	public $timestamps = false;
}
