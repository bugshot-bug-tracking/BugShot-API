<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bug extends Model
{
	use HasFactory;

	protected $fillable = ["project_id", "user_id", "designation", "description", "url", "status_id", "priority_id", "operating_system", "browser", "selector", "resolution", "deadline"];

	protected $touches = ["project", "status"];

	public function project()
	{
		return $this->belongsTo(Project::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

	public function priority()
	{
		return $this->belongsTo(Priority::class);
	}

	public function screenshots()
	{
		return $this->hasMany(Screenshot::class);
	}

	public function attachments()
	{
		return $this->hasMany(Attachment::class);
	}

	public function comments()
	{
		return $this->hasMany(Comment::class);
	}
}
