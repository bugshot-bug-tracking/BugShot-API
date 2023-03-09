<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema()
 */
class Marker extends Model
{
	use HasFactory, SoftDeletes;

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
	 * 	property="screenshot_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the screenshot to which the object belongs."
	 * )
	 *
	 * @OA\Property(
	 * 	property="position_x",
	 * 	type="number",
	 *  format="float",
	 * 	description="The x position of the marker."
	 * )
     *
	 * @OA\Property(
	 * 	property="position_y",
	 * 	type="number",
	 *  format="float",
	 * 	description="The y position of the marker."
	 * )
     *
	 * @OA\Property(
	 * 	property="web_position_x",
	 * 	type="number",
	 *  format="float",
	 * 	description="The x position in web of the marker."
	 * )
     *
	 * @OA\Property(
	 * 	property="web_position_y",
	 * 	type="number",
	 *  format="float",
	 * 	description="The y position in web of the marker."
	 * )
     *
	 * @OA\Property(
	 * 	property="target_x",
	 * 	type="number",
	 *  format="float",
	 * 	description="The x position of the element the marker sits on."
	 * )
     *
	 * @OA\Property(
	 * 	property="target_y",
	 * 	type="number",
	 *  format="float",
	 * 	description="The y position of the element the marker sits on."
	 * )
     *
	 * @OA\Property(
	 * 	property="target_height",
	 * 	type="number",
	 *  format="float",
	 * 	description="The height of the element the marker sits on."
	 * )
     *
	 * @OA\Property(
	 * 	property="target_width",
	 * 	type="number",
	 *  format="float",
	 * 	description="The width of the element the marker sits on."
	 * )
     *
	 * @OA\Property(
	 * 	property="scroll_x",
	 * 	type="number",
	 *  format="float",
	 * 	description="The x scroll position of the marker."
	 * )
     *
	 * @OA\Property(
	 * 	property="scroll_y",
	 * 	type="number",
	 *  format="float",
	 * 	description="The y scroll position of the marker."
	 * )
     *
	 * @OA\Property(
	 * 	property="screenshot_height",
	 * 	type="number",
	 *  format="float",
	 * 	description="The height of the screenshot."
	 * )
     *
	 * @OA\Property(
	 * 	property="screenshot_width",
	 * 	type="number",
	 *  format="float",
	 * 	description="The width of the screenshot."
	 * )
	 *
	 * @OA\Property(
	 * 	property="device_pixel_ratio",
	 * 	type="float",
	 *  nullable=true,
	 * 	description="ratio of the devices pixels."
	 * )
     *
	 * @OA\Property(
	 * 	property="target_full_selector",
	 * 	type="string",
	 * 	description="The full selector name of the element the marker sits on."
	 * )
     *
	 * @OA\Property(
	 * 	property="target_short_selector",
	 * 	type="string",
	 * 	description="The short selector name of the element the marker sits on"
	 * )
     *
	 * @OA\Property(
	 * 	property="target_html",
	 * 	type="string",
	 * 	description="The html of the element the marker sits on"
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
	 */
	protected $fillable = [
        "id",
        "screenshot_id",
        "position_x",
        "position_y",
        "web_position_x",
        "web_position_y",
        "target_x",
        "target_y",
        "target_height",
        "target_width",
        "scroll_x",
        "scroll_y",
        "screenshot_height",
        "screenshot_width",
		"device_pixel_ratio",
        "target_full_selector",
        "target_short_selector",
        "target_html"
    ];

	protected $touches = ['screenshot'];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function screenshot()
	{
		return $this->belongsTo(Screenshot::class);
	}
}
