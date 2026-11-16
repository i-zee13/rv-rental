<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Mcp\McpServer;
use App\Models\McpAuditLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class McpController extends Controller
{
    public function __invoke(Request $request, McpServer $server): Response
    {
        if (! $request->isMethod('POST')) {
            return response()->json([
                'jsonrpc' => '2.0',
                'id' => null,
                'error' => ['code' => -32000, 'message' => 'Streamable HTTP MCP accepts POST requests only.'],
            ], 405);
        }

        $started = hrtime(true);
        $result = $server->handle((string) $request->getContent());
        $duration = (int) ((hrtime(true) - $started) / 1_000_000);
        $this->audit($request, $result, $duration);

        if ($result['body'] === null) {
            return response('', $result['status']);
        }

        return response()->json($result['body'], $result['status']);
    }

    protected function audit(Request $request, array $result, int $duration): void
    {
        $token = $request->attributes->get('mcp_token');
        $decoded = json_decode((string) $request->getContent(), true);
        $method = is_array($decoded) && is_string($decoded['method'] ?? null) ? $decoded['method'] : null;

        McpAuditLog::create([
            'mcp_access_token_id' => $token?->id,
            'user_id' => $token?->user_id,
            'workspace_id' => $token?->workspace_id,
            'method' => $method ? substr($method, 0, 80) : null,
            'tool' => $result['tool'] ?? null,
            'outcome' => $result['outcome'] ?? 'error',
            'http_status' => $result['status'] ?? 500,
            'ip_address' => $request->ip(),
            'duration_ms' => $duration,
            'message' => $this->redact($result['message'] ?? null),
            'created_at' => now(),
        ]);
    }

    protected function redact(?string $message): ?string
    {
        if ($message === null || $message === '') {
            return null;
        }

        $message = preg_replace('/sk_(live|test)_[A-Za-z0-9]+/', '[redacted]', $message) ?? $message;
        $message = preg_replace('/mvmcp_[A-Za-z0-9]+/', '[redacted]', $message) ?? $message;

        return substr($message, 0, 500);
    }
}
