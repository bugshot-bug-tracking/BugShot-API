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
     * Handle the Project "projectCreated" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function projectCreated(Project $project)
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
     * Handle the Project "projectUpdated" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function projectUpdated(Project $project)
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
     * Handle the Project "projectDeleted" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function projectDeleted(Project $project)
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
     * Handle the Project "projectRestored" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function projectRestored(Project $project)
    {
        //
    }

    /**
     * Handle the Project "projectForceDeleted" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function projectForceDeleted(Project $project)
    {
        //
    }

	/**
     * Handle the Project "projectMovedToNewGroup" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function projectMovedToNewGroup(Project $project)
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
     * Handle the Project "projectAccessTokenGenerated" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function projectAccessTokenGenerated(Project $project)
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
     * Handle the Project "projectAccessTokenDeleted" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function projectAccessTokenDeleted(Project $project)
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
