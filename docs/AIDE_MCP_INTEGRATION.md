# Aide MCP integration

Aide is not connected by this repository. This document is the contract for an Aide worker that calls MV Miami Rental. The TypeScript example is `examples/aide/mv-mcp-client.ts`. It uses `fetch` and reads credentials from the caller. Do not put the token in a prompt or in source control.

## Endpoint

`POST {APP_URL}/mcp`

Transport: Streamable HTTP, one JSON-RPC message per POST, response `application/json`. Notifications return HTTP 202 with an empty body. There is no session id and no SSE stream.

Send:

- `Authorization: Bearer <token>`
- `Accept: application/json, text/event-stream`
- `Content-Type: application/json`

Workspace headers (`X-Aide-Workspace-Id`, `X-Wwt-Workspace`) are optional and ignored for authorization. Auth is token-only.

The server stays dark until `MCP_ENABLED=true`. Until then `/mcp` is HTTP 404.

## Issue and revoke

On the app server, after the MCP migration has been applied:

```bash
php artisan mcp:issue "Aide" "ws_your_workspace" --user=admin@example.com
php artisan mcp:tokens
php artisan mcp:revoke 1
```

`--user` is the email or id of an active `super_admin`, `admin`, or `manager`. Tokens never expire by default. Pass `--days=90` only if you want an expiry. The command prints the token once. Store it in Aide. Revoke with `mcp:revoke` when access should end.

Revoke accepts the numeric id or the token prefix. Revocation takes effect on the next request. Deactivating the MV user also blocks the token.

The workspace argument on `mcp:issue` is only a label for `mcp:tokens`. Requests authenticate with the bearer token alone.

## Health

1. `initialize` returns `serverInfo.name` `mv-miami-rental` and protocol `2025-03-26`.
2. Send `notifications/initialized` with no `id`.
3. `tools/list` returns the catalog.
4. `tools/call` `mv_get_site_information` returns `source` `mv_miami_rental`.

HTTP 401 means the token is missing, wrong, expired, or revoked. The message is the same for those cases. HTTP 403 means the workspace or the MV user is not allowed. HTTP 429 means the token or the IP exceeded the per-minute limit. Refresh a revoked or expired token by issuing a new one. Do not retry 401s in a loop.

## Questions the tools can answer

| Question | Tool |
| --- | --- |
| What is the business snapshot this month? | `mv_get_business_overview` |
| Which vehicles are in the fleet, by category or status? | `mv_list_vehicles`, `mv_get_fleet_summary` |
| Is this vehicle bookable on these dates under the current website rules? | `mv_check_vehicle_availability` |
| Which bookings were created, are upcoming, or are out today? | `mv_list_bookings`, `mv_get_upcoming_bookings`, `mv_get_active_rentals` |
| How much cash was collected, as opposed to booking totals? | `mv_get_revenue_summary` |
| Which reservations are unpaid or waiting on Stripe? | `mv_list_pending_payments` |
| Which leads are new, and which are property inquiries? | `mv_list_leads`, `mv_get_uncontacted_leads`, `mv_get_property_inquiries` |
| Which homes allow pets? | `mv_list_properties` with `pets_allowed` |
| What does a published FAQ say? | `mv_search_faqs` |

Read `limitations` on the overview before answering. In particular:

- Do not treat vehicle status `available` as free for specific dates unless `date_overlap_enforced` is true and `calendar_conflict` is false.
- Do not call booking totals revenue.
- Do not invent a booking conversion rate. `booking_conversion_rate_available` is false.
- Do not offer property reservation availability. Housing is inquiry-only.

## Client

```ts
import { createMvMcpClient } from "./mv-mcp-client";

const client = createMvMcpClient({
  endpoint: env.MV_MCP_URL,
  token: env.MV_MCP_TOKEN,
});

await client.initialize();
const overview = await client.callTool("mv_get_business_overview", {});
```

`callTool` returns `{ ok: false, error }` when the tool payload has `isError: true`. Network and auth failures throw.

## Production

1. Deploy the code with `MCP_ENABLED=false`.
2. Run `php artisan migrate` only when you accept the two new tables.
3. Issue a token bound to the Aide workspace and an admin user.
4. Set `MCP_ENABLED=true`.
5. Confirm `/mcp` without a token returns 401, and the customer site, checkout, and `/booking-chat/start` still behave as before.

Set the flag back to false to stop the connector without removing routes or data. A disabled endpoint does not query bookings or payments.

## Limits

- Read-only. No create, cancel, refund, or lead update tools.
- No raw SQL, no Stripe ids, no VIN, no chatbot transcripts.
- Contact details only from `mv_get_booking` and `mv_get_lead`.
- Revenue recognition is `payments.updated_at`, because there is no `paid_at`.
- Overlap enforcement follows `BOOKING_CHECK_AVAILABILITY`.
- Aide's own workspace auth must keep the token server-side. This app cannot see Aide's users.
