<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JiraBugLink extends Model
{
	use HasFactory;

	protected $fillable = ["project_link_id", "bug_id", "issue_id", "issue_key", "issue_url"];

	protected $touches = ["bug"];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function bug()
	{
		return $this->belongsTo(Bug::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function projectLink()
	{
		return $this->belongsTo(JiraProjectLink::class);
	}
}
