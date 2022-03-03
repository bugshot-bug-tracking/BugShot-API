<?php

namespace App\Services;
use App\Models\Organization;

class OrganizationService
{
    // Store a newly created organization on the server.
    public function store($request, $user, $id = null)
    {
		$organization = Organization::create([
			"id" => $id,
			"user_id" => $user->id,
			"designation" => $request->designation
		]);

		// Add the organization_id to the user
		$user->update([
			'organization_id' => $organization->id
		]);

        return $organization;
    }
}