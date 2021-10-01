<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
	use HasFactory;

	protected $fillable = ["sender_id",	"target_id", "comnpany_id",	"project_id", "comnpany_role_id", "project_role_id", "status_id"];


	public function sender()
	{
		return $this->belongsTo(User::class, "sender_id");
	}

	public function target()
	{
		return $this->belongsTo(User::class, "target_id");
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function project()
	{
		return $this->belongsTo(Project::class);
	}

	public function companyRole()
	{
		return $this->belongsTo(Role::class, "company_role_id");
	}

	public function projectRole()
	{
		return $this->belongsTo(Role::class, "project_role_id");
	}

	public function status()
	{
		return $this->belongsTo(InvitationStatus::class, "status_id");
	}
}
