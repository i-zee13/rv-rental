<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Vehicle;
use App\Models\Addon;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_flow_creates_booking()
    {
        // seed a vehicle and addon
        $vehicle = Vehicle::factory()->create(['price_per_day' => 100, 'status' => 'available']);
        $addon = Addon::factory()->create(['price' => 20]);

        // step1
        $resp = $this->post(route('booking.postStep1'), [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
        ]);
        $resp->assertRedirect(route('booking.step2'));

        // step2
        $resp = $this->post(route('booking.postStep2'), ['addon_ids' => [$addon->id]]);
        $resp->assertRedirect(route('booking.step3'));

        // step3
        $resp = $this->post(route('booking.postStep3'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com'
        ]);
        $resp->assertRedirect(route('booking.step4'));

        // confirm (no payment)
        $resp = $this->post(route('booking.confirm'));
        $resp->assertStatus(200);
        $this->assertDatabaseCount('bookings', 1);
    }
}
