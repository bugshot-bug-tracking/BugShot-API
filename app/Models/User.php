<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordLinkNotification;
use App\Services\GetUserLocaleService;
use App\Traits\HasSettings;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;
use Dyrynda\Database\Support\CascadeSoftDeletes;

/**
 * @OA\Schema()
 */

class User extends Authenticatable implements MustVerifyEmail
{
	use Billable, HasSettings, HasApiTokens, HasFactory, Notifiable, SoftDeletes, CascadeSoftDeletes;

	/**
	 * @OA\Property(
	 * 	property="id",
	 * 	type="integer",
	 *  format="int64",
	 * )
	 *
	 * @OA\Property(
	 * 	property="first_name",
	 * 	type="string",
	 *  maxLength=255,
	 * )
	 *
	 * @OA\Property(
	 * 	property="last_name",
	 * 	type="string",
	 *  maxLength=255,
	 * )
	 *
	 * @OA\Property(
	 * 	property="email",
	 * 	type="string",
	 *  maxLength=255,
	 * )
	 *
	 * @OA\Property(
	 * 	property="email_verified_at",
	 * 	type="string",
	 * 	nullable=true,
	 *  format="date-time",
	 * )
	 *
	 * @OA\Property(
	 * 	property="password",
	 * 	type="string",
	 *  format="password",
	 * )
	 *
	 * @OA\Property(
	 * 	property="remember_token",
	 * 	type="string",
	 * 	nullable=true,
	 * )
	 *
	 * @OA\Property(
	 * 	property="is_admin",
	 * 	type="boolean"
	 * )
	 *
	 * @OA\Property(
	 * 	property="subscription_item_id",
	 * 	type="integer",
	 *  format="int64",
	 * 	description="The id of the subscription, if the user has been given one."
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
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'password',
		'email_verified_at',
		'subscription_item_id',
		'trial_end_date',
	];

	// Cascade the soft deletion to the given child resources
	protected $cascadeDeletes = ['createdOrganizations', 'createdCompanies', 'createdProjects', 'billingAddress', 'settings'];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token'
	];

	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];
	}

	/**
	 * @return string $fullName
	 */
	public function fullName()
	{
		// Trim function eliminates additional spaces when a name part is not available
		$fullName = trim($this->first_name . " " . $this->last_name);

		return empty($fullName) ? __("application.anonymous-user") : $fullName;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphOne
	 */
	public function billingAddress()
	{
		return $this->morphOne(BillingAddress::class, "billing_addressable");
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function organizations()
	{
		return $this->belongsToMany(Organization::class, 'organization_user_roles')->withPivot('role_id', "subscription_item_id");
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function createdOrganizations()
	{
		return $this->hasMany(Organization::class, 'user_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function companies()
	{
		return $this->belongsToMany(Company::class, 'company_user_roles')->withPivot('role_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function createdCompanies()
	{
		return $this->hasMany(Company::class, 'user_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function projects()
	{
		return $this->belongsToMany(Project::class, 'project_user_roles')->withPivot('role_id')->orderBy('updated_at', 'desc');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function createdProjects()
	{
		return $this->hasMany(Project::class, 'user_id');
	}

	/**
	 * Get all of the bugs of the users projects.
	 */
	public function projectsBugs()
	{
		return $this->hasManyThrough(Bug::class, Project::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function bugs()
	{
		return $this->belongsToMany(Bug::class, 'bug_user_roles')->withPivot('role_id')->orderBy('order_number');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function createdBugs()
	{
		return $this->hasMany(Bug::class, 'user_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function clients()
	{
		return $this->belongsToMany(Client::class, 'client_users')->withPivot(['last_active_at', 'login_counter']);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function settings()
	{
		return $this->belongsToMany(Setting::class, 'setting_user_values')->withPivot('value_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function subscription()
	{
		return $this->hasOneThrough(Subscription::class, SubscriptionItem::class);
		// return $this->belongsTo(Subscription::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function subscriptionItem()
	{
		return $this->belongsTo(SubscriptionItem::class);
	}

	/**
	 * Send a password reset notification to the user.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify((new ResetPasswordLinkNotification($this->email, $token))->locale(GetUserLocaleService::getLocale($this)));
	}

	/**
	 * Check if the user is an admin
	 */
	public function isAdministrator()
	{
		return $this->is_admin;
	}

	/**
	 * Check if the user is privilieged to have access to certain resources
	 * E.g.: A user with the role of a company manager should have access to all projects within
	 * that company, eventhough he isn't part of all projects
	 */
	public function isPriviliegated($resourceType, $resource)
	{

		/**
		 * Roles:
		 * | id | designation
		 * |----|----------------------
		 * | 1  | Manager
		 * | 2  | Team
		 * | 3  | Client (e.g. Customer)
		 */

		// Check if the user is an admin
		if ($this->isAdministrator()) {
			return true;
		}

		// Check if the user is the creator of the resource
		if ($resource->user_id == $this->id) {
			return true;
		}

		// Check if the user has a sufficient role within the given resource
		if ($resourceType == 'organizations') {
			// Get users resource role
			return $this->isOwnerOrManagerInResource($this->organizations, $resource);
		} else if ($resourceType == 'companies') {
			// Get users resource role
			return $this->isOwnerOrManagerInResource($this->companies, $resource) || $this->isOwnerOrManagerInResource($this->organizations, $resource->organization);
		} else if ($resourceType == 'projects') {
			// Get users resource role
			return $this->isOwnerOrManagerInResource($this->projects, $resource) || $this->isOwnerOrManagerInResource($this->companies, $resource->company) || $this->isOwnerOrManagerInResource($this->organizations, $resource->company->organization);
		}

		return false;
	}


	// Checks if the given user is a owner or manager in the resource
	private function isOwnerOrManagerInResource($resources, $resource)
	{
		// Check if the user is the creator of the resource
		if ($resource->user_id == $this->id) {
			return true;
		}

		// Check if the resource contains the user
		if ($resource->users->doesntContain($this)) {
			return false;
		}

		$tempResource = $resources->find($resource);
		if (!isset($tempResource) || $tempResource == null) {
			return false;
		}
		$userResourceRoleId = $tempResource->pivot->role_id;

		switch ($userResourceRoleId) {
			case 1:
				return true;
				break;

			case 0:
				return true;
				break;

			default:
				return false;
				break;
		}
	}

	public function trialActive()
	{
		return strtotime($this->trial_end_date) > time();
	}

	public function startTrial($days = 15)
	{
		if ($this->trial_end_date == null) {
			$trialDuration = $days * 24 * 60 * 60;

			$date = gmdate("Y-m-d H:i:s", time() + $trialDuration);
			$this->update([
				"trial_end_date" => $date
			]);
		} else {
			$message = [
				'httpCode' => 403,
				'success' => false,
				'message' => "User already had a trial"
			];
			response()->json($message, 403)->send();
		}
	}

	public function endTrial()
	{
		$date = gmdate("Y-m-d H:i:s", 1);
		$this->update([
			"trial_end_date" => $date
		]);
	}

	public function licenseActive($orgId = null)
	{
		if ($this->trialActive()) {
			return true;
		}

		//get subscriptions of user
		if (isset($orgId)) {
			//allowed to use in another org?
			$licenses = OrganizationUserRole::where("user_id", $this->id)
				->where("subscription_item_id", "!=", null)
				->where("organization_id", $orgId)
				->get();
		} else {
			$licenses = OrganizationUserRole::where("user_id", $this->id)
				->where("subscription_item_id", "!=", null)
				->get();
		}

		return $licenses->count() > 0;
	}
}
