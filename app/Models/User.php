<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordLinkNotification;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema()
 */

class User extends Authenticatable implements MustVerifyEmail
{
	use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'password',
		'email_verified_at',
	];

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
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user_roles')->withPivot('role_id');
    }

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user_roles')->withPivot('role_id')->orderBy('updated_at', 'desc');
    }

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bugs()
    {
        return $this->belongsToMany(Bug::class, 'bug_user_roles')->withPivot('role_id')->orderBy('order_number');
    }

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_users')->withPivot(['last_active_at', 'login_counter']);
    }

	/**
	 * Send a password reset notification to the user.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
	    $url = route('password.update') . '?token=' . $token;

	    $this->notify(new ResetPasswordLinkNotification($url));
	}

	/**
	 * Check if the user is an admin
	 */
	public function isAdministrator() {
		return $this->is_admin;
	 }

}

