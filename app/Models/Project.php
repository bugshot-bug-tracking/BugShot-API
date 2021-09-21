<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
	use HasFactory;

	protected $fillable = ["designation", "url", "company_id", "image_id"];

	protected $touches = ['company'];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function statuses()
	{
		return $this->hasMany(Status::class);
	}

	public function bugs()
	{
		return $this->hasMany(Bug::class);
	}

	public function images()
	{
		return $this->belongsTo(Image::class, "image_id");
	}
}
