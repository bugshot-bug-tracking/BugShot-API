<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Project;
use App\Models\ApiToken;

class ApiTokenService
{
    public function getModelToApiToken(string $apiToken)
	{
		// Get token in DB
		$apitoken_entry = ApiToken::where([
			['token', '=', $apiToken],
		])->first();

		// Get model from type
		$model = null;
		if ($apitoken_entry != NULL) {
			$class = Relation::getMorphedModel($apitoken_entry->api_tokenable_type);
			switch ($class) {
				case Project::class:
					$model = Project::find($apitoken_entry->api_tokenable_id);
					break;
				
				default:
					$model = null;
					break;
			}
		}

		return $model;
	}
}