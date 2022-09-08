<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Organization;
use App\Models\BillingAddress;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillingAddressPolicy
{
    use HandlesAuthorization;

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
     * Determine whether the user can show the balance of the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function showBalance(User $user, BillingAddress $billingAddress)
    {
        dd($billingAddress->billingAddressable());

        // return $user->id == $requestedUser->id;
    }

    /**
     * Determine whether the user can retrieve the setup intent form.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $requestedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function showSetupIntentForm(User $user, BillingAddress $billingAddress)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine whether the user can get the payment methods
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $requestedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function getPaymentMethods(User $user, BillingAddress $billingAddress)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine whether the user can create a new subscription
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $requestedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createSubscription(User $user, BillingAddress $billingAddress)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine whether the user can create a new stripe customer
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $requestedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createStripeCustomer(User $user, BillingAddress $billingAddress)
    {
        dd($billingAddress->billingAddressable());

        return $user->id == $billingAddress->id;
    }

    /**
     * Determine whether the user can retrieve a stripe customer
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $requestedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function getStripeCustomer(User $user, BillingAddress $billingAddress)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Determine whether the user can change the quantity of the subscription
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $requestedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function changeSubscriptionQuantity(User $user, BillingAddress $billingAddress)
    {
        return $user->id == $requestedUser->id;
    }
}