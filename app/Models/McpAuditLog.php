<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McpAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'mcp_access_token_id',
        'user_id',
        'workspace_id',
        'method',
        'tool',
        'outcome',
        'http_status',
        'ip_address',
        'duration_ms',
        'message',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
