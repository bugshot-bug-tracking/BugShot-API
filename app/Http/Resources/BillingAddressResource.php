<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Organization;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Relations\Relation;

class BillingAddressResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
        // Check the model type of the billing address
		$class = Relation::getMorphedModel($this->billing_addressable_type);
        switch ($class) {
            case Organization::class:
                $resource = new OrganizationResource(Organization::find($this->billing_addressable_id));
                break;

            case User::class:
                $resource = new UserResource(User::find($this->billing_addressable_id));
                break;
        }

        $billingAddress = array(
			'id' => $this->id,
			'type' => 'BillingAddress',
			'attributes' => [
                'billing_addressable' => $resource,
				'street' => $this->street,
				'housenumber' => $this->housenumber,
				'state' => $this->state,
				'city' => $this->city,
				'zip' => $this->zip,
				'country' => $this->country,
				'tax_id' => $this->tax_id,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at
			]
		);

        return $billingAddress;
	}
}


