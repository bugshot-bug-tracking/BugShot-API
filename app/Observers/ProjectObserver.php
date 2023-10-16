<?php

namespace App\Observers;

// Models
use App\Models\Action;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Project "created" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function created(Project $project)
    {
		$action = new Action();
		$actionId = $action->getIdByName("project_created");
		if($actionId)
		{
			$project->history()->attach($actionId, ["user_id" => $project->creator ? $project->creator->id : NULL]);
		}
    }

    /**
     * Handle the Project "updated" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function updated(Project $project)
    {
		$action = new Action();
		$actionId = $action->getIdByName("project_updated");
		$project->history()->attach($actionId, ["user_id" => Auth::id()]);
    }

    /**
     * Handle the Project "deleted" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function deleted(Project $project)
    {
        dd("project deleted");
    }

    /**
     * Handle the Project "restored" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function restored(Project $project)
    {
        //
    }

    /**
     * Handle the Project "forceDeleted" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function forceDeleted(Project $project)
    {
        //
    }

	/**
     * Handle the Project "movedToNewGroup" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function movedToNewGroup(Project $project)
    {
        //
    }

	/**
     * Handle the Project "accessTokenGenerated" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function accessTokenGenerated(Project $project)
    {
        //
    }

	/**
     * Handle the Project "accessTokenDeleted" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function accessTokenDeleted(Project $project)
    {
        //
    }
}
