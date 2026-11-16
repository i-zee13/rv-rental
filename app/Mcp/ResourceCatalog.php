<?php

namespace App\Mcp;

class ResourceCatalog
{
    public function __construct(protected ToolRegistry $tools) {}

    public function list(): array
    {
        return array_map(fn (string $uri) => [
            'uri' => $uri,
            'name' => str_replace('mv://context/', '', $uri),
            'mimeType' => 'application/json',
        ], array_keys($this->documents()));
    }

    public function read(string $uri): array
    {
        $documents = $this->documents();

        if (! isset($documents[$uri])) {
            return [];
        }

        return [[
            'uri' => $uri,
            'mimeType' => 'application/json',
            'text' => json_encode($documents[$uri], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]];
    }

    public function exists(string $uri): bool
    {
        return array_key_exists($uri, $this->documents());
    }

    protected function documents(): array
    {
        $rules = BusinessRules::limitations();

        return [
            'mv://context/overview' => [
                'brand' => 'MV Miami Rental',
                'location' => 'Miami, FL 33122',
                'email' => 'info@mvmiamirental.com',
                'locales' => ['en' => 'English', 'es' => 'Spanish'],
                'business' => 'Vehicle rentals and housing inquiries in Miami.',
                'mcp' => 'Read-only operational access for Aide. The customer OpenAI chatbot is a separate feature.',
            ],
            'mv://context/vehicles' => [
                'categories' => ['cars', 'suvs', 'luxury', 'vans', 'trucks', 'rvs', 'chauffeur', 'subscription'],
                'statuses' => ['available', 'unavailable', 'maintenance'],
                'rates' => 'price_per_day is used for booking totals. price_per_week and price_per_month are display rates.',
                'fees_not_in_booking_total' => ['security_deposit', 'cleaning_fee', 'delivery_fee'],
            ],
            'mv://context/vehicle-availability' => [
                'service' => 'App\\Services\\VehicleAvailabilityService',
                'mode' => $rules['availability_mode'],
                'date_overlap_checks_enabled' => $rules['date_overlap_checks_enabled'],
                'blocking_statuses' => $rules['blocking_statuses'],
                'cancelled_bookings_block' => false,
                'rule' => 'When overlap checks are off, a vehicle with status available is bookable even if another booking covers the dates. calendar_conflict is still reported.',
            ],
            'mv://context/bookings' => [
                'statuses_used_by_the_website' => [
                    'pending' => 'Pay at pickup reservation.',
                    'pending_payment' => 'Stripe Checkout started and not collected.',
                    'confirmed' => 'Stripe payment collected, or an admin set the status.',
                    'cancelled' => 'Admin-cancelled. Does not block availability.',
                ],
                'other_blocking_statuses_configured' => ['active', 'completed'],
                'active_rental' => 'status confirmed or active, and today falls between start_date and end_date inclusive.',
                'rental_days' => 'Inclusive calendar days: end date minus start date plus 1.',
                'tax' => 'Stored on the booking. New quotes use subtotal times booking.tax_rate.',
            ],
            'mv://context/properties' => [
                'types' => ['apartment', 'house', 'condo', 'townhouse', 'villa'],
                'statuses' => ['available', 'unavailable', 'rented', 'hidden'],
                'reservations_supported' => false,
                'workflow' => 'Customers submit a lead. available_from, status, and instant_book do not reserve the property.',
            ],
            'mv://context/leads' => [
                'statuses' => ['new', 'lead', 'contacted', 'qualified', 'converted', 'spam', 'closed'],
                'uncontacted' => 'status new and contacted_at is null.',
                'types' => [
                    'property_inquiry' => 'property_id is set',
                    'vehicle_inquiry' => 'vehicle_id is set and property_id is empty',
                    'general' => 'neither id is set',
                ],
                'booking_conversion_rate_supported' => false,
            ],
            'mv://context/payments' => [
                'provider' => 'stripe',
                'statuses_written_by_the_application' => ['pending', 'paid'],
                'refunds_supported' => false,
                'collected_revenue' => $rules['collected_revenue_definition'],
                'currency' => $rules['currency'],
                'secrets_exposed' => false,
            ],
            'mv://context/business-rules' => $rules,
            'mv://context/capabilities' => [
                'read_only' => true,
                'tools' => $this->tools->names(),
                'not_exposed' => [
                    'booking creation',
                    'cancellation',
                    'refunds',
                    'payment collection',
                    'inventory edits',
                    'lead updates',
                    'customer chatbot replacement',
                    'raw SQL',
                ],
            ],
        ];
    }
}
