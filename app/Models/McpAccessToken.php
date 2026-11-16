<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class McpAccessToken extends Model
{
    protected $fillable = [
        'name',
        'token_hash',
        'token_prefix',
        'workspace_id',
        'user_id',
        'scopes',
        'expires_at',
        'revoked_at',
        'last_used_at',
    ];

    protected $casts = [
        'scopes' => 'array',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
