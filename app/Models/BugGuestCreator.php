<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugGuestCreator extends Model
{
	use HasFactory;

	protected $fillable = ["bug_id", "name", "email"];

	public $timestamps = false;

	public function bug()
	{
		return $this->belongsTo(Bug::class);
	}
}
