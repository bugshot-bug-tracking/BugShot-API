<?php

namespace App\Models;

use App\Traits\HasUniqueName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @OA\Schema()
 */
class Action extends Model
{
	use HasFactory, HasUniqueName;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

	protected $fillable = ['designation'];

    /**
     * Get all of the projects that made use of this action.
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'historyable', "history")->withTimestamps();
    }
}
