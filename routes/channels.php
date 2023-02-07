<?php

use App\Models\Bug;
use App\Models\Comment;
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

Broadcast::channel('comments.{commentId}', function ($user, $commentId) {
	$comment = Comment::findOrFail($commentId);
    return $this->authorize('view', [Comment::class, $comment->bug->project]);
});

Broadcast::channel('bugs.{bugId}', function ($user, $bugId) {
	//$bug = Bug::findOrFail($bugId);
    //test if user is in proj
    // return $user->id === Bug::findOrFail($bugId);
    return true;
});
