<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $designation
 */
class SubValue extends Model
{
	use HasFactory;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

	protected $fillable = ["designation"];

	public $timestamps = false;

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function settingUserValues()
    {
        return $this->belongsToMany(SettingUserValue::class, 'setting_user_values');
    }
}
