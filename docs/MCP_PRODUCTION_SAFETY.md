# MCP production safety

The connector is not enabled and no production migration was run.

## Existing files touched

| File | Change |
| --- | --- |
| `bootstrap/app.php` | Loads `routes/mcp.php` next to the existing deploy routes. |
| `.env.example` | Documents `MCP_ENABLED=false` and the rate-limit settings. |
| `phpunit.xml` | Forces `MCP_ENABLED=false` in tests. |
| `database/migrations/2026_06_20_000001_widen_image_path_columns.php` | Skips the MySQL-only `ALTER TABLE ... MODIFY` when the driver is not MySQL. SQLite tests can migrate. MySQL still runs the same statements. |

No booking, payment, lead, Stripe, email, locale, or OpenAI chat file was modified.

## Added

- `routes/mcp.php`
- `config/mcp.php`
- `app/Mcp/*`
- `app/Http/Controllers/Mcp/McpController.php`
- `app/Http/Middleware/EnsureMcpEnabled.php`
- `app/Http/Middleware/AuthenticateMcpToken.php`
- `app/Console/Commands/IssueMcpToken.php`, `RevokeMcpToken.php`, `ListMcpTokens.php`
- `app/Models/McpAccessToken.php`, `McpAuditLog.php`
- `database/migrations/2026_11_16_000001_create_mcp_tables.php`
- `tests/Feature/McpServerTest.php`
- `examples/aide/mv-mcp-client.ts`
- these docs

The new migration creates `mcp_access_tokens` and `mcp_audit_logs` only.

## Tests

`php artisan test --filter=McpServerTest`: 12 passed.

`php artisan test`: 33 tests, 31 passed. The two failures are not caused by the connector:

- `ExampleTest` requests `/` against the phpunit `:memory:` database and does not migrate, so `vehicles` does not exist.
- `BookingFlowTest` calls `Vehicle::factory()` and `database/factories/VehicleFactory.php` does not exist.

Feature tests that do migrate, including booking chat, lead submission, booking confirmation, and SEO, passed. The chat test still reaches `/booking-chat/start` with MCP enabled in `McpServerTest`.

## Risks before production

- Turn the flag on only after `php artisan migrate`. Otherwise token lookup will error.
- A token can read booking and lead contact details through the two detail tools. Keep it in Aide's secret store.
- With the default availability flag off, `available: true` means the vehicle status is `available`. Use `calendar_conflict` and `date_overlap_enforced`.
- Paid revenue follows `payments.updated_at`, not a dedicated paid timestamp.
- This work was not deployed.
