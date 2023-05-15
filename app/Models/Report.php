<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Laravel\Scout\Searchable;

/**
 * @OA\Schema()
 */
class Report extends Model
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
	 * 	property="export_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the export to which the object belongs."
	 * )
	 *
	 * @OA\Property(
	 * 	property="generated_by",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user that generated the report."
	 * )
	 *
	 * @OA\Property(
	 * 	property="url",
	 * 	type="string",
	 * 	description="The url where the report is stored"
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

	protected $fillable = ["id", "export_id", "generated_by", "url"];

	// protected $touches = [''];

	// Cascade the soft deletion to the given child resources
	protected $cascadeDeletes = ['bug_exports', 'reports'];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function export()
	{
		return $this->belongsTo(Export::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function generator()
	{
		return $this->belongsTo(User::class, 'generated_by');
	}
}
