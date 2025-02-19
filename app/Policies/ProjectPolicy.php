<?php

namespace App\Policies;

// Miscellaneous, Helpers, ...
use Illuminate\Auth\Access\HandlesAuthorization;

// Models
use App\Models\Project;
use App\Models\Company;
use App\Models\User;

class ProjectPolicy
{
  use HandlesAuthorization;

  /**
   * Roles:
   * | id | designation
   * |----|----------------------
   * | 1  | Manager
   * | 2  | Team
   * | 3  | Client (e.g. Customer)
   */

  /**
   * Perform pre-authorization checks.
   *
   * @param  \App\Models\User  $user
   * @param  string  $ability
   * @return void|bool
   */
  public function before(User $user, $ability)
  {
    if ($user->isAdministrator()) {
      return true;
    }
  }

  /**
   * Determine whether the user can view any models.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Company  $company
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function viewAny(User $user, Company $company)
  {
    if ($user->isPriviliegated('companies', $company)) {
      return true;
    }

    if ($company->user_id == $user->id) {
      return true;
    }

    $company = $user->companies()->find($company);
    if ($company != NULL) {
      return true;
    }
  }

  /**
   * Determine whether the user can view the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function view(User $user, Project $project)
  {
    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };

    $project = $user->projects()->find($project);
    if ($project == NULL) {
      return false;
    }

    $role = $project->pivot->role_id;
    switch ($role) {
      case 1:
        return true;
        break;
      case 2:
        return true;
        break;
      case 3:
        return true;
        break;

      default:
        return false;
        break;
    }
  }

  /**
   * Determine whether the user can create models.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Company  $company
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function create(User $user, Company $company)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the company
    if ($user->isPriviliegated('companies', $company)) {
      return true;
    };
  }

  /**
   * Determine whether the user can update the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function update(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can delete the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function delete(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can restore the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function restore(User $user, Project $project)
  {
    //
  }

  /**
   * Determine whether the user can permanently delete the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function forceDelete(User $user, Project $project)
  {
    //
  }

  /**
   * Determine whether the user can view the image of the the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function viewImage(User $user, Project $project)
  {
    //
  }

  /**
   * Determine whether the user can view the users of the the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function viewUsers(User $user, Project $project)
  {
    //
  }

  /**
   * Determine whether the user is authorized to update the users role in the given project
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function updateUserRole(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can remove a user from the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function removeUser(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can view the invitations of the the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function viewInvitations(User $user, Project $project)
  {
    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can view the invitations of the the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function invite(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

	/**
   * Determine whether the user can move bugs of the project to a new project
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function moveBugs(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

	/**
   	* Determine whether the user can move a project to a new company
   	*
   	* @param  \App\Models\User  $user
   	* @param  \App\Models\Project  $project
   	* @return \Illuminate\Auth\Access\Response|bool
   	*/
  	public function moveProject(User $user, Project $project)
  	{
  	  	if(!$user->licenseActive()){return false;}

  	  	// Check if user is the manager or owner of the project or above
  	  	if ($user->isPriviliegated('projects', $project)) {
  	  	  	return true;
  	  	};
  	}

  /**
   * Determine whether the user can store an url for the project
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function createUrl(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can view the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function viewUrl(User $user, Project $project)
  {
    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };

    $project = $user->projects()->find($project);
    if ($project == NULL) {
      return false;
    }

    $role = $project->pivot->role_id;
    switch ($role) {
      case 1:
        return true;
        break;
      case 2:
        return true;
        break;
      case 3:
        return true;
        break;

      default:
        return false;
        break;
    }
  }

  /**
   * Determine whether the user can view the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function viewAnyUrls(User $user, Project $project)
  {
    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };

    $project = $user->projects()->find($project);
    if ($project == NULL) {
      return false;
    }

    $role = $project->pivot->role_id;
    switch ($role) {
      case 1:
        return true;
        break;
      case 2:
        return true;
        break;
      case 3:
        return true;
        break;

      default:
        return false;
        break;
    }
  }

  /**
   * Determine whether the user can store an url for the project
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function updateUrl(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can delete the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function deleteUrl(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can view the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function viewAnyApiTokens(User $user, Project $project)
  {
    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can create an api token
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function createApiToken(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can update an api token
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function updateApiToken(User $user, Project $project)
  {
    if(!$user->licenseActive()){return false;}

    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }

  /**
   * Determine whether the user can delete the api token.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function deleteApiToken(User $user, Project $project)
  {
    // Check if user is the manager or owner of the project
    if ($user->isPriviliegated('projects', $project)) {
      return true;
    };
  }
}
