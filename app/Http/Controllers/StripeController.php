<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\SubscriptionResource;

// Models
use App\Models\User;

// Requests
use App\Http\Requests\SubscriptionStoreRequest;

/**
 * @OA\Tag(
 *     name="Stripe",
 * )
 */
class StripeController extends Controller
{
	/**
	 * Retrieve the balance of a specific user
	 *
	 * @param  Request  $request
     * @param  User  $user
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/users/{user_id}/balance",
	 *	tags={"Stripe"},
	 *	summary="Show stripe balance of user.",
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
	 *		name="user_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
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
	public function showBalance(User $user)
	{
		// Check if the user is authorized to make this request
		$this->authorize('showBalance', $user);

        $balance = $user->balance();

        return response()->json(["data" => [
            "balance" => $balance
        ]], 200);
	}

    /**
	 * Show setup intent form
	 *
	 * @param  Request  $request
     * @param  User  $user
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/users/{user_id}/setup-intent-form",
	 *	tags={"Stripe"},
	 *	summary="Show the setup intent form for the user",
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
	 *		name="user_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
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
	public function showSetupIntentForm(User $user)
	{
		// Check if the user is authorized to show the setup intent form
		$this->authorize('showSetupIntentForm', $user);

        $intent = $user->createSetupIntent();

        return response()->json(['data'=> [
            'intent' => $intent
        ]], 200);
	}

    /**
	 * Create a new subscription
	 *
	 * @param  Request  $request
     * @param  User  $user
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/users/{user_id}/subscription",
	 *	tags={"Stripe"},
	 *	summary="Create a new subscription for the user",
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
	 *		name="user_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
	 *		)
	 *	),
	 *
     * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
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
	 *              required={"payment_method_id", "quantity"}
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
	public function createSubscription(SubscriptionStoreRequest $request, User $user)
	{
		// Check if the user is authorized to create a new subscription for the user
		$this->authorize('createSubscription', $user);
       
        $subscription = $user->newSubscription(
            'default', 'price_1LdBwAGDzmJ5MOfXt0icF1JN'
        )->quantity($request->quantity)
        ->create($request->payment_method_id);

        return new SubscriptionResource($subscription);
	}
}
