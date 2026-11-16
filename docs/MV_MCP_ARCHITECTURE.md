# MV Miami Rental MCP architecture

The MCP connector is a read-only Streamable HTTP endpoint inside this Laravel app. Aide calls it with a revocable token. It does not replace the customer OpenAI chatbot, and it does not create bookings, leads, payments, or inventory changes.

## Request path

`POST /mcp` is registered outside the `web` middleware group.

1. `EnsureMcpEnabled` reads `config('mcp.enabled')`. When it is false the response is HTTP 404. Authentication, tool code, business queries, and audit writes do not run.
2. `AuthenticateMcpToken` checks the bearer token, the `X-Aide-Workspace-Id` header, the bound admin user, and the rate limit.
3. `McpController` handles one JSON-RPC message and writes `mcp_audit_logs`.

`GET` and `DELETE` use the same gate. When MCP is enabled they return HTTP 405. This server does not open a server-sent-events stream and does not issue an `Mcp-Session-Id`. Each request is stateless.

## Protocol

Implemented JSON-RPC methods:

- `initialize`
- `notifications/initialized` (HTTP 202, empty body)
- `ping`
- `tools/list`
- `tools/call`
- `resources/list`
- `resources/read`

Protocol version returned: `2025-03-26`. Successful tool calls include both `content` text and `structuredContent`. Invalid tool arguments return HTTP 200 with `isError: true`. Unknown methods return JSON-RPC `-32601`. Batch messages are rejected.

The application did not already depend on an MCP library. The handler lives in `app/Mcp` so the disabled flag can reject `/mcp` before any business service is resolved. No booking, payment, lead, or chat class was edited to add this.

## Authentication

Tokens are issued only by Artisan. The plaintext value is shown once. The database stores `hash('sha256', $plain)`, which is appropriate for a high-entropy random token and allows lookup. Passwords are not stored this way.

Each token is bound to:

- one Aide workspace id
- one MV user whose role is `super_admin`, `admin`, or `manager` and whose `is_active` is true
- the scope `operations:read`

The workspace header must match the token. A workspace id by itself is not authorization. Customer accounts, including `booking_agent`, cannot use MCP. There is no website screen that mints tokens.

Deactivating the user, changing the role, or setting `revoked_at` stops the token. Expiry is checked on every request. Default life is 90 days.

Rate limits, per minute:

- `MCP_RATE_LIMIT` (default 60) for authenticated requests, keyed by token
- `MCP_AUTH_RATE_LIMIT` (default 20) for failed authentication, keyed by IP

Audit rows store the tool name, outcome, status, and IP. They do not store the bearer token, tool arguments, or payment metadata.

## Business reads

Tools call Eloquent and the existing `VehicleAvailabilityService`. They do not recalculate a new checkout and they do not call Stripe or OpenAI.

`available` on a vehicle availability result is exactly `VehicleAvailabilityService::isVehicleBookable()`. `calendar_conflict` is `hasOverlap()` and is reported even when `BOOKING_CHECK_AVAILABILITY` is false, so a status of `available` is not presented as proof that the dates are free.

Collected revenue is the sum of `payments.amount` where `status` is `paid`. Booking `total` is reported separately. Properties have no reservation calendar; tools expose listings and leads only.

## Configuration

`config/mcp.php`

| Key | Default |
| --- | --- |
| `MCP_ENABLED` | `false` |
| `MCP_RATE_LIMIT` | `60` |
| `MCP_AUTH_RATE_LIMIT` | `20` |
| `MCP_TOKEN_TTL_DAYS` | `90` |

The string `"false"` is parsed with `FILTER_VALIDATE_BOOLEAN`, so it does not enable the endpoint.

## New tables

`mcp_access_tokens` and `mcp_audit_logs` are created by `database/migrations/2026_11_16_000001_create_mcp_tables.php`. No business table is altered by that migration. Do not run it on production until you intend to enable the connector.
