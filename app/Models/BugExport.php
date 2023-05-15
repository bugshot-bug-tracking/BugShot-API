<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
 */
class BugExport extends Model
{
	use HasFactory;

	/**
	 * @OA\Property(
	 * 	property="export_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the export."
	 * )
	 *
	 * @OA\Property(
	 * 	property="bug_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the bug."
	 * )
	 *
	 * @OA\Property(
	 * 	property="status_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the bug_export_status."
	 * )
	 *
	 * @OA\Property(
	 * 	property="time_estimation",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The time estimation as unix timestamp."
	 * )
	 *
	 * @OA\Property(
	 * 	property="evaluated_by",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user that evaluated the bug export to either approved or declined."
	 * )
	 *
	 */

	protected $fillable = ["export_id", "bug_id", "status_id", "time_estimation", "evaluated_by"];

	public $timestamps = false;

	public function export()
	{
		return $this->belongsTo(Export::class);
	}

	public function bug()
	{
		return $this->belongsTo(Bug::class);
	}

	public function status()
	{
		return $this->belongsTo(BugExportStatus::class, "status_id");
	}

	public function evaluator()
	{
		return $this->belongsTo(User::class, "evaluated_by");
	}
}
