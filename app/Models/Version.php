<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $client_id
 * @property integer $version_type_id
 * @property string $designation
 * @property string $description
 * @property boolean $supported
 * @property string $created_at
 * @property string $updated_at
 * @property Client $client
 * @property VersionType $versionType
 */
class Version extends Model
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
    protected $fillable = ['client_id', 'version_type_id', 'designation', 'description', 'supported', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function versionType()
    {
        return $this->belongsTo(VersionType::class);
    }
}
