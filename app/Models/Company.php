<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
	use HasFactory;

	protected $fillable = ["designation", "image_id"];

	public function projects()
	{
		return $this->hasMany(Project::class);
	}

	public function images()
	{
		return $this->belongsTo(Image::class, "image_id");
	}
}
