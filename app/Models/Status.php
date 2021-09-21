<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
	use HasFactory;

	protected $fillable = ["designation", "project_id"];

	protected $touches = ['project'];

	public function project()
	{
		return $this->belongsTo(Project::class);
	}

	public function bugs()
	{
		return $this->hasMany(Bug::class);
	}
}
