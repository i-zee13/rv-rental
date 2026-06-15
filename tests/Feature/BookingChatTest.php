<?php

namespace Tests\Feature;

use App\Mail\BookingAdminNotificationMail;
use App\Mail\BookingCustomerConfirmationMail;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_chat_starts_and_lists_vehicles(): void
    {
        $this->seed(\Database\Seeders\VehiclesSeeder::class);

        $response = $this->postJson(route('booking-chat.start'));

        $response->assertOk();
        $response->assertJsonStructure([
            'step',
            'messages',
            'options',
            'actions',
            'completed',
        ]);
        $response->assertJsonPath('step', 'vehicle');
        $this->assertNotEmpty($response->json('options'));
    }

    public function test_booking_chat_confirm_creates_booking(): void
    {
        $vehicle = Vehicle::create([
            'make' => 'Toyota',
            'model' => 'Camry',
            'price_per_day' => 100,
            'status' => 'available',
            'seats' => 5,
            'bags' => 2,
        ]);

        session([
            'booking_chat' => [
                'step' => 'confirm',
                'data' => [
                    'vehicle_id' => $vehicle->id,
                    'start_date' => now()->addDay()->toDateString(),
                    'end_date' => now()->addDays(3)->toDateString(),
                    'pickup_location' => 'Miami',
                    'dropoff_location' => 'Miami',
                    'addon_ids' => [],
                    'first_name' => 'Zeeshan',
                    'last_name' => 'Hamza',
                    'email' => 'zeeshan@example.com',
                    'phone' => null,
                    '_addons_done' => true,
                    '_dates_validated' => $vehicle->id . '|' . now()->addDay()->toDateString() . '|' . now()->addDays(3)->toDateString(),
                ],
                'history' => [],
            ],
        ]);

        $response = $this->postJson(route('booking-chat.action'), [
            'action' => 'confirm',
            'payload' => [],
        ]);

        $response->assertOk();
        $response->assertJsonPath('completed', true);
        $this->assertDatabaseHas('bookings', [
            'email' => 'zeeshan@example.com',
            'vehicle_id' => $vehicle->id,
        ]);
    }

    public function test_booking_chat_confirm_sends_confirmation_emails(): void
    {
        Mail::fake();
        config(['booking.admin_email' => 'admin@example.com']);

        $vehicle = Vehicle::create([
            'make' => 'Toyota',
            'model' => 'Camry',
            'price_per_day' => 100,
            'status' => 'available',
            'seats' => 5,
            'bags' => 2,
        ]);

        session([
            'booking_chat' => [
                'step' => 'confirm',
                'data' => [
                    'vehicle_id' => $vehicle->id,
                    'start_date' => now()->addDay()->toDateString(),
                    'end_date' => now()->addDays(3)->toDateString(),
                    'pickup_location' => 'Miami',
                    'dropoff_location' => 'Miami',
                    'addon_ids' => [],
                    'first_name' => 'Zeeshan',
                    'last_name' => 'Hamza',
                    'email' => 'zeeshan@example.com',
                    'phone' => null,
                    '_addons_done' => true,
                    '_dates_validated' => $vehicle->id . '|' . now()->addDay()->toDateString() . '|' . now()->addDays(3)->toDateString(),
                ],
                'history' => [],
            ],
        ]);

        $this->postJson(route('booking-chat.action'), [
            'action' => 'confirm',
            'payload' => [],
        ])->assertOk();

        Mail::assertSent(BookingCustomerConfirmationMail::class, function ($mail) {
            return $mail->hasTo('zeeshan@example.com');
        });

        Mail::assertSent(BookingAdminNotificationMail::class, function ($mail) {
            return $mail->hasTo('admin@example.com');
        });
    }
}
