<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

// Resources
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\StripeCustomerResource;
use App\Http\Resources\PaymentMethodResource;

// Services
use App\Services\StripeService;

// Models
use App\Models\BillingAddress;

// Requests
use App\Http\Requests\SubscriptionStoreRequest;
use App\Http\Requests\StripeCustomerStoreRequest;
use App\Http\Requests\PaymentMethodsGetRequest;
use App\Http\Requests\SubscriptionChangeQuantityRequest;

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
	public function createStripeCustomer(StripeCustomerStoreRequest $request, BillingAddress $billingAddress, StripeService $stripeService)
	{
		// Check if the user is authorized to create a new stripe customer
		$this->authorize('createStripeCustomer', $billingAddress);
       
		// Create the corresponding stripe customer
		$stripeCustomer = $stripeService->createStripeCustomer($billingAddress);
		// $stripeCustomer = $billingAddress->createOrGetStripeCustomer(['name' => $billingAddress->first_name . ' ' . $billingAddress->last_name]);

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
	 *	summary="Get the billable model",
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
	public function showBalance(BillingAddress $billingAddress, StripeService $stripeService)
	{
		// Check if the user is authorized to make this request
		$this->authorize('showBalance', $billingAddress);

		$balance = $stripeService->showBalance($billingAddress);
        // $balance = $billingAddress->balance();

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
	 *	path="/billing-addresses/{billing_address_id}/stripe/subscription/{subscription_id)/change-quantity",
	 *	tags={"Stripe"},
	 *	summary="Change the quanitity of the given subscription",
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
	 *                  description="The name of the subscription that needs to be adjusted",
	 *                  property="subscription_name",
	 * 					example="default",
	 *                  type="string"
	 *              ),
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
	public function changeSubscriptionQuantity(SubscriptionChangeQuantityRequest $request, BillingAddress $billingAddress)
	{
		// Check if the user is authorized to change the billing addresses subscription quantity
		$this->authorize('changeSubscriptionQuantity', $billingAddress);
       
		$quantity = $request->quantity;
		$subscriptionName = $request->subscription_name;
		if($request->type == 'increment') {
			// Add $quantity to the subscription's current quantity
			$billingAddress->subscription($subscriptionName)->incrementQuantity($quantity);
		} else {
			// Subtract $quantity from the subscription's current quantity
			$billingAddress->subscription($subscriptionName)->decrementQuantity($quantity);
		}

        // return PaymentMethodResource::collection($paymentMethods);
	}

	/** ##################################################################
	 * #################### PRODUCT SPECIFIC METHODS ####################
	*/##################################################################

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
	public function listProducts()
	{
		// Check if the user is authorized to change the billing addresses subscription quantity
		abort(401);
       
		// $quantity = $request->quantity;
		// $subscriptionName = $request->subscription_name;
		// if($request->type == 'increment') {
		// 	// Add $quantity to the subscription's current quantity
		// 	$billingAddress->subscription($subscriptionName)->incrementQuantity($quantity);
		// } else {
		// 	// Subtract $quantity from the subscription's current quantity
		// 	$billingAddress->subscription($subscriptionName)->decrementQuantity($quantity);
		// }

        // return PaymentMethodResource::collection($paymentMethods);
	}
}
