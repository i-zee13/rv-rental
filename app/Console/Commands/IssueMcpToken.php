<?php

namespace App\Console\Commands;

use App\Mcp\McpTokenService;
use App\Models\User;
use Illuminate\Console\Command;

class IssueMcpToken extends Command
{
    protected $signature = 'mcp:issue {name} {workspace} {--user=} {--days=} {--no-expiry}';

    protected $description = 'Issue a revocable read-only MCP token for an Aide workspace';

    public function handle(McpTokenService $tokens): int
    {
        $workspace = (string) $this->argument('workspace');

        if (! preg_match('/^[A-Za-z0-9._:-]{1,100}$/', $workspace)) {
            $this->error('Workspace id must be 1-100 characters: letters, numbers, dot, underscore, colon, or hyphen.');

            return self::FAILURE;
        }

        $user = $this->resolveUser((string) $this->option('user'));

        if (! $user) {
            $this->error('Pass --user with the email or id of an active admin, manager, or super_admin.');

            return self::FAILURE;
        }

        $expiresAt = null;

        if (! $this->option('no-expiry')) {
            $days = $this->option('days') === null || $this->option('days') === ''
                ? (int) config('mcp.token_ttl_days', 90)
                : (int) $this->option('days');

            if ($days < 1 || $days > 365) {
                $this->error('Days must be between 1 and 365, or pass --no-expiry.');

                return self::FAILURE;
            }

            $expiresAt = now()->addDays($days);
        }

        try {
            $issued = $tokens->issue((string) $this->argument('name'), $workspace, $user->id, $expiresAt);
        } catch (\InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->warn('Store this token in Aide. It will not be shown again.');
        $this->line('Token: '.$issued['plain']);
        $this->line('Id: '.$issued['token']->id);
        $this->line('Prefix: '.$issued['token']->token_prefix);
        $this->line('Workspace: '.$issued['token']->workspace_id);
        $this->line('User: '.$user->email);
        $this->line('Expires: '.($issued['token']->expires_at?->utc()->format('Y-m-d\TH:i:s\Z') ?? 'never'));

        return self::SUCCESS;
    }

    protected function resolveUser(string $identifier): ?User
    {
        if ($identifier === '') {
            return null;
        }

        $user = User::query()->where('email', $identifier)->first();

        if (! $user && ctype_digit($identifier)) {
            $user = User::query()->find($identifier);
        }

        return $user;
    }
}
