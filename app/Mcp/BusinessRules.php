<?php

namespace App\Mcp;

class BusinessRules
{
    public const ADMIN_ROLES = ['super_admin', 'admin', 'manager'];

    public const READ_SCOPE = 'operations:read';

    public static function availabilityMode(): string
    {
        return config('booking.check_availability') ? 'booking_overlap' : 'status_only';
    }

    public static function limitations(): array
    {
        return [
            'vehicle_status_available_is_not_a_date_calendar' => true,
            'date_overlap_checks_enabled' => (bool) config('booking.check_availability'),
            'availability_mode' => self::availabilityMode(),
            'blocking_statuses' => array_values(config('booking.blocking_statuses', [])),
            'property_reservations_supported' => false,
            'property_workflow' => 'inquiry_only',
            'booking_conversion_rate_supported' => false,
            'booking_conversion_reason' => 'Leads have a manual status of converted and no foreign key to bookings.',
            'collected_revenue_definition' => 'Sum of payments.amount where payments.status is paid. Recognized on payments.updated_at because the payments table has no paid_at column.',
            'booking_totals_are_not_collected_revenue' => true,
            'refunds_supported' => false,
            'quote_formula' => 'BookingCreatorService charges inclusive calendar days times price_per_day, plus flat add-on prices. Tax is that subtotal times booking.tax_rate. Security deposit, cleaning fee, and delivery fee are stored on the vehicle and are not added to bookings.total.',
            'weekly_and_monthly_vehicle_rates_not_used_in_booking_total' => true,
            'currency' => strtoupper((string) config('booking.currency', 'USD')),
            'tax_rate' => (float) config('booking.tax_rate', 0),
            'timezone' => (string) config('app.timezone', 'UTC'),
        ];
    }
}
