<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Organization;
use App\Models\Company;
use App\Models\Project;
use App\Models\Bug;

class InvitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Check the model type of the invitation
        switch ($this->invitable_type) {
            case 'organization':
                $resource = new OrganizationResource($this->invitable);
                break;

            case 'company':
                $resource = new CompanyResource($this->invitable);
                break;

            case 'project':
                $resource = new ProjectResource($this->invitable);
                break;

            case 'bug':
                $resource = new BugResource($this->invitable);
                break;
        }

        $invitation = array(
			"id" => $this->id,
			"type" => "Invitation",
			"attributes" => [
				"sender" => new UserResource($this->sender),
				"target_email" => $this->target_email,
                "invitable" => $resource,
                "role" => new RoleResource($this->role),
                "status" => new InvitationStatusResource($this->status),
                "created_at" => $this->created_at,
                "updated_at" => $this->updated_at
			]
		);

        return $invitation;
    }
}
