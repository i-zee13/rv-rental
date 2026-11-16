<?php

namespace App\Console\Commands;

use App\Models\McpAccessToken;
use Illuminate\Console\Command;

class ListMcpTokens extends Command
{
    protected $signature = 'mcp:tokens';

    protected $description = 'List MCP token metadata without secrets';

    public function handle(): int
    {
        $rows = McpAccessToken::query()->orderByDesc('id')->get()->map(fn (McpAccessToken $token) => [
            $token->id,
            $token->name,
            $token->token_prefix,
            $token->workspace_id,
            $token->user_id,
            $token->expires_at?->utc()->format('Y-m-d\TH:i:s\Z') ?? 'never',
            $token->revoked_at ? 'revoked' : 'active',
            $token->last_used_at?->utc()->format('Y-m-d\TH:i:s\Z') ?? '',
        ]);

        $this->table(
            ['Id', 'Name', 'Prefix', 'Workspace', 'User', 'Expires', 'State', 'Last used'],
            $rows->all(),
        );

        return self::SUCCESS;
    }
}
