<?php

use App\Models\Bug;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Organization;
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

//TEST IF ALLOWED TO VIEW CERTAIN RESOURCE -> inside resource as part of users / creator || manager / creator on higher level

Broadcast::channel('test', function () {
    return true;
});

Broadcast::channel('bug.{bugId}', function ($user, $bugId) {

    // test if user is assigned
    $bug = Bug::findOrFail($bugId);
    $bugUsers = $bug->users->where('id', '=', $user->id);
    if (!$bugUsers->empty() && $user->id === $bugUsers->first()->id) {
        return true;
    }

    return $user->can('view', [Bug::class, $bug->project]);
});

Broadcast::channel('project.{projectId}', function ($user, $projectId) {
    $project = Project::findOrFail($projectId);
    return $user->can('view', [Project::class, $project]);
});

Broadcast::channel('company.{companyId}', function ($user, $companyId) {
    $company = Company::findOrFail($companyId);
    return $user->can('view', [Company::class, $company]);
});

Broadcast::channel('organization.{organizationId}', function ($user, $organizationId) {
    $org = Organization::findOrFail($organizationId);
    return $user->can('view', [Organization::class, $org]);
});


//Admin routes for Creator / Managers only

Broadcast::channel('project.{projectId}.admin', function ($user, $projectId) {
    $project = Project::findOrFail($projectId);
    return $user->isPriviliegated('projects', $project);
});

Broadcast::channel('company.{companyId}.admin', function ($user, $companyId) {
    $company = Company::findOrFail($companyId);
    return $user->isPriviliegated('companies', $company);
});

Broadcast::channel('organization.{organizationId}.admin', function ($user, $organizationId) {
    $org = Organization::findOrFail($organizationId);
    return $user->isPriviliegated('organizations', $org);
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return $user->id == $userId;
});
