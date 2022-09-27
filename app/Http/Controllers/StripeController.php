<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Http;
use Stripe\StripeClient;

// Resources
use App\Http\Resources\OrganizationUserRoleResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\StripeCustomerResource;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StripeSubscriptionResource;

// Models
use App\Models\OrganizationUserRole;
use App\Models\User;
use App\Models\BillingAddress;
use Laravel\Cashier\Subscription;

// Requests
use App\Http\Requests\SubscriptionChangeRestrictionRequest;
use App\Http\Requests\SubscriptionRevokeRequest;
use App\Http\Requests\SubscriptionAssignRequest;
use App\Http\Requests\SubscriptionStoreRequest;
use App\Http\Requests\StripeCustomerStoreRequest;
use App\Http\Requests\PaymentMethodsGetRequest;
use App\Http\Requests\SubscriptionChangeQuantityRequest;
use App\Http\Resources\UserResource;

/**
 * @OA\Tag(
 *     name="Stripe",
 * )
 */
class StripeController extends Controller
{
    /**
	 * Create a new stripe customer
	 *
	 * @param  Request  $request
     * @param  BillingAddress $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/billing-addresses/{billing_address_id}/stripe/customer",
	 *	tags={"Stripe"},
	 *	summary="Create a new stripe customer",
	 *	operationId="createStripeCustomer",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
     * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function createStripeCustomer(StripeCustomerStoreRequest $request, BillingAddress $billingAddress)
	{
		// Check if the user is authorized to create a new stripe customer
		$this->authorize('createStripeCustomer', $billingAddress);
       
		// Create the corresponding stripe customer
		$stripeCustomer = $billingAddress->createOrGetStripeCustomer(['name' => $billingAddress->first_name . ' ' . $billingAddress->last_name]);

        return new StripeCustomerResource($stripeCustomer);
	}

	/**
	 * Retrieve a stripe customer
	 *
	 * @param  Request  $request
     * @param  BillingAddress $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/billing-addresses/{billing_address_id}/stripe/customer/{stripe_customer_id}",
	 *	tags={"Stripe"},
	 *	summary="Get the stripe customer",
	 *	operationId="getStripeCustomer",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="stripe_customer_id",
	 *		required=true,
	 *		in="path"
	 *	),
     * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function getStripeCustomer(StripeCustomerStoreRequest $request, BillingAddress $billingAddress, $stripeCustomerId)
	{
		// Check if the user is authorized to create a new subscription for the billing address
		$this->authorize('getStripeCustomer', $billingAddress);
	
		// Retrieve the corresponding stripe customer
		$stripeCustomer = Cashier::findBillable($stripeCustomerId);

        return new StripeCustomerResource($stripeCustomer);
	}

	/**
	 * Retrieve the balance of a specific billing address
	 *
	 * @param  Request  $request
     * @param  BillingAddress $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/billing-addresses/{billing_address_id}/stripe/balance",
	 *	tags={"Stripe"},
	 *	summary="Show stripe balance of billing address.",
	 *	operationId="showStripeBalance",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function showBalance(BillingAddress $billingAddress)
	{
		// Check if the user is authorized to make this request
		$this->authorize('showBalance', $billingAddress);

        $balance = $billingAddress->balance();

        return response()->json(["data" => [
            "balance" => $balance
        ]], 200);
	}

    /**
	 * Show setup intent form
	 *
	 * @param  Request  $request
     * @param  BillingAddress $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/billing-addresses/{billing_address_id}/stripe/setup-intent-form",
	 *	tags={"Stripe"},
	 *	summary="Show the setup intent form for the billing address",
	 *	operationId="showSetupIntent",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function showSetupIntentForm(BillingAddress $billingAddress)
	{
		// Check if the user is authorized to show the setup intent form
		$this->authorize('showSetupIntentForm', $billingAddress);

        $intent = $billingAddress->createSetupIntent();

        return response()->json(['data'=> [
            'intent' => $intent
        ]], 200);
	}

    /**
	 * Retrieve a collection of payment methods
	 *
	 * @param  Request  $request
     * @param  BillingAddress $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/billing-addresses/{billing_address_id}/stripe/payment-methods",
	 *	tags={"Stripe"},
	 *	summary="Retrieve a collection of payment methods",
	 *	operationId="getUsersPaymentMethods",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
     * 
     * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="Specifies the type of payment method that shall be retrieved",
	 *                  property="type",
	 *                  type="string"
	 *              )
	 *          )
	 *      )
	 *  ),
	 * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function getPaymentMethods(PaymentMethodsGetRequest $request, BillingAddress $billingAddress)
	{
		// Check if the user is authorized to get the billing addresses payment methods
		$this->authorize('getPaymentMethods', $billingAddress);
       
        $paymentMethods = $request->type ? $billingAddress->paymentMethods($request->type) : $billingAddress->paymentMethods();

        return PaymentMethodResource::collection($paymentMethods);
	}

    /**
	 * Create a new subscription
	 *
	 * @param  Request  $request
     * @param  BillingAddress $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/billing-addresses/{billing_address_id}/stripe/subscription",
	 *	tags={"Stripe"},
	 *	summary="Create a new subscription for the billing address",
	 *	operationId="createNewSubscription",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 *
     * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The name of the selected subscription (only for internal use)",
	 *                  property="subscription_name",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The api id of the price",
	 *                  property="price_api_id",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The Id of the selected payment method",
	 *                  property="payment_method_id",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The amount of subscriptions that shall be created",
	 *                  property="quantity",
	 *                  type="integer",
	 *              ),
	 *              required={"subscription_name", "price_api_id", "payment_method_id", "quantity"}
	 *          )
	 *      )
	 *  ),
     * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function createSubscription(SubscriptionStoreRequest $request, BillingAddress $billingAddress)
	{
		// Check if the user is authorized to create a new subscription for the billing address
		$this->authorize('createSubscription', $billingAddress);
       
        $subscription = $billingAddress->newSubscription($request->subscription_name, $request->price_api_id)
			->quantity($request->quantity)
			->create($request->payment_method_id);

        return new SubscriptionResource($subscription);
	}

	/**
	 * Retrieve a collection of payment methods
	 *
	 * @param  Request  $request
     * @param  BillingAddress $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/billing-addresses/{billing_address_id}/stripe/subscription/{subscription_id}/change-quantity",
	 *	tags={"Stripe"},
	 *	summary="Change the quantity of the given subscription",
	 *	operationId="changeSubscriptionQuantity",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="subscription_id",
	 *		required=true,
	 *		in="path"
	 *	),
     * 
     * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="Specifies if the quantity is an increment or decrement",
	 *                  property="type",
	 * 					example="increment",
	 *                  type="string"
	 *              ),
	 *              @OA\Property(
	 *                  description="Specifies the quantity that shall be added/removed from the subscription",
	 *                  property="quantity",
	 * 					example="2",
	 *                  type="integer"
	 *              )
	 *          )
	 *      )
	 *  ),
	 * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function changeSubscriptionQuantity(SubscriptionChangeQuantityRequest $request, BillingAddress $billingAddress, $subscriptionId)
	{
		// Check if the user is authorized to change the billing addresses subscription quantity
		$this->authorize('changeSubscriptionQuantity', $billingAddress);
	
		$quantity = $request->quantity;
		$subscription = Subscription::where('stripe_id', $subscriptionId)->first();
		if($request->type == 'increment') {
			// Add $quantity to the subscription's current quantity
			$subscription = $billingAddress->subscription($subscription->name)->incrementQuantity($quantity);
		} else {
			// Subtract $quantity from the subscription's current quantity
			$subscription = $billingAddress->subscription($subscription->name)->decrementQuantity($quantity);
		}
	
        return new SubscriptionResource($subscription);
	}

	/**
	 * Retrieve a collection of products
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/stripe/products",
	 *	tags={"Stripe"},
	 *	summary="Get a list of the stripe products",
	 *	operationId="listProducts",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function listProducts(Request $request)
	{
		// Check if the user is authorized to list the products
		if(!$request->user()->isAdministrator()) {
			abort(401);
		}	

		$stripe = new StripeClient(config('app.stripe_api_secret'));
		$response = $stripe->products->all();

        return ProductResource::collection($response->data);
	}


	/**
	 * Retrieve a collection of subscriptions
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/billing-addresses/{billing_address_id}/stripe/subscriptions",
	 *	tags={"Stripe"},
	 *	summary="Get a list of the stripe subscriptions of a specific user",
	 *	operationId="listSubscriptions",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function listSubscriptions(BillingAddress $billingAddress)
	{
		// Check if the user is authorized to list the subscriptions
		$this->authorize('listSubscriptions', $billingAddress);

		$stripe = new StripeClient(config('app.stripe_api_secret'));
		$response = $stripe->subscriptions->all(['customer' => $billingAddress->stripe_id]);

        return StripeSubscriptionResource::collection($response->data);
	}

	/**
	 * Cancel a specific subscription
	 *
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/billing-addresses/{billing_address_id}/stripe/subscriptions/{subscription_id}",
	 *	tags={"Stripe"},
	 *	summary="Cancel a specific subscription",
	 *	operationId="cancelSubscription",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="subscription_id",
	 *		required=true,
	 *		in="path"
	 *	),
	 * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function cancelSubscription(BillingAddress $billingAddress, Subscription $subscription)
	{
		// Check if the user is authorized to list the subscriptions
		$this->authorize('cancelSubscription', $billingAddress);

		$val = $billingAddress->subscription($subscription->name)->cancel();

		return response($val, 204);
	}

	/**
	 * Assign subscription to a user 
	 *
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/billing-addresses/{billing_address_id}/stripe/subscriptions/{subscription_id}/assign",
	 *	tags={"Stripe"},
	 *	summary="Assign a subscription to a user",
	 *	operationId="assignSubscription",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="subscription_id",
	 *		required=true,
	 *		in="path"
	 *	),
	 * 
     * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *             @OA\Property(
	 *                  description="Defines if the user is only allowed to use this subscription within the ",
	 *                  property="restricted_subscription_usage",
	 *                  type="boolean"
	 *              ),
	 *              @OA\Property(
	 *                  description="The id of the user the subscription shall be assigned to",
	 *                  property="user_id",
	 *                  type="integer"
	 *              )
	 *          )
	 *      )
	 *  ),
	 * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function assignSubscription(SubscriptionAssignRequest $request, BillingAddress $billingAddress, $subscriptionId)
	{
		
		// Check if the user is authorized to assign a subscription to a user
		$this->authorize('assignSubscription', $billingAddress);

		// Check if the provided subscription has a sufficient quantity
		$quantity = Subscription::where('stripe_id', $subscriptionId)->first()->quantity;
		$amountOfUsers = User::where('subscription_id', $subscriptionId)->count(); // Amount of personal user accounts this subscription has been assigned to
		$amountOfOrganizationUsers = OrganizationUserRole::where('subscription_id', $subscriptionId)->count(); // Amount of organization user accounts this subscription has been assigned to
		
		if(($amountOfUsers + $amountOfOrganizationUsers) == $quantity) {
			return response()->json(["message" => __('application.subscription-quantity-not-sufficient')], 400);
		}

		/** 
		 * Check if the billing address which is assigning the subscription is a personal user or organization account.
		 * If it is a personal user account, assign the subscription to himself
		**/
		if($billingAddress->billing_addressable_type == 'user') {
			$user = $billingAddress->billingAddressable;
			$user->update([
				'subscription_id' => $subscriptionId
			]);

			return new UserResource($user);
		} else {
			$organization = $billingAddress->billingAddressable;

			// Check if the user that shall receive the subscription is part of the organization
			$user = User::find($request->user_id);
			$organization = $user->organizations->find($organization);
			if ($organization == NULL && $organization->user_id != $user->id) {
				return response()->json(["message" => __('application.user-not-part-of-organization')], 403);
			}
			
			// Update the pivot model
			$user->organizations()->updateExistingPivot($organization->id, [
				'subscription_id' => $subscriptionId,
				'restricted_subscription_usage' => $request->restricted_subscription_usage ? 1 : 0
			]);

			return new OrganizationUserRoleResource(OrganizationUserRole::where('organization_id', $organization->id)
			->with('organization')
			->with('user')
			->with('role')
			->with('subscription')
			->first());
		}
	}

	/**
	 * Revoke subscription from a user
	 *
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/billing-addresses/{billing_address_id}/stripe/subscriptions/revoke",
	 *	tags={"Stripe"},
	 *	summary="Revoke a subscription to a user",
	 *	operationId="revokeSubscription",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 *
     * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The id of the user the subscription is assigned to",
	 *                  property="user_id",
	 *                  type="integer"
	 *              )
	 *          )
	 *      )
	 *  ),
	 * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function revokeSubscription(SubscriptionRevokeRequest $request, BillingAddress $billingAddress)
	{
		// Check if the user is authorized to revoke a subscription from a user
		$this->authorize('revokeSubscription', $billingAddress);

		/** 
		 * Check if the billing address which is assigning the subscription is a personal user or organization account.
		 * If it is a personal user account, assign the subscription to himself
		**/
		if($billingAddress->billing_addressable_type == 'user') {
			$user = $billingAddress->billingAddressable;
			$user->update([
				'subscription_id' => NULL
			]);

			return new UserResource($user);
		} else {
			$organization = $billingAddress->billingAddressable;

			// Check if the user that the subscription shall be revoked from is part of the organization
			$user = User::find($request->user_id);
			$organization = $user->organizations->find($organization);
			if ($organization == NULL && $organization->user_id != $user->id) {
				return response()->json(["message" => __('application.user-not-part-of-organization')], 403);
			}
			
			// Update the pivot model
			$user->organizations()->updateExistingPivot($organization->id, [
				'subscription_id' => NULL,
				'restricted_subscription_usage' => NULL
			]);

			return new OrganizationUserRoleResource(OrganizationUserRole::where('organization_id', $organization->id)
			->with('organization')
			->with('user')
			->with('role')
			->with('subscription')
			->first());
		}
	}

	/**
	 * Change restriction of a subscription of a user
	 *
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/billing-addresses/{billing_address_id}/stripe/subscriptions/change-restriction",
	 *	tags={"Stripe"},
	 *	summary="Change restriction of a subscription of a user",
	 *	operationId="changeRestrictionOfSubscription",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 *
     * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *             @OA\Property(
	 *                 description="The id of the user the subscription is assigned to",
	 *                 property="user_id",
	 *                 type="integer"
	 *             ),
	 *             @OA\Property(
	 *                  description="Defines if the user is only allowed to use this subscription within the ",
	 *                  property="restricted_subscription_usage",
	 *                  type="boolean"
	 *              )
	 *          )
	 *      )
	 *  ),
	 * 
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success"
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function changeRestrictionOfSubscription(SubscriptionChangeRestrictionRequest $request, BillingAddress $billingAddress)
	{
		// Check if the user is authorized to change the restriction of a subscription from a user
		$this->authorize('changeRestrictionOfSubscription', $billingAddress);

		$organization = $billingAddress->billingAddressable;

		// Check if the user that the restriction shall be changed from is part of the organization
		$user = User::find($request->user_id);
		$organization = $user->organizations->find($organization);
		if ($organization == NULL && $organization->user_id != $user->id) {
			return response()->json(["message" => __('application.user-not-part-of-organization')], 403);
		}
			
		// Update the pivot model
		$user->organizations()->updateExistingPivot($organization->id, [
			'restricted_subscription_usage' => $request->restricted_subscription_usage ? 1 : 0
		]);

		return new OrganizationUserRoleResource(OrganizationUserRole::where('organization_id', $organization->id)
			->with('organization')
			->with('user')
			->with('role')
			->with('subscription')
			->first());
	}
}
