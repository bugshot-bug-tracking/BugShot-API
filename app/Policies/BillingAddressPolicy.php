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
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can show the invoices of the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function listInvoices(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can show a specific invoice of the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function showInvoice(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can retrieve the setup intent form.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function showSetupIntentForm(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can get the payment methods
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function getPaymentMethods(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can create a new subscription
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createSubscription(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can create a new stripe customer
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createStripeCustomer(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can retrieve a stripe customer
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function getStripeCustomer(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can change the quantity of the subscription
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function changeSubscriptionQuantity(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can list the subscriptions of the given resource
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function listSubscriptions(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can cancel the given subscription
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function cancelSubscription(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can assign a subscription to another user
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function assignSubscription(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user can revoke a subscription from a user
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function revokeSubscription(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }

    /**
     * Determine whether the user change restriction of a subscription of the given user
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingAddress  $billingAddress
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function changeRestrictionOfSubscription(User $user, BillingAddress $billingAddress)
    {
        if($billingAddress->billing_addressable_type == 'organization') {
            $organization = $billingAddress->billingAddressable;
  
            // The user is the creator of the organization
            if($organization->user_id == $user->id) {
                return true;
            }
    
            // The user isn't part of the organization
            $organization = $user->organizations()->find($organization);
            if ($organization == NULL) {
                return false;
            }
    
            $role = $organization->pivot->role_id;
    
            switch ($role) {
                case 1:
                    return true;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
  
        return $user->id == $billingAddress->billing_addressable_id;
    }
}