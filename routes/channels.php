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

    // test if user is in proj
    $project = Project::findOrFail($bug->project->id);
    $projectUsers = $project->users->where('id', '=', $user->id);
    if (!$projectUsers->empty() && $user->id === $projectUsers->first()->id) {
        return true;
    }

    return false;
});

Broadcast::channel('project.{projectId}', function ($user, $projectId) {
    // test if user is in proj
    $project = Project::findOrFail($projectId);
    $projectUsers = $project->users->where('id', '=', $user->id);
    if (!$projectUsers->empty() && $user->id === $projectUsers->first()->id) {
        return true;
    }
    return false;
});

Broadcast::channel('project.{projectId}.admin', function ($user, $projectId) {
    // test if user is creator / manager of proj
    return false;
});

Broadcast::channel('company.{companyId}', function ($user, $companyId) {
    // test if user is in comp
    $company = Company::findOrFail($companyId);
    $companyUsers = $company->users->where('id', '=', $user->id);
    if (!$companyUsers->empty() && $user->id === $companyUsers->first()->id) {
        return true;
    }
    return false;
});

Broadcast::channel('company.{companyId}.admin', function ($user, $companyId) {
    // test if user is creator / manager of company
    return true;
});

Broadcast::channel('organization.{organizationId}', function ($user, $organizationId) {
    // test if user is in org
    $org = Organization::findOrFail($organizationId);
    $orgUsers = $org->users->where('id', '=', $user->id);
    if (!$orgUsers->empty() && $user->id === $orgUsers->first()->id) {
        return true;
    }
    return false;
});

Broadcast::channel('organization.{organizationId}.admin', function ($user, $organizationId) {
    // test if user is creator / manager of company
    return true;
});

Broadcast::channel('user.{userId}', function ($user) {
    // test if user is user?
    return true;
});
