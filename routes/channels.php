<?php

use App\Models\Bug;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('test', function () {
    return true;
});

// Broadcast::channel('comments.{commentId}', function ($user, $commentId) {
// 	$comment = Comment::findOrFail($commentId);
//     return $this->authorize('view', [Comment::class, $comment->bug->project]);
// });

Broadcast::channel('bug.{bugId}', function ($user, $bugId) {
    
    // test if user is in proj // assigned
	// $bug = Bug::findOrFail($bugId);
    // $project = Project::findOrFail($bug->project->id);
    // $bugUsers = $bug->users->where('id', '=', $user->id);
    // $projectUsers = $project->users->where('id', '=', $user->id);

    // if(!$bugUsers->empty() && $user->id === $bugUsers->first()->id){return true;}
    // if(!$projectUsers->empty() && $user->id === $projectUsers->first()->id){return true;}
    // return false;
    return true;
});

Broadcast::channel('project.{projectId}', function ($user, $projectId) {
    // test if user is in proj
    return true;
});

Broadcast::channel('company.{companyId}', function ($user, $companyId) {
    // test if user is in comp
    return true;
});

Broadcast::channel('company.{companyId}.admin', function ($user, $companyId) {
    // test if user is creator / manager of company
    return true;
});

Broadcast::channel('organization.{organizationId}', function ($user, $organizationId) {
    // test if user is in org
    return true;
});

Broadcast::channel('organization.{organizationId}.admin', function ($user, $organizationId) {
    // test if user is creator / manager of company
    return true;
});

Broadcast::channel('user.{userId}', function ($user) {
    // test if user is user?
    return true;
});