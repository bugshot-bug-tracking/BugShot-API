<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
	use HasFactory;

	protected $fillable = ["sender_id",	"target_id", "role_id", "status_id"];


	public function invitable()
	{
		return $this->morphTo();
	}

	public function sender()
	{
		return $this->belongsTo(User::class, "sender_id");
	}

	public function target()
	{
		return $this->belongsTo(User::class, "target_id");
	}

	public function role()
	{
		return $this->belongsTo(Role::class, "role_id");
	}

	public function status()
	{
		return $this->belongsTo(InvitationStatus::class, "status_id");
	}
}
