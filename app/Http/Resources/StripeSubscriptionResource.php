<?php

namespace App\Http\Resources;

use Laravel\Cashier\Subscription;
use App\Models\BillingAddress;
use App\Models\OrganizationUserRole;
use Illuminate\Http\Resources\Json\JsonResource;

class StripeSubscriptionResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$subscriptionModel = Subscription::where('stripe_id', $this->id)->first();
		$billingAddress = BillingAddress::find($subscriptionModel->billing_address_id);

		foreach($this->items as $item) {
			$assigned = OrganizationUserRole::where("subscription_item_id", $item->id)->get()->unique(["user_id"])->count();
			$item["assigned"] = $assigned;
		}

		return [
			'id' => $this->id,
			'type' => 'StripeSubscription',
			'attributes' => [
				'billable' => new BillingAddressResource($billingAddress),
				'application' => $this->application,
				'application_fee_percent' => $this->application_fee_percent,
				'automatic_tax' => $this->automatic_tax,
				'billing_cycle_anchor' => $this->billing_cycle_anchor,
				'billing_thresholds' => $this->billing_thresholds,
				'cancel_at' => $this->cancel_at,
				'cancel_at_period_end' => $this->cancel_at_period_end,
				'canceled_at' => $this->canceled_at,
				'collection_method' => $this->collection_method,
				'created' => $this->created,
				'currency' => $this->currency,
				'current_period_end' => $this->current_period_end,
				'current_period_start' => $this->current_period_start,
				'customer' => $this->customer,
				'days_until_due' => $this->days_until_due,
				'default_payment_method' => $this->default_payment_method,
				'default_source' => $this->default_source,
				'default_tax_rates' => $this->default_tax_rates,
				'description' => $this->description,
				'discount' => $this->discount,
				'ended_at' => $this->ended_at,
				'items' => $this->items,
				'latest_invoice' => $this->latest_invoice,
				'livemode' => $this->livemode,
				'metadata' => $this->metadata,
				'next_pending_invoice_item_invoice' => $this->next_pending_invoice_item_invoice,
				'pause_collection' => $this->pause_collection,
				'payment_settings' => $this->payment_settings,
				'pending_invoice_item_interval' => $this->pending_invoice_item_interval,
				'pending_setup_intent' => $this->pending_setup_intent,
				'pending_update' => $this->pending_update,
				'plan' => $this->plan,
				'quantity' => $this->quantity,
				'schedule' => $this->schedule,
				'start_date' => $this->start_date,
				'status' => $this->status,
				'test_clock' => $this->test_clock,
				'transfer_data' => $this->transfer_data,
				'trial_end' => $this->trial_end,
				'trial_start' => $this->trial_start
			]
		];
	}
}
