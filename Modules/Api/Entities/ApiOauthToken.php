<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;

class ApiOauthToken extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'oauth_client_id',
        'user_id',
        'access_token_hash',
        'refresh_token_hash',
        'scopes',
        'expires_at',
        'refresh_expires_at',
        'revoked_at',
        'created_at',
    ];

    protected $casts = [
        'scopes' => 'array',
        'expires_at' => 'datetime',
        'refresh_expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(ApiOauthClient::class, 'oauth_client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
