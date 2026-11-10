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

    public function test_dates_are_required_for_contact_leads(): void
    {
        $this->from(route('contact'))
            ->post(route('leads.store'), [
                'first_name' => 'Rob',
                'email' => 'seitz.rob@gmail.com',
                'message' => 'Need a car please',
                'source' => 'contact',
            ])
            ->assertSessionHasErrors(['pickup_date', 'dropoff_date']);

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_pitch_message_with_url_is_saved_as_spam_without_emails(): void
    {
        Mail::fake();

        $response = $this->post(route('leads.store'), [
            'first_name' => 'Rob',
            'email' => 'seitz.rob@gmail.com',
            'phone' => '151097420',
            'pickup_date' => now()->addDay()->format('Y-m-d'),
            'dropoff_date' => now()->addDays(3)->format('Y-m-d'),
            'message' => 'Hi there, I was checking out mvmiamirental.com and thought this might be useful for your business. We created a free tool that lets you submit your site to multiple directories. Check it out: https://directoryinspector.com/mvmiamirental.com',
            'source' => 'contact',
        ]);

        $response->assertRedirect(route('leads.thank-you', ['ref' => Lead::first()->reference]));

        $lead = Lead::first();
        $this->assertSame('spam', $lead->status);
        $this->assertStringContainsString('Auto-flagged spam', (string) $lead->admin_notes);

        Mail::assertNothingSent();
    }

    public function test_ai_lead_generation_pitch_is_flagged_as_spam(): void
    {
        Mail::fake();

        $this->post(route('leads.store'), [
            'first_name' => 'Leia',
            'email' => 'hello@ultraleadgeneration.com',
            'pickup_date' => now()->addDay()->format('Y-m-d'),
            'dropoff_date' => now()->addDays(2)->format('Y-m-d'),
            'message' => 'Hi Mvmiamirental Com team, according to MIT\'s Lead Response Management study you are 100x more likely to reach them with our AI voice agent. Start a 14-day free trial at https://ultraleadgeneration.com',
            'source' => 'contact',
        ]);

        $this->assertDatabaseHas('leads', [
            'email' => 'hello@ultraleadgeneration.com',
            'status' => 'spam',
        ]);

        Mail::assertNothingSent();
    }

    public function test_contact_page_loads(): void
    {
        $this->get(route('contact'))->assertOk();
    }
}
