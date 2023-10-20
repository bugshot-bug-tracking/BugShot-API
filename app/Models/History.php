<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @OA\Schema()
 */
class History extends Model
{
	use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'history';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

	protected $fillable = ['action_id', 'args'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'args' => 'array',
    ];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
	public function historyable()
	{
		return $this->morphTo();
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function action()
	{
		return $this->belongsTo(Action::class, 'action_id');
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function agent()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
