<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;
use App\Models\OrganizationUserRole;

class UserResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$user = array(
			'id' => $this->id,
			'type' => 'User',
			'attributes' => [
				'first_name' => $this->first_name,
				'last_name' => $this->last_name,
				'email' => $this->email,
				'trial_end_date' => $this->trial_end_date,
			]
		);

		$header = $request->header();

		// Check if the response should contain the respective subscription-items of the user
		if(array_key_exists('include-subscriptions', $header) && $header['include-subscriptions'][0] == "true") {
			if(Auth::user()->id == $this->id || Auth::user()->isAdministrator()) {
				$organizationUserRoles = OrganizationUserRole::where("user_id", $this->id)->whereNot("subscription_item_id", NULL)->get()->unique(["subscription_item_id"]);
				$subscriptionItems = array();

				foreach($organizationUserRoles as $organizationUserRole) {
					$subscriptionItem = SubscriptionItem::where("stripe_id", $organizationUserRole->subscription_item_id)->first();
					// $subscriptionItems->push($subscriptionItem);

					array_push($subscriptionItems, [
						"subscription" => new SubscriptionItemResource($subscriptionItem),
						"assigned_on" => $organizationUserRole->assigned_on
					]);
				}
				$user['attributes']['subscriptions'] = $subscriptionItems;
				// $user['attributes']['subscriptions'] = SubscriptionItemResource::collection($subscriptionItems);
			}
		}

		return $user;
	}
}
