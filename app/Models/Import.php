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
