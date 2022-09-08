<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

/**
 * @OA\Schema()
 */
class BillingAddress extends Model
{
	use Billable, HasFactory, SoftDeletes;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="string",
	 *  maxLength=255,
	 * )
	 * 
	 * @OA\Property(
	 * 	property="billing_addressable_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the billing_addressable resource."
	 * )
	 *
	 * @OA\Property(
	 * 	property="billing_addressable_type",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The type of the billing_addressable resource."
	 * )
	 *
	 * @OA\Property(
	 * 	property="street",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The billing_addressable street."
	 * )
	 * 
	 * @OA\Property(
	 * 	property="housenumber",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The billing_addressable housenumber."
	 * )
	 * 
	 * @OA\Property(
	 * 	property="state",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The billing_addressable state."
	 * )
	 * 
	 * @OA\Property(
	 * 	property="zip",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The billing_addressable zip."
	 * )
	 * 
	 * @OA\Property(
	 * 	property="country",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The billing_addressable country."
	 * )	
	 *  
	 * @OA\Property(
	 * 	property="tax_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The billing_addressable tax_id."
	 * )
	 *
	 * @OA\Property(
	 * 	property="created_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The creation date."
	 * )
	 *
	 * @OA\Property(
	 * 	property="updated_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The last date when the resource was changed."
	 * )
	 * 
	 * @OA\Property(
	 * 	property="deleted_at",
	 * 	type="string",
	 *  format="date-time",
	 * 	description="The deletion date."
	 * )
	 * 
	 */
	protected $fillable = ["id", "street", "housenumber", "city", "state", "zip", "country", "tax_id"];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
	public function billingAddressable()
	{
		return $this->morphTo();
	}
}
