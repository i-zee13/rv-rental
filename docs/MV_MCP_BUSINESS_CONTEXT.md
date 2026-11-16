# MV Miami Rental business context

Verified from the application source. The app timezone is `UTC`. Currency is `config('booking.currency')`, default `USD`. Locales are `en` and `es`.

MV Miami Rental sells vehicle rentals and accepts housing inquiries in Miami, FL 33122. The public contact is `info@mvmiamirental.com`.

## Vehicles

A vehicle has id, slug, category, make, model, year, seats, doors, bags, transmission, fuel type, and status.

Admin statuses are `available`, `unavailable`, and `maintenance`.

Seeded categories: `cars`, `suvs`, `luxury`, `vans`, `trucks`, `rvs`, `chauffeur`, `subscription`.

Rates stored on the vehicle:

- `price_per_day` is the rate `BookingCreatorService` uses
- `price_per_week` and `price_per_month` are display rates and are not used in the booking total
- `security_deposit`, `cleaning_fee`, and `delivery_fee` are listing fields and are not added to `bookings.subtotal`, `taxes`, or `total`

VIN is stored and is not returned by MCP.

## Vehicle availability

`VehicleAvailabilityService`:

- When `BOOKING_CHECK_AVAILABILITY` is false (the default), a vehicle is bookable when `status` is `available`. Overlapping bookings do not block the website.
- When it is true, the vehicle must be `available` and must not overlap a booking whose status is in `booking.blocking_statuses`.

Default blocking statuses: `pending`, `pending_payment`, `confirmed`, `active`, `completed`. `cancelled` does not block.

Overlap is inclusive of the stored `start_date` and `end_date`. MCP returns both the website decision (`available`) and whether a blocking booking exists (`calendar_conflict`).

## Bookings

A booking is a vehicle reservation. Fields used by the website include reference, vehicle, customer, `start_date`, `end_date`, pickup and dropoff text, contact fields, add-ons, `subtotal`, `taxes`, `total`, currency, and status.

Rental length for a new quote is inclusive: end date minus start date, plus 1, minimum 1. Base charge is that day count times `price_per_day`. Add-ons are flat prices. Tax is the subtotal times `booking.tax_rate` (environment `TAX_RATE`, default `0.10`). MCP does not recompute stored totals.

Statuses written by the website:

| Status | Meaning |
| --- | --- |
| `pending` | Reserved to pay at pickup. No Stripe payment row. |
| `pending_payment` | Stripe Checkout was started. A payment row is `pending` until the webhook or return marks it `paid`. |
| `confirmed` | Stripe marked the booking paid, or an admin set the status. Confirmation alone is not proof of collected cash. |
| `cancelled` | Admin status. It does not block availability. |

`active` and `completed` are blocking statuses in config. The admin form only offers pending, confirmed, and cancelled, and nothing in the app automatically moves a booking to `active`.

An active rental in MCP means status `confirmed` or `active`, and today is between `start_date` and `end_date` inclusive.

## Payments

`StripeCheckoutService` creates a Checkout Session and a `payments` row with provider `stripe`, status `pending`, and the session id in `provider_id`. After a paid session, status becomes `paid` and the booking becomes `confirmed`.

There is no `paid_at` column. Collected revenue uses `payments.updated_at` for rows with status `paid`.

The application does not create refund rows. A `refunded` or `failed` status would only appear if it was written outside this code.

MCP never returns `provider_id`, `meta`, card data, or Stripe secrets. `stripe_checkout_configured` is only a boolean.

Pay-at-pickup bookings have status `pending` and no payment row. Their `total` is not collected revenue.

## Leads

Lead statuses: `new`, `lead`, `contacted`, `qualified`, `converted`, `spam`, `closed`.

`contacted_at` is the follow-up timestamp. Uncontacted means status `new` and `contacted_at` is null. Status `lead` is a later stage.

Type, derived from foreign keys:

- `property_inquiry`: `property_id` is set
- `vehicle_inquiry`: `vehicle_id` is set and `property_id` is empty
- `general`: neither is set

`converted` is an admin label. There is no booking foreign key on leads, so MCP does not calculate a booking conversion rate.

IP address and user agent are stored and are not returned.

## Properties

Types: `apartment`, `house`, `condo`, `townhouse`, `villa`.

Listing statuses: `available`, `unavailable`, `rented`, `hidden`.

A property has bedrooms, bathrooms, size, guests, minimum nights, nightly, weekly, and monthly prices, amenities, furnished, pets, and `available_from`.

`instant_book` and `available_from` do not create a reservation. The public property page submits a lead. The chatbot also creates a lead for a property, not a booking. There is no property availability calendar.

## Content

FAQs are active rows with English and Spanish translations, scoped to general pages, vehicles, or properties. Pages have `is_published`. Blog posts use `draft` or `published`. MCP site information returns titles and counts, not page HTML.
