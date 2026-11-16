<?php

namespace App\Http\Middleware;

use App\Mcp\McpAuthException;
use App\Mcp\McpTokenService;
use App\Models\McpAuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMcpToken
{
    public function __construct(protected McpTokenService $tokens) {}

    public function handle(Request $request, Closure $next): Response
    {
        $ipKey = 'mcp-auth-fail:'.$request->ip();
        $authLimit = max(1, (int) config('mcp.auth_failures_per_minute', 20));

        if (RateLimiter::tooManyAttempts($ipKey, $authLimit)) {
            $this->audit($request, null, 429, 'rate_limited', 'Too many authentication attempts.');

            return $this->error(429, 'Too many authentication attempts.');
        }

        $plain = $request->bearerToken();

        if (! is_string($plain) || $plain === '') {
            RateLimiter::hit($ipKey, 60);
            $this->audit($request, null, 401, 'missing_token', 'Authentication required.');

            return $this->error(401, 'Authentication required.');
        }

        try {
            $token = $this->tokens->authenticate($plain);
        } catch (McpAuthException $exception) {
            RateLimiter::hit($ipKey, 60);
            $this->audit($request, null, $exception->status, $exception->outcome, $exception->getMessage());

            return $this->error($exception->status, $exception->getMessage());
        }

        $toolKey = 'mcp-tool:'.$token->id;
        $toolLimit = max(1, (int) config('mcp.rate_limit_per_minute', 60));

        if (RateLimiter::tooManyAttempts($toolKey, $toolLimit)) {
            $this->audit($request, $token, 429, 'rate_limited', 'Rate limit exceeded.');

            return $this->error(429, 'Rate limit exceeded.');
        }

        RateLimiter::hit($toolKey, 60);
        $request->attributes->set('mcp_token', $token);

        return $next($request);
    }

    protected function error(int $status, string $message): Response
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'id' => null,
            'error' => ['code' => -32000, 'message' => $message],
        ], $status);
    }

    protected function audit(Request $request, mixed $token, int $status, string $outcome, string $message): void
    {
        McpAuditLog::create([
            'mcp_access_token_id' => $token?->id,
            'user_id' => $token?->user_id,
            'workspace_id' => $token?->workspace_id,
            'method' => 'auth',
            'tool' => null,
            'outcome' => $outcome,
            'http_status' => $status,
            'ip_address' => $request->ip(),
            'message' => substr($message, 0, 500),
            'created_at' => now(),
        ]);
    }
}
