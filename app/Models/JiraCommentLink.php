<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JiraCommentLink extends Model
{
	use HasFactory;

	protected $fillable = ["comment_id", "jira_comment_id", "jira_comment_url"];
	protected $touches = ["comment"];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function comment()
	{
		return $this->belongsTo(Comment::class);
	}
}
