<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
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

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function settingUserValues()
    {
        return $this->belongsToMany(SettingUserValue::class, 'setting_user_value_sub_values');
    }
}
