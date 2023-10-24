<?php

namespace App\Observers;

// Models
use App\Models\Action;
use App\Models\Project;

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
			$project->history()->attach(
				$actionId,
				[
					"user_id" => $project->creator ? $project->creator->id : NULL,
					"args" => json_encode([
						$project->designation,
						$project->creator->fullName()
						]
					)
				]
			);
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
		$dirtyAttributeMessage = $this->buildDirtyAttributesMessage($project);

		$action = new Action();
		$actionId = $action->getIdByName("project_updated");
		if($actionId)
		{
			$project->history()->attach(
				$actionId,
				[
					"user_id" => $project->creator ? $project->creator->id : NULL,
					"args" => json_encode([
							$project->designation,
							$project->creator->fullName(),
							$dirtyAttributeMessage
						]
					)
				]
			);
		}
    }

    /**
     * Handle the Project "deleted" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function deleted(Project $project)
    {
		$action = new Action();
		$actionId = $action->getIdByName("project_deleted");
		if($actionId)
		{
			$project->history()->attach(
				$actionId,
				[
					"user_id" => $project->creator ? $project->creator->id : NULL,
					"args" => json_encode([
							$project->designation,
							$project->creator->fullName()
						]
					)
				]
			);
		}
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
		$dirtyAttributeMessage = $this->buildDirtyAttributesMessage($project);

		$action = new Action();
		$actionId = $action->getIdByName("project_moved_to_new_group");
		if($actionId)
		{
			$project->history()->attach(
				$actionId,
				[
					"user_id" => $project->creator ? $project->creator->id : NULL,
					"args" => json_encode([
							$project->designation,
							$project->creator->fullName(),
							$dirtyAttributeMessage
						]
					)
				]
			);
		}
    }

	/**
     * Handle the Project "accessTokenGenerated" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function accessTokenGenerated(Project $project)
    {
		$action = new Action();
		$actionId = $action->getIdByName("project_access_token_generated");
		if($actionId)
		{
			$project->history()->attach(
				$actionId,
				[
					"user_id" => $project->creator ? $project->creator->id : NULL,
					"args" => json_encode([
							$project->designation,
							$project->creator->fullName()
						]
					)
				]
			);
		}
    }

	/**
     * Handle the Project "accessTokenDeleted" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function accessTokenDeleted(Project $project)
    {
		$action = new Action();
		$actionId = $action->getIdByName("project_access_token_deleted");
		if($actionId)
		{
			$project->history()->attach(
				$actionId,
				[
					"user_id" => $project->creator ? $project->creator->id : NULL,
					"args" => json_encode([
							$project->designation,
							$project->creator->fullName()
						]
					)
				]
			);
		}
    }

	private function buildDirtyAttributesMessage($resource)
	{
		$dirtyAttributes = $resource->getDirty();
		unset($dirtyAttributes['updated_at']);
		$dirtyAttributeMessage = "";
		foreach($dirtyAttributes as $dirtyAttribute => $dirtyValue)
		{
			$newValue = $dirtyValue;
			$oldValue = $resource->getOriginal($dirtyAttribute);
			if($dirtyAttributeMessage == "")
			{
				$dirtyAttributeMessage .= "$dirtyAttribute: '$oldValue' => '$newValue'";
			} else
			{
				$dirtyAttributeMessage .= ", $dirtyAttribute: '$oldValue' => '$newValue'";
			}
		}

		return $dirtyAttributeMessage;
	}
}
