<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bug extends Model
{
	use HasFactory;

	protected $fillable = ["project_id", "user_id", "designation", "description", "url", "status_id", "priority_id", "operating_system", "browser", "selector", "resolution", "deadline"];
}
