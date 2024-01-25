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
class Import extends Model
{
	use HasFactory;

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
	 * 	property="status_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the import_status."
	 * )
	 *
	 * @OA\Property(
	 * 	property="imported_by",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user that created the import."
	 * )
	 *
	 * @OA\Property(
	 * 	property="source",
	 * 	type="array",
	 * 	description="Info about the source."
	 * )
	 *
	 * @OA\Property(
	 * 	property="target",
	 * 	type="array",
	 * 	description="Info about the target."
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

	protected $fillable = ["id", "status_id", "imported_by", "source", "target"];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'source' => 'array',
		'target' => 'array'
    ];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function importer()
	{
		return $this->belongsTo(User::class, 'imported_by');
	}

 	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function status()
	{
		return $this->belongsTo(ImportStatus::class, "status_id");
	}
}
