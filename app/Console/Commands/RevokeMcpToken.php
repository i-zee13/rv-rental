<?php

namespace App\Console\Commands;

use App\Models\McpAccessToken;
use Illuminate\Console\Command;

class RevokeMcpToken extends Command
{
    protected $signature = 'mcp:revoke {token}';

    protected $description = 'Revoke an MCP token by id or prefix';

    public function handle(): int
    {
        $identifier = (string) $this->argument('token');
        $token = McpAccessToken::query()->where('token_prefix', $identifier)->first();

        if (! $token && ctype_digit($identifier)) {
            $token = McpAccessToken::query()->find($identifier);
        }

        if (! $token) {
            $this->error('Token not found.');

            return self::FAILURE;
        }

        if ($token->revoked_at) {
            $this->info('Token '.$token->id.' is already revoked.');

            return self::SUCCESS;
        }

        $token->forceFill(['revoked_at' => now()])->save();
        $this->info('Revoked token '.$token->id.' ('.$token->token_prefix.').');

        return self::SUCCESS;
    }
}
