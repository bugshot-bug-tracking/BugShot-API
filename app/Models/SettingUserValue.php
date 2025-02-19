<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class SettingUserValue extends Model
{
	use HasFactory;

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="integer",
	 *  format="int64",
	 * )
	 *
	 * @OA\Property(
	 * 	property="setting_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the setting."
	 * )
	 *
	 * @OA\Property(
	 * 	property="user_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user."
	 * )
	 *
	 * @OA\Property(
	 * 	property="value_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the value."
	 * )
	 *
	 */
	protected $fillable = ["setting_id", "user_id", "value_id"];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setting_user_values';

	public $timestamps = false;

	public function setting()
	{
		return $this->belongsTo(Setting::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function value()
	{
		return $this->belongsTo(Value::class);
	}
}
