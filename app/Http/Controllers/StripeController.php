<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Http;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Illuminate\Support\Facades\Log;

// Resources
use App\Http\Resources\OrganizationUserRoleResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\StripeCustomerResource;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StripeSubscriptionResource;
use App\Http\Resources\InvoiceResource;

// Services
use App\Services\GetUserLocaleService;

// Notifications
use App\Notifications\SubscriptionStartedNotification;

// Models
use App\Models\OrganizationUserRole;
use App\Models\User;
use App\Models\BillingAddress;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;

// Requests
use App\Http\Requests\SubscriptionChangeRestrictionRequest;
use App\Http\Requests\SubscriptionRevokeRequest;
use App\Http\Requests\SubscriptionAssignRequest;
use App\Http\Requests\SubscriptionStoreRequest;
use App\Http\Requests\StripeCustomerStoreRequest;
use App\Http\Requests\PaymentMethodsGetRequest;
use App\Http\Requests\SubscriptionChangeQuantityRequest;
use App\Http\Requests\SubscriptionUpgradeRequest;
use App\Http\Resources\UserResource;

// Events
use App\Events\SubscriptionCreated;

/**
 * @OA\Tag(
 *     name="Stripe",
 * )
 */
class StripeController extends Controller
{

	public function handle(Request $request)
	{
		$customer = $request->data["object"]["customer"];
		$billingAddress = BillingAddress::where("stripe_id", $customer)->first();
		$billingAddressable = $billingAddress->billingAddressable;

		broadcast(new SubscriptionCreated($billingAddressable))->toOthers();
	}

    /**
	 * Create a new stripe session
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/stripe/checkout/create-session",
	 *	tags={"Stripe"},
	 *	summary="Create a new stripe session",
	 *	operationId="createStripeSession",
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
     * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 * 				type="object",
	 *   			@OA\AdditionalProperties(
	 *                  type="string"
	 *              ),
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
	public function createSession(Request $request)
	{
		Stripe::setApiKey(config('app.stripe_api_secret'));

		$checkout_session = Session::create($request->json()->all());

        return response()->json(["checkout_session" => $checkout_session], 200);
	}

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
	 * Retrieve the invoices of a specific billing address
	 *
	 * @param  Request  $request
     * @param  BillingAddress $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/billing-addresses/{billing_address_id}/stripe/invoices",
	 *	tags={"Stripe"},
	 *	summary="List stripe invoices of billing address.",
	 *	operationId="listStripeInvoices",
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
	public function listInvoices(BillingAddress $billingAddress)
	{
		// Check if the user is authorized to make this request
		$this->authorize('listInvoices', $billingAddress);

        $invoices = $billingAddress->invoicesIncludingPending();

        return response()->json(["data" => [
            "invoices" => $invoices
        ]], 200);
	}

	/**
	 * Retrieve a specific invoice of a specific billing address
	 *
	 * @param  Request  $request
     * @param  BillingAddress $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/billing-addresses/{billing_address_id}/stripe/invoices/{invoice_id}",
	 *	tags={"Stripe"},
	 *	summary="Show a specific stripe invoice of a billing address.",
	 *	operationId="showStripeInvoice",
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
	 *		name="invoice_id",
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
	public function showInvoice(BillingAddress $billingAddress, $invoiceId)
	{
		// Check if the user is authorized to make this request
		$this->authorize('showInvoice', $billingAddress);

        $invoice = $billingAddress->findInvoice($invoiceId);

		return new InvoiceResource($invoice);
        return response()->json(["data" => [
            "invoice" => $invoice
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
	 *                  type="string",
	 * 					example="card"
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
	 *                  description="The Id of the selected payment method",
	 *                  property="payment_method_id",
	 *                  type="string",
	 *              ),
	 *   			@OA\Property(
	 *                  property="products",
	 *                  type="array",
	 * 					@OA\Items(
	 * 	   					@OA\Property(
	 * 							description="The api id of the price",
	 *              		    property="price_api_id",
	 *              		    type="string"
	 *              		),
	 *  					@OA\Property(
	 * 							description="The amount of this subscription that shall be created",
	 *              		    property="quantity",
	 *              		    type="integer",
	 *              		    format="int32",
	 *              		),
	 * 					)
	 *              ),
	 *              required={"subscription_name", "payment_method_id", "products"}
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
		$class = $billingAddress->billing_addressable_type;
		// Additional validation to check if the billingAddressable is a normal user. If so, restrict the quantity to only 1
		if($class == 'user') {
			if($request->quantity > 1) {
				return response()->json(["message" => __('validation.quantity-not-allowed')], 400);
			}
		}

		// Check if the user is authorized to create a new subscription for the billing address
		$this->authorize('createSubscription', $billingAddress);

        $subscriptionCreationQuery = $billingAddress->newSubscription($request->subscription_name, array_column($request->products, 'price_api_id'));
		foreach($request->products as $product) {
			if(array_key_exists('quantity', $product)) {
				$subscriptionCreationQuery = $subscriptionCreationQuery->quantity($product['quantity'], $product['price_api_id']);
			}
		}

		$subscription = $subscriptionCreationQuery->create($request->payment_method_id);

		if($class == 'user') {
			$notifiable = $billingAddress->billingAddressable;

			// If the billingAddressable is a normal user, assign the subscription to himself
			$notifiable->update([
				'subscription_item_id' => $subscription->id
			]);
		} else {
			$subscriptionItem = SubscriptionItem::where("stripe_price", $subscription->stripe_price)->where("subscription_id", $subscription->id)->first();
			$organizationUserRole = OrganizationUserRole::where("organization_id", $billingAddress->billingAddressable->id)->where("user_id", $billingAddress->billingAddressable->user_id)->first();

			if($organizationUserRole->subscription_item_id == NULL) {
				$billingAddress->billingAddressable->users()->updateExistingPivot($billingAddress->billingAddressable->user_id, [
					'subscription_item_id' => $subscriptionItem->stripe_id,
					'restricted_subscription_usage' => $request->restricted_subscription_usage ? 1 : 0
				]);
			}

			$notifiable = $billingAddress->billingAddressable->creator;
		}

		$notifiable->notify((new SubscriptionStartedNotification($subscription))->locale(GetUserLocaleService::getLocale($notifiable)));

		$stripe = new StripeClient(config('app.stripe_api_secret'));
		$subscription = $stripe->subscriptions->retrieve(
			$subscription->stripe_id,
			[]
		);

        return new StripeSubscriptionResource($subscription);
	}

	/**
	 * Upgrade subscription
	 *
	 * @param  Request  $request
     * @param  BillingAddress $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/billing-addresses/{billing_address_id}/stripe/subscription/{subscription_id}/upgrade",
	 *	tags={"Stripe"},
	 *	summary="Upgrade to the given subscription",
	 *	operationId="upgradeSubscription",
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
	 *                  description="Specifies the exact product of the susbcriptions of which the quantitiy shall be changed",
	 *                  property="price_api_id",
	 *                  type="string"
	 *              ),
	 *             @OA\Property(
	 *                  description="The id of the exact subscription item that should be assigned to the user",
	 *                  property="subscription_item_id",
	 *                  type="string"
	 *              ),
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
	public function upgradeSubscription(SubscriptionUpgradeRequest $request, BillingAddress $billingAddress, $subscriptionId)
	{
		// Check if the user is authorized to upgrade the billing addresses subscription
		$this->authorize('upgradeSubscription', $billingAddress);

		$subscriptionItemId = $request->subscription_item_id;
		$price_api_id = $request->price_api_id;

		$stripe = new StripeClient(config('app.stripe_api_secret'));
		$response = $stripe->subscriptions->update(
			$subscriptionId,
			[
			  'items' => [
					[
					  'id' => $subscriptionItemId,
					  'price' => $price_api_id,
					],
			  	],
			]
		);

		return StripeSubscriptionResource::collection($response->data);
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
	 *                  description="Specifies the exact product of the susbcriptions of which the quantitiy shall be changed",
	 *                  property="price_api_id",
	 *                  type="string"
	 *              ),
	 *             @OA\Property(
	 *                  description="The id of the exact subscription item that should be assigned to the user",
	 *                  property="subscription_item_id",
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
	public function changeSubscriptionQuantity(SubscriptionChangeQuantityRequest $request, BillingAddress $billingAddress, $subscriptionId)
	{
		// Check if the user is authorized to change the billing addresses subscription quantity
		$this->authorize('changeSubscriptionQuantity', $billingAddress);

		$subscriptionItemId = $request->subscription_item_id;
		$quantity = $request->quantity;
		$subscription = Subscription::where('stripe_id', $subscriptionId)->first();

		if($request->type == 'increment') {
			// Add $quantity to the subscription's current quantity
			$subscription = $subscription->incrementQuantity($quantity, $request->price_api_id);
		} else {
            $subscriptionItem = $subscription->items()->where('stripe_id', $subscriptionItemId)->first();
			$totalAssignments = $this->getAmountOfAssignments($subscriptionItemId);

			// If all quantities of this subscription are assigned to users, it cannot be decreased
			if($totalAssignments > $subscriptionItem->quantity - $quantity) {
				return response()->json(["message" => __('application.subscription-quantity-not-sufficient')], 400);
			} else {
				// Subtract $quantity from the subscription's current quantity
				$subscription = $subscription->decrementQuantity($quantity, $request->price_api_id);
			}
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
		$stripe = new StripeClient(config('app.stripe_api_secret'));
		$response = $stripe->products->all(["active" => true]);

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
	 *	summary="Get a list of the stripe subscriptions of a specific billing address",
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
	public function cancelSubscription(BillingAddress $billingAddress, $subscriptionId)
	{
		$subscription = Subscription::where('stripe_id', $subscriptionId)->first();
		$subscriptionItems = $subscription->items;

		// Check if the user is authorized to list the subscriptions
		$this->authorize('cancelSubscription', $billingAddress);

		$val = $billingAddress->subscription($subscription->name)->cancel();

		// Remove sub itemes from organization users
		foreach($subscriptionItems as $subscriptionItem) {
			$users = $billingAddress->billingAddressable->users;
			foreach($users as $user) {
				$billingAddress->billingAddressable->users()->where("subscription_item_id", $subscriptionItem)->updateExistingPivot($user->id, [
					'subscription_item_id' => NULL,
					'restricted_subscription_usage' => NULL,
					'assigned_on' => NULL
				]);
			}
		}

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
	 * 	@OA\Parameter(
	 *		name="include-subscription-item",
	 *		required=false,
	 *		in="header"
	 *	),
	 *
     * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *             @OA\Property(
	 *                  description="The id of the exact subscription item that should be assigned to the user",
	 *                  property="subscription_item_id",
	 *                  type="string"
	 *              ),
	 *             @OA\Property(
	 *                  description="Defines if the user is only allowed to use this subscription within the organization",
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
		$subscriptionItemId = $request->subscription_item_id;
		// $subscription = Subscription::where('stripe_id', $subscriptionId)->first();
		// $subscriptionItem = $subscription->items()->where('stripe_id', $subscriptionItemId)->first();
		$subscriptionItem = SubscriptionItem::where("stripe_id", $subscriptionItemId)->first();
		$assignments = $this->getAmountOfAssignments($subscriptionItemId);

		if($assignments >= $subscriptionItem->quantity) {
			return response()->json(["message" => __('application.subscription-quantity-not-sufficient')], 400);
		}

		/**
		 * Check if the billing address which is assigning the subscription is a personal user or organization account.
		 * If it is a personal user account, assign the subscription to himself
		**/
		if($billingAddress->billing_addressable_type == 'user') {
			$user = $billingAddress->billingAddressable;
			$user->update([
				'subscription_item_id' => $subscriptionItemId
			]);

			$user->endTrial();

			return new UserResource($user);
		}

		$organization = $billingAddress->billingAddressable;

		// Check if the user that shall receive the subscription is part of the organization
		$user = User::find($request->user_id);
		$organization = $user->organizations->find($organization);
		if ($organization == NULL && $organization->user_id != $user->id) {
			return response()->json(["message" => __('application.user-not-part-of-organization')], 403);
		}

		// Update the pivot model
		$user->organizations()->updateExistingPivot($organization->id, [
			'subscription_item_id' => $subscriptionItemId,
			'restricted_subscription_usage' => $request->restricted_subscription_usage ? 1 : 0,
			"assigned_on" => now()
		]);

		return new OrganizationUserRoleResource(OrganizationUserRole::where('organization_id', $organization->id)->where('user_id', $user->id)
			->with('organization')
			->with('user')
			->with('role')
			->with('subscriptionItem')
		->first());
	}

	/**
	 * Revoke subscription from a user
	 *
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/billing-addresses/{billing_address_id}/stripe/subscriptions/{subscription_id}/revoke",
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
	 *		name="subscription_id",
	 *		required=true,
	 *		in="path"
	 *	),
	 *	@OA\Parameter(
	 *		name="billing_address_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/BillingAddress/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-subscription-item",
	 *		required=false,
	 *		in="header"
	 *	),
	 *
     * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *             @OA\Property(
	 *                  description="The id of the exact subscription item that should be assigned to the user",
	 *                  property="subscription_item_id",
	 *                  type="string"
	 *              ),
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
	public function revokeSubscription(SubscriptionRevokeRequest $request, BillingAddress $billingAddress, $subscriptionId)
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
				'subscription_item_id' => NULL
			]);

			return new UserResource($user);
		} else {
			$organization = $billingAddress->billingAddressable;

			// Check if the subscription that shall be revoked belongs to one of the requesting users organizations
			$subscription = Subscription::where('stripe_id', $subscriptionId)->first();

			if($subscription->billing_address_id != $billingAddress->id) {
				return response()->json(["message" => __('application.unsufficient-permissions')], 403);
			} else {

				// Check if the user that the subscription shall be revoked from is part of the organization
				$user = User::find($request->user_id);
				$organization = $user->organizations->find($organization);
				if ($organization == NULL && $organization->user_id != $user->id) {
					return response()->json(["message" => __('application.user-not-part-of-organization')], 403);
				}

				// Update the pivot model
				$organizations = $user->organizations()->where("subscription_item_id", $request->subscription_item_id)->get();

				foreach($organizations as $organization) {
					$organization->users()->updateExistingPivot($user, array(
						'subscription_item_id' => NULL,
						'restricted_subscription_usage' => 0
					), false);
				}
				// $user->organizations()->updateExistingPivot($organization->id, [
				// 	'subscription_item_id' => NULL,
				// 	'restricted_subscription_usage' => NULL
				// ]);

				return response()->json(["message" => __('application.subscription-revoked-successfully')], 200);
				// return new OrganizationUserRoleResource(OrganizationUserRole::where('organization_id', $organization->id)->where('user_id', $user->id)
				// ->with('organization')
				// ->with('user')
				// ->with('role')
				// ->with('subscriptionItem')
				// ->first());

			}
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

		return new OrganizationUserRoleResource(OrganizationUserRole::where('organization_id', $organization->id)->where('user_id', $user->id)
			->with('organization')
			->with('user')
			->with('role')
			->with('subscriptionItem')
			->first());
	}

	// Get the total amount of users that the given subscription was assigned to
	public function getAmountOfAssignments($subscriptionItemId)
	{
		// $amountOfUsers = User::where('subscription_item_id', $subscriptionItemId)->count(); // Amount of personal user accounts this subscription has been assigned to
		// $amountOfOrganizationUsers = OrganizationUserRole::where('subscription_item_id', $subscriptionItemId)->groupBy(["user_id"])->count(); // Amount of organization user accounts this subscription has been assigned to
		$amountOfOrganizationUsers = OrganizationUserRole::where('subscription_item_id', $subscriptionItemId)->get()->unique(["user_id"])->count();

		// return $amountOfUsers + $amountOfOrganizationUsers;
		return $amountOfOrganizationUsers;
	}
}
