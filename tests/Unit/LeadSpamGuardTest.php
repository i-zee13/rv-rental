<?php

namespace Tests\Unit;

use App\Services\LeadSpamGuard;
use Tests\TestCase;

class LeadSpamGuardTest extends TestCase
{
    public function test_rental_message_is_not_spam(): void
    {
        $result = app(LeadSpamGuard::class)->analyze('Need a luxury car for a weekend trip to Miami Beach.');

        $this->assertFalse($result['is_spam']);
    }

    public function test_directory_pitch_with_url_is_spam(): void
    {
        $result = app(LeadSpamGuard::class)->analyze(
            'Hi there, I was checking out mvmiamirental.com and thought this might be useful for your business. We created a free tool: https://directoryinspector.com/mvmiamirental.com'
        );

        $this->assertTrue($result['is_spam']);
        $this->assertContains('url', $result['reasons']);
    }

    public function test_ai_lead_gen_pitch_is_spam(): void
    {
        $result = app(LeadSpamGuard::class)->analyze(
            'Hi Mvmiamirental Com team, our AI voice agent helps with lead generation. Start a 14-day free trial.'
        );

        $this->assertTrue($result['is_spam']);
    }

    public function test_instagram_growth_pitch_is_spam(): void
    {
        $result = app(LeadSpamGuard::class)->analyze(
            'We run an Instagram growth service which increases your followers per month. Guaranteed 300-1000+.'
        );

        $this->assertTrue($result['is_spam']);
    }

    public function test_empty_message_is_not_spam(): void
    {
        $result = app(LeadSpamGuard::class)->analyze(null);

        $this->assertFalse($result['is_spam']);
        $this->assertSame(0, $result['score']);
    }
}
