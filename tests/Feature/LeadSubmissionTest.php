<?php

namespace Tests\Feature;

use App\Mail\LeadThankYouMail;
use App\Mail\NewLeadAdminMail;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_form_submission_saves_to_database_and_sends_emails(): void
    {
        Mail::fake();

        $response = $this->post(route('leads.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+17860000000',
            'pickup_location' => 'Miami Airport',
            'dropoff_location' => 'Miami Beach',
            'pickup_date' => now()->addDay()->format('Y-m-d'),
            'dropoff_date' => now()->addDays(3)->format('Y-m-d'),
            'message' => 'Need a luxury car for weekend.',
            'source' => 'contact',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leads', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'status' => 'new',
        ]);

        $lead = Lead::where('email', 'john@example.com')->first();
        $this->assertNotNull($lead->reference);

        Mail::assertSent(LeadThankYouMail::class, fn ($mail) => $mail->hasTo('john@example.com'));
        Mail::assertSent(NewLeadAdminMail::class);
    }

    public function test_honeypot_blocks_spam_submissions(): void
    {
        $this->post(route('leads.store'), [
            'first_name' => 'Spam',
            'email' => 'spam@bot.com',
            'website_url' => 'http://spam.com',
        ])->assertRedirect(route('leads.thank-you'));

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_contact_page_loads(): void
    {
        $this->get(route('contact'))->assertOk();
    }
}
