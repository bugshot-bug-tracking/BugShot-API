<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;
use App\Models\OrganizationUserRole;

class NotificationResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$notification = array(
			'id' => $this->id,
			'type' => 'Notification',
			'attributes' => [
				"type" => $this->type,
				"notifiable_type" => $this->notifiable_type,
				"notifiable_id" => $this->notifiable_id,
				"data" => $this->data
			]
		);

		return $notification;
	}
}
