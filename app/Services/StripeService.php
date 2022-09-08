<?php

namespace App\Services;

class StripeService
{
    // Create a new stripe customer
    public function createStripeCustomer($model)
	{
        $billingAddress = $model->billingAddress;
   
		// Create the corresponding stripe customer
		$stripeCustomer = $billingAddress->createOrGetStripeCustomer(['name' => $model->first_name . ' ' . $model->last_name]);

        return $stripeCustomer;
	}

    // Retrieve the balance of a specific user/organization
	public function showBalance($billingAddress)
	{
        $balance = $billingAddress->balance();

        return $balance;
	}
}

