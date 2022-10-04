<?php

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

Broadcast::channel('projects.{projectId}', function ($user, $projectId) {
    return $this->authorize('view', [Comment::class, Project::find($projectId)]);
});
