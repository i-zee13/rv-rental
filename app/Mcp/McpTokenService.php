<?php

namespace App\Mcp;

use App\Models\McpAccessToken;
use App\Models\User;
use Illuminate\Support\Str;

class McpTokenService
{
    public function issue(string $name, string $workspaceId, int $userId, ?\DateTimeInterface $expiresAt): array
    {
        $user = User::query()->find($userId);

        if (! $user || ! $user->is_active || ! in_array($user->role, BusinessRules::ADMIN_ROLES, true)) {
            throw new \InvalidArgumentException('MCP credentials can only be issued to an active admin, manager, or super_admin.');
        }

        $plain = 'mvmcp_'.Str::random(48);

        $token = McpAccessToken::create([
            'name' => $name,
            'token_hash' => hash('sha256', $plain),
            'token_prefix' => substr($plain, 0, 16),
            'workspace_id' => $workspaceId,
            'user_id' => $user->id,
            'scopes' => [BusinessRules::READ_SCOPE],
            'expires_at' => $expiresAt,
        ]);

        return ['token' => $token, 'plain' => $plain];
    }

    public function authenticate(string $plain): McpAccessToken
    {
        $token = McpAccessToken::query()
            ->where('token_hash', hash('sha256', $plain))
            ->first();

        if (! $token) {
            throw new McpAuthException('Invalid or expired token.', 401, 'invalid_token');
        }

        if ($token->isRevoked()) {
            throw new McpAuthException('Invalid or expired token.', 401, 'revoked');
        }

        if ($token->isExpired()) {
            throw new McpAuthException('Invalid or expired token.', 401, 'expired');
        }

        if (! in_array(BusinessRules::READ_SCOPE, $token->scopes ?? [], true)) {
            throw new McpAuthException('Credential is not authorized.', 403, 'insufficient_scope');
        }

        $user = User::query()->find($token->user_id);

        if (! $user || ! $user->is_active || ! in_array($user->role, BusinessRules::ADMIN_ROLES, true)) {
            throw new McpAuthException('Credential is not authorized.', 403, 'unauthorized_identity');
        }

        $token->forceFill(['last_used_at' => now()])->save();

        return $token;
    }
}
