<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\SubscriptionItem;

/**
 * @OA\Schema()
 */
class OrganizationUserRole extends Model
{
	use HasFactory;

	/**
	 * @OA\Property(
	 * 	property="organization_id",
	 * 	type="string",
	 *  maxLength=255,
	 * 	description="The id of the organization."
	 * )
	 *
	 * @OA\Property(
	 * 	property="user_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the user."
	 * )
	 *
	 * @OA\Property(
	 * 	property="role_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the role."
	 * )
	 *
	 * @OA\Property(
	 * 	property="subscription_item_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the subscription, if the user has been given one."
	 * )
	 *
	 */

	protected $fillable = ["organization_id", "user_id", "role_id", "subscription_item_id"];

	public $timestamps = false;

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function organization()
	{
		return $this->belongsTo(Organization::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function role()
	{
		return $this->belongsTo(Role::class);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function subscriptionItem()
	{
		return $this->belongsTo(SubscriptionItem::class, 'subscription_item_id', 'stripe_id');
	}
}
