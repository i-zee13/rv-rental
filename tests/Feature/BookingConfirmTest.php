<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingConfirmTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_reservation_creates_booking_without_customers_table_error(): void
    {
        $vehicle = Vehicle::create([
            'make' => 'Toyota',
            'model' => 'Camry',
            'price_per_day' => 100,
            'status' => 'available',
            'seats' => 5,
            'bags' => 2,
        ]);

        $this->withSession([
            'booking.step1' => [
                'vehicle_id' => $vehicle->id,
                'start_date' => now()->addDay()->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'pickup_location' => 'Miami',
            ],
            'booking.step2' => ['addon_ids' => []],
            'booking.step3' => [
                'first_name' => 'Zeeshan',
                'last_name' => 'Hamza',
                'email' => 'zeeshan@example.com',
                'phone' => '+17860000000',
            ],
        ]);

        $response = $this->post(route('booking.confirm'));

        $response->assertOk();
        $this->assertDatabaseHas('bookings', [
            'email' => 'zeeshan@example.com',
            'vehicle_id' => $vehicle->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('customers', [
            'email' => 'zeeshan@example.com',
        ]);
    }
}
