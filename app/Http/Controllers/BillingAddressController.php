<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

// Resources
use App\Http\Resources\BillingAddressResource;

// Models
use App\Models\User;
use App\Models\BillingAddress;
use App\Models\Organization;

// Requests
use App\Http\Requests\BillingAddressStoreRequest;
use App\Http\Requests\BillingAddressUpdateRequest;

/**
 * @OA\Tag(
 *     name="BillingAddress",
 * )
 */
class BillingAddressController extends Controller
{
	/**
	 * Get the billing address of a specific model
	 *
	 * @param  User  $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/billing-addresses/{type}/{id}",
	 *	tags={"BillingAddress"},
	 *	summary="Get the billing address of a specific model",
	 *	operationId="getBillingAddress",
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
	 *		name="type",
	 *		required=true,
	 *		in="path"
	 *	),
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path"
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/BillingAddress"
	 *		)
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
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function getBillingAddress($type, $id)
	{
		// Check if the given type is a user or an organization
		$billingAddress = BillingAddress::where('billing_addressable_id', $id)->firstOrFail();
		$this->authorize('getStripeCustomer', $billingAddress);

		return new BillingAddressResource($billingAddress);
	}

   	/**
     * Store the billing address that belongs to the given model
     *
     * @param  BillingAddressStoreRequest  $request
     * @return Response
     */
	/**
	 * @OA\Post(
	 *	path="/billing-addresses/{type}/{id}",
	 *	tags={"BillingAddress"},
	 *	summary="Store a billing address for a given model",
	 *	operationId="storeBillingAddress",
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
	 *		name="type",
	 *		required=true,
	 *		in="path"
	 *	),
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path"
	 *	),
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The billing address street",
	 *                  property="street",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address housenumber",
	 *                  property="housenumber",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address city",
	 *                  property="city",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address state",
	 *                  property="state",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address zip",
	 *                  property="zip",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address country",
	 *                  property="country",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address tax_id",
	 *                  property="tax_id",
	 *                  type="string",
	 *              ),
	 *              required={"street", "housenumber", "city", "state", "zip", "country", "tax_id"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/BillingAddress"
	 *		)
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
    public function store(BillingAddressStoreRequest $request, $type, $id)
    {
		// Check if the given type is a user or an organization
		$class = Relation::getMorphedModel($type);
		if($class == User::class) {
			$model = User::find($id);
			// Check if the user is authorized to store a billing address for the given user
			$this->authorize('createBillingAddress', [User::class, $model]);
		} else {
			$model = Organization::find($id);
			// Check if the user is authorized to store a billing address for the given organization
			$this->authorize('createBillingAddress', [Organization::class, $model]);
		}

        // Create the billing address
        $billingAddress = $model->billingAddress()->create([
            "id" => (string) Str::uuid(),
			"street" => $request->street,
			"housenumber" => $request->housenumber,
			"state" => $request->state,
			"city" => $request->city,
			"zip" => $request->zip,
			"country" => $request->country,
			"tax_id" => $request->tax_id
		]);

		if($class == User::class) {
			$billingAddress->createOrGetStripeCustomer(['name' => $model->first_name . ' ' . $model->last_name, 'email' => $model->email]);
		} else {
			$billingAddress->createOrGetStripeCustomer(['name' => $model->designation, 'email' => $model->creator->email]);
		}

        return new BillingAddressResource($billingAddress);
    }


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  BillingAddressUpdateRequest  $request
	 * @param  BillingAddress  $billingAddress
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/billing-addresses/{billing_address_id}",
	 *	tags={"BillingAddress"},
	 *	summary="Update a billing address.",
	 *	operationId="updateBillingAddress",
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
	 *		name="_method",
	 *		required=true,
	 *		in="query",
	 *		@OA\Schema(
	 *			type="string",
	 *			default="PUT"
	 *		)
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The billing address street",
	 *                  property="street",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address housenumber",
	 *                  property="housenumber",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address city",
	 *                  property="city",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address state",
	 *                  property="state",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address zip",
	 *                  property="zip",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address country",
	 *                  property="country",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The billing address tax_id",
	 *                  property="tax_id",
	 *                  type="string",
	 *              ),
	 *              required={"street", "housenumber", "city", "state", "zip", "country", "tax_id"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/BillingAddress"
	 *		)
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
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function update(BillingAddressUpdateRequest $request, BillingAddress $billingAddress)
	{
		// Check if the given type is a user or an organization
		if($billingAddress->billing_addressable_type == 'user') {
			// Check if the user is authorized to store a billing address for the given user
			$this->authorize('updateBillingAddress', [User::class, $billingAddress->billingAddressable]);
		} else {
			// Check if the user is authorized to store a billing address for the given organization
			$this->authorize('updateBillingAddress', [Organization::class, $billingAddress->billingAddressable]);
		}

		// Update the billing address
		$billingAddress->update($request->all());

		return new BillingAddressResource($billingAddress);
	}
}
