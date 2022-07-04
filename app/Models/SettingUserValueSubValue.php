<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class SettingUserValueSubValue extends Model
{
	use HasFactory;

	/**
	 * @OA\Property(
	 * 	property="setting_user_value_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the setting-user-value pivot model."
	 * )
	 *
	 * @OA\Property(
	 * 	property="sub_value_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the sub value."
	 * )
	 *
	 */

	protected $fillable = ["setting_user_value_id", "sub_value_id"];

	public $timestamps = false;

	public function settingUserValue()
	{
		return $this->belongsTo(SettingUserValue::class);
	}

	public function subValue()
	{
		return $this->belongsTo(SubValue::class);
	}
}
