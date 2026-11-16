<?php

namespace App\Mcp;

class McpServer
{
    public function __construct(
        protected ToolRegistry $tools,
        protected ResourceCatalog $resources,
    ) {}

    public function handle(string $raw): array
    {
        try {
            $payload = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $this->error(null, -32700, 'Parse error.', 400, 'error', null);
        }

        if (! is_array($payload) || array_is_list($payload)) {
            return $this->error(null, -32600, 'Invalid request. Batch messages are not supported.', 200, 'error', null);
        }

        $id = $payload['id'] ?? null;
        $isNotification = ! array_key_exists('id', $payload);
        $method = $payload['method'] ?? null;

        if (($payload['jsonrpc'] ?? null) !== '2.0' || ! is_string($method)) {
            return $this->error($isNotification ? null : $id, -32600, 'Invalid request.', 200, 'error', null);
        }

        if ($isNotification || str_starts_with($method, 'notifications/')) {
            return ['status' => 202, 'body' => null, 'outcome' => 'accepted', 'tool' => null, 'message' => null];
        }

        try {
            $result = match ($method) {
                'initialize' => $this->initialize(),
                'ping' => (object) [],
                'tools/list' => ['tools' => $this->tools->list()],
                'tools/call' => $this->callTool($payload['params'] ?? []),
                'resources/list' => ['resources' => $this->resources->list()],
                'resources/read' => $this->readResource($payload['params'] ?? []),
                default => null,
            };
        } catch (ToolInputException|UnknownMcpToolException $exception) {
            if ($method === 'resources/read') {
                return $this->error($id, -32002, $exception->getMessage(), 200, 'error', null);
            }

            return [
                'status' => 200,
                'body' => ['jsonrpc' => '2.0', 'id' => $id, 'result' => $this->toolError($exception->getMessage())],
                'outcome' => 'error',
                'tool' => $this->toolName($payload),
                'message' => $exception->getMessage(),
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->error($id, -32603, 'Internal error.', 200, 'error', $this->toolName($payload));
        }

        if ($result === null) {
            return $this->error($id, -32601, 'Method not found.', 200, 'error', null);
        }

        $isError = is_array($result) && ($result['isError'] ?? false) === true;

        return [
            'status' => 200,
            'body' => ['jsonrpc' => '2.0', 'id' => $id, 'result' => $result],
            'outcome' => $isError ? 'error' : 'success',
            'tool' => $this->toolName($payload),
            'message' => $isError ? (string) ($result['structuredContent']['error'] ?? 'Tool error') : null,
        ];
    }

    protected function initialize(): array
    {
        return [
            'protocolVersion' => config('mcp.protocol_version'),
            'capabilities' => [
                'tools' => ['listChanged' => false],
                'resources' => ['listChanged' => false],
            ],
            'serverInfo' => [
                'name' => config('mcp.server_name'),
                'version' => config('mcp.server_version'),
            ],
            'instructions' => 'Read-only MV Miami Rental operations. Vehicle status is not date availability unless date_overlap_enforced is true. Booking totals are not collected revenue. Properties are inquiry-only.',
        ];
    }

    protected function callTool(mixed $params): array
    {
        if (! is_array($params) || ! isset($params['name']) || ! is_string($params['name'])) {
            throw new ToolInputException('Tool name is required.');
        }

        $arguments = $params['arguments'] ?? [];
        if (! is_array($arguments) || array_is_list($arguments) && $arguments !== []) {
            throw new ToolInputException('Tool arguments must be an object.');
        }

        try {
            $structured = $this->tools->call($params['name'], $arguments);
        } catch (UnknownMcpToolException|ToolInputException $exception) {
            return $this->toolError($exception->getMessage());
        }

        return [
            'content' => [[
                'type' => 'text',
                'text' => json_encode($structured, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            ]],
            'structuredContent' => $structured,
            'isError' => false,
        ];
    }

    protected function readResource(mixed $params): array
    {
        $uri = is_array($params) ? ($params['uri'] ?? null) : null;

        if (! is_string($uri) || ! $this->resources->exists($uri)) {
            throw new ToolInputException('Resource not found.');
        }

        return ['contents' => $this->resources->read($uri)];
    }

    protected function toolError(string $message): array
    {
        return [
            'content' => [['type' => 'text', 'text' => $message]],
            'structuredContent' => [
                'source' => 'mv_miami_rental',
                'error' => $message,
            ],
            'isError' => true,
        ];
    }

    protected function error(mixed $id, int $code, string $message, int $status, string $outcome, ?string $tool): array
    {
        return [
            'status' => $status,
            'body' => [
                'jsonrpc' => '2.0',
                'id' => $id,
                'error' => ['code' => $code, 'message' => $message],
            ],
            'outcome' => $outcome,
            'tool' => $tool,
            'message' => $message,
        ];
    }

    protected function toolName(array $payload): ?string
    {
        if (($payload['method'] ?? null) !== 'tools/call') {
            return null;
        }

        $name = $payload['params']['name'] ?? null;

        return is_string($name) ? substr($name, 0, 80) : null;
    }
}
