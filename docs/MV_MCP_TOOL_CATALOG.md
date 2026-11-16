# MV MCP tool catalog

Every tool requires a non-revoked, unexpired bearer token and an active `super_admin`, `admin`, or `manager`. Workspace headers are not required. All tools are read-only.

There are **11 tools**. Extra specialty tools were removed; use filters on list tools instead.

Dates are `YYYY-MM-DD` (`UTC`). Money is `{ "amount": number, "currency": "USD" }`. Payloads include `source` and `retrieved_at`. Pagination defaults to page 1 / 25 (max 50).

## Tools

| Tool | Use for |
| --- | --- |
| `mv_get_business_overview` | Snapshot: vehicles, bookings, paid revenue, leads |
| `mv_list_vehicles` | Fleet list (category, status, price, search) |
| `mv_get_vehicle` | One vehicle detail (no VIN) |
| `mv_check_vehicle_availability` | Date range availability via `VehicleAvailabilityService` |
| `mv_list_bookings` | Bookings; `payment_state`, status, rental/created dates |
| `mv_get_booking` | One booking + customer contact |
| `mv_list_properties` | Housing listings (inquiry-only, no reservations) |
| `mv_get_property` | One property + address |
| `mv_list_leads` | Leads; `lead_type`, status, `contacted` |
| `mv_get_lead` | One lead + customer contact |
| `mv_get_revenue_summary` | Paid payments vs booking totals for a period |

## Filter shortcuts

- Unpaid / pay-at-pickup → `mv_list_bookings` + `payment_state`
- Active rental today → `mv_list_bookings` + `status=confirmed` + `rental_from`/`rental_to` = today
- Property inquiries → `mv_list_leads` + `lead_type=property_inquiry`
- Uncontacted → `mv_list_leads` + `status=new` + `contacted=false`

Contact emails appear only on `mv_get_booking` and `mv_get_lead`. Booking totals are not collected revenue.
