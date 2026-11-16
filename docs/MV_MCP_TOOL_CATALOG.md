# MV MCP tool catalog

Every tool requires a non-revoked, unexpired token with scope `operations:read`, an `X-Aide-Workspace-Id` matching that token, and an active user in `super_admin`, `admin`, or `manager`.

All tools are read-only. List responses omit customer email, phone, and street address. `mv_get_booking` and `mv_get_lead` return contact details. `mv_get_property` returns the street address.

Dates are `YYYY-MM-DD` in the application timezone (`UTC`). Money objects are `{ "amount": number, "currency": "USD" }`. Every payload includes `source: "mv_miami_rental"` and `retrieved_at`. Pagination defaults to page 1 and 25 rows, maximum 50, and sets `has_more`.

Omitted `from` means the first day of the `to` month. Omitted `to` means today. Both ends are inclusive.

## Business

### `mv_get_business_overview`

Period counts: vehicles, vehicles with status `available`, bookings created, pending bookings, paid revenue, new leads, property inquiries, vehicle inquiries, properties.

`vehicles_with_status_available` is not date availability. `booking_conversion_rate` is not returned.

Questions: current business snapshot, bookings created today, paid revenue this month, new leads, property inquiries.

## Vehicles

### `mv_list_vehicles`

Filters: `category` slug, `status`, `min_price`, `max_price` (daily rate), `search`, `page`, `per_page`.

### `mv_get_vehicle`

`id` or `slug`. Includes deposit, cleaning, and delivery fees with a note that they are not in the booking total. No VIN.

### `mv_check_vehicle_availability`

Required: `vehicle_id`, `start`, `end`.

`available` matches `VehicleAvailabilityService`. `calendar_conflict` is true when a blocking-status booking overlaps, even if the website is not enforcing dates. Read `date_overlap_enforced` before telling a user the vehicle is free.

### `mv_get_fleet_summary`

Counts by category and status. Not a calendar.

## Bookings

### `mv_list_bookings`

Filters: `status`, `payment_state` (`paid`, `pending`, `pay_at_pickup`, `unpaid`), `vehicle_id`, `email`, `created_from`, `created_to`, `rental_from`, `rental_to`, pagination.

`created_*` filters `created_at`. `rental_*` filters bookings whose dates overlap that range. The email filter does not add email to the list payload.

### `mv_get_booking`

`id` or `reference`. Contact, notes, add-ons, and payment status. No Stripe ids.

### `mv_get_upcoming_bookings`

`start_date` today or later, status `pending`, `pending_payment`, `confirmed`, or `active`. Optional `within_days`.

### `mv_get_active_rentals`

Status `confirmed` or `active`, and today is inside the rental dates. Pending checkout is not an active rental.

### `mv_get_booking_statistics`

Counts and stored totals by status for bookings created in the period. These totals are not collected cash.

## Properties

### `mv_list_properties`

Filters: `type` slug, `status`, `bedrooms_min`, `bathrooms_min`, `pets_allowed`, `furnished`, `min_nights_max`, `min_price`, `max_price` on monthly price, `search`. No street address. `reservations_supported` is false.

### `mv_get_property`

`id` or `slug`. Address, amenities, and the same reservation limitation.

### `mv_get_property_inquiries`

Leads with `property_id`. Optional `property_id`, `status`, `created_from`, `created_to`. Contact details stay on `mv_get_lead`.

There is no property booking tool.

## Leads

### `mv_list_leads`

Filters: `status`, `source`, `lead_type` (`property_inquiry`, `vehicle_inquiry`, `general`), `vehicle_id`, `property_id`, `contacted`, created dates.

### `mv_get_lead`

`id` or `reference`. Includes contact and message. Omits IP and user agent.

### `mv_get_lead_statistics`

Counts by status, source, and type. `booking_conversion_rate` is null and `booking_conversion_rate_available` is false.

### `mv_get_uncontacted_leads`

Status `new` and `contacted_at` null.

## Payments

### `mv_get_payment_summary`

Counts and sums by payment status. Without dates, the scope is all payment rows. With dates, rows are filtered on `updated_at`.

### `mv_get_revenue_summary`

`collected_revenue` is paid payment amounts in the period. `booking_totals_created_in_period` is separate and includes unpaid reservations.

### `mv_list_pending_payments`

Bookings in `pending`, `pending_payment`, `confirmed`, or `active` with no payment in status `paid`. `payment_state` distinguishes pay-at-pickup from an unfinished Stripe checkout.

## Content

### `mv_search_faqs`

Required `query`, at least 2 characters. Active FAQs only, English and Spanish.

### `mv_get_site_information`

Brand, location, public email, locales, tax rate, availability mode, whether Stripe checkout is configured, published page titles, and published blog count. No secrets and no page HTML.
