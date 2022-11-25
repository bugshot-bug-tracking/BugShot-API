<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $designation
 * @property string $client_url
 * @property string $client_key
 * @property string $created_at
 * @property string $updated_at
 * @property UserClient[] $userClients
 * @property Version[] $versions
 */
class Client extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['designation','client_url', 'client_key', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'client_users')->withPivot(['last_active_at', 'login_counter']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function versions()
    {
        return $this->hasMany(Version::class);
    }
}
