<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
	use HasFactory;

	protected $fillable = ["bug_id", "designation", "url"];

	protected $touches = ["bug"];

	public function bug()
	{
		return $this->belongsTo(Bug::class);
	}
}
