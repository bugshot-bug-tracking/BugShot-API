<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screenshot extends Model
{
	use HasFactory;

	protected $fillable = ["bug_id", "designation", "url", "position_x", "position_y", "web_position_x", "web_position_y"];

	protected $touches = ["bug"];

	public function bug()
	{
		return $this->belongsTo(Bug::class);
	}
}
