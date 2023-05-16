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
class Export extends Model
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
	 * 	property="exported_by",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user that created the export."
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

	protected $fillable = ["id", "exported_by"];

	// protected $touches = [''];

	// Cascade the soft deletion to the given child resources
	protected $cascadeDeletes = ['bug_exports', 'reports'];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function project()
	{
		return $this->belongsTo(Project::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function exporter()
	{
		return $this->belongsTo(User::class, 'exported_by');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bugs()
    {
        return $this->belongsToMany(Bug::class, 'bug_exports')->withPivot('evaluated_by');
    }

	/**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function reports()
	{
		return $this->hasMany(Report::class)->orderBy('created_at', 'desc');
	}
}
