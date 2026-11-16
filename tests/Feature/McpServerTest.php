<?php

namespace Tests\Feature;

use App\Mcp\McpTokenService;
use App\Models\Booking;
use App\Models\Faq;
use App\Models\FaqTranslation;
use App\Models\Lead;
use App\Models\McpAccessToken;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class McpServerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected string $plain;

    protected function setUp(): void
    {
        parent::setUp();

        config(['mcp.enabled' => true]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $issued = app(McpTokenService::class)->issue('Aide', 'ws_aide', $this->admin->id, now()->addDay());
        $this->plain = $issued['plain'];
    }

    public function test_mcp_is_not_found_when_disabled_and_does_not_authenticate(): void
    {
        config(['mcp.enabled' => false]);

        $this->mcpCall('initialize')->assertNotFound();
        $this->assertDatabaseCount('mcp_audit_logs', 0);
    }

    public function test_initialize_lists_tools_and_rejects_unknown_methods(): void
    {
        $init = $this->mcpCall('initialize');
        $init->assertOk();
        $init->assertJsonPath('result.protocolVersion', '2025-03-26');
        $init->assertJsonPath('result.serverInfo.name', 'mv-miami-rental');

        $tools = $this->mcpCall('tools/list')->assertOk()->json('result.tools');
        $names = array_column($tools, 'name');

        $this->assertContains('mv_check_vehicle_availability', $names);
        $this->assertContains('mv_get_revenue_summary', $names);
        $this->assertNotContains('mv_create_booking', $names);

        foreach ($tools as $tool) {
            $this->assertTrue($tool['annotations']['readOnlyHint']);
            $this->assertFalse($tool['annotations']['destructiveHint']);
            $this->assertDoesNotMatchRegularExpression('/create|update|delete|cancel|refund/i', $tool['name']);
        }

        $this->mcpCall('nope')->assertOk()->assertJsonPath('error.code', -32601);

        $this->call(
            'POST',
            '/mcp',
            [],
            [],
            [],
            $this->transformHeadersToServerVars($this->headers()),
            'not-json',
        )->assertStatus(400)->assertJsonPath('error.code', -32700);
    }

    public function test_notifications_return_accepted_without_a_body(): void
    {
        $response = $this->postJson('/mcp', [
            'jsonrpc' => '2.0',
            'method' => 'notifications/initialized',
        ], $this->headers());

        $response->assertStatus(202);
        $this->assertSame('', $response->getContent());
    }

    public function test_authentication_failures(): void
    {
        $this->mcpCall('ping', [], ['Authorization' => 'Bearer wrong'])->assertUnauthorized();

        $expired = $this->issueFor($this->admin, now()->subMinute());
        $this->mcpCall('ping', [], ['Authorization' => 'Bearer '.$expired])->assertUnauthorized();

        $revokedPlain = $this->issueFor($this->admin, now()->addDay());
        McpAccessToken::query()->where('token_hash', hash('sha256', $revokedPlain))->update(['revoked_at' => now()]);
        $this->mcpCall('ping', [], ['Authorization' => 'Bearer '.$revokedPlain])->assertUnauthorized();

        $this->mcpCall('ping', [], ['X-Aide-Workspace-Id' => 'other-workspace'])->assertForbidden();

        $agent = User::factory()->create(['role' => 'booking_agent', 'is_active' => true]);
        $agentToken = $this->storeToken($agent, 'ws_aide');
        $this->mcpCall('ping', [], ['Authorization' => 'Bearer '.$agentToken])->assertForbidden();

        $this->assertDatabaseHas('mcp_audit_logs', ['outcome' => 'workspace_mismatch']);
        $this->assertDatabaseHas('mcp_audit_logs', ['outcome' => 'revoked']);
        $this->assertDatabaseHas('mcp_audit_logs', ['outcome' => 'expired']);
    }

    public function test_vehicle_availability_uses_booking_rules(): void
    {
        config([
            'booking.check_availability' => true,
            'booking.blocking_statuses' => ['pending', 'pending_payment', 'confirmed', 'active', 'completed'],
        ]);

        $vehicle = $this->vehicle('available');
        $this->booking($vehicle, 'confirmed', '2026-12-01', '2026-12-05');

        $blocked = $this->tool('mv_check_vehicle_availability', [
            'vehicle_id' => $vehicle->id,
            'start' => '2026-12-03',
            'end' => '2026-12-04',
        ]);

        $this->assertFalse($blocked['available']);
        $this->assertTrue($blocked['calendar_conflict']);
        $this->assertTrue($blocked['date_overlap_enforced']);

        Booking::query()->update(['status' => 'cancelled']);

        $open = $this->tool('mv_check_vehicle_availability', [
            'vehicle_id' => $vehicle->id,
            'start' => '2026-12-03',
            'end' => '2026-12-04',
        ]);

        $this->assertTrue($open['available']);
        $this->assertFalse($open['calendar_conflict']);

        config(['booking.check_availability' => false]);
        Booking::query()->update(['status' => 'confirmed']);

        $statusOnly = $this->tool('mv_check_vehicle_availability', [
            'vehicle_id' => $vehicle->id,
            'start' => '2026-12-03',
            'end' => '2026-12-04',
        ]);

        $this->assertTrue($statusOnly['available']);
        $this->assertTrue($statusOnly['calendar_conflict']);
        $this->assertFalse($statusOnly['date_overlap_enforced']);
        $this->assertSame('status_only', $statusOnly['check_mode']);
    }

    public function test_revenue_is_paid_payments_and_secrets_are_omitted(): void
    {
        $vehicle = $this->vehicle('available');
        $unpaid = $this->booking($vehicle, 'pending', now()->toDateString(), now()->addDay()->toDateString(), 500);
        $paid = $this->booking($vehicle, 'confirmed', now()->toDateString(), now()->addDays(2)->toDateString(), 200);

        Payment::create([
            'booking_id' => $paid->id,
            'provider' => 'stripe',
            'provider_id' => 'cs_test_should_not_leak',
            'amount' => 200,
            'currency' => 'USD',
            'status' => 'paid',
            'meta' => ['checkout_url' => 'https://checkout.stripe.test/secret'],
        ]);

        $today = now()->toDateString();
        $revenue = $this->tool('mv_get_revenue_summary', ['from' => $today, 'to' => $today]);

        $this->assertEquals(200, $revenue['collected_revenue']['amount']);
        $this->assertSame('USD', $revenue['collected_revenue']['currency']);
        $this->assertEquals(700, $revenue['booking_totals_created_in_period']['amount']);

        $pending = $this->tool('mv_list_pending_payments', []);
        $pendingIds = array_column($pending['bookings'], 'id');
        $this->assertContains($unpaid->id, $pendingIds);
        $this->assertNotContains($paid->id, $pendingIds);
        $this->assertSame('pay_at_pickup', collect($pending['bookings'])->firstWhere('id', $unpaid->id)['payment_state']);

        $listed = $this->tool('mv_list_bookings', []);
        $this->assertArrayNotHasKey('email', $listed['bookings'][0]);

        $detailResponse = $this->mcpCall('tools/call', [
            'name' => 'mv_get_booking',
            'arguments' => ['id' => $paid->id],
        ]);
        $detailResponse->assertOk();
        $this->assertStringNotContainsString('cs_test_should_not_leak', $detailResponse->getContent());
        $this->assertStringNotContainsString('checkout.stripe.test', $detailResponse->getContent());
        $this->assertSame('ada@example.com', $detailResponse->json('result.structuredContent.customer.email'));
    }

    public function test_active_rentals_require_confirmed_or_active_status(): void
    {
        $vehicle = $this->vehicle('available');
        $active = $this->booking($vehicle, 'confirmed', now()->subDay()->toDateString(), now()->addDay()->toDateString());
        $pending = $this->booking($vehicle, 'pending', now()->subDay()->toDateString(), now()->addDay()->toDateString());

        $result = $this->tool('mv_get_active_rentals', []);
        $ids = array_column($result['bookings'], 'id');

        $this->assertContains($active->id, $ids);
        $this->assertNotContains($pending->id, $ids);
    }

    public function test_property_inquiries_are_not_reservations(): void
    {
        $type = PropertyType::create(['slug' => 'condo', 'is_active' => true, 'sort_order' => 1]);
        $property = Property::create([
            'property_type_id' => $type->id,
            'slug' => 'brickell-condo',
            'address_line1' => '100 Secret St',
            'city' => 'Miami',
            'neighborhood' => 'Brickell',
            'bedrooms' => 2,
            'bathrooms' => 2,
            'pets_allowed' => true,
            'furnished' => true,
            'price_per_month' => 3000,
            'status' => 'available',
        ]);

        Lead::create([
            'reference' => 'LD-PROP1',
            'status' => 'new',
            'source' => 'property',
            'property_id' => $property->id,
            'property_name' => 'Brickell Condo',
            'first_name' => 'Grace',
            'email' => 'grace@example.com',
        ]);

        $list = $this->mcpCall('tools/call', [
            'name' => 'mv_list_properties',
            'arguments' => ['pets_allowed' => true],
        ]);
        $list->assertOk();
        $this->assertStringNotContainsString('100 Secret St', $list->getContent());
        $this->assertFalse($list->json('result.structuredContent.reservations_supported'));

        $detail = $this->tool('mv_get_property', ['id' => $property->id]);
        $this->assertStringContainsString('100 Secret St', $detail['address']);
        $this->assertStringContainsString('Miami', $detail['address']);
        $this->assertFalse($detail['reservations_supported']);

        $inquiries = $this->tool('mv_get_property_inquiries', ['property_id' => $property->id]);
        $this->assertSame('property_inquiry', $inquiries['inquiries'][0]['lead_type']);
        $this->assertArrayNotHasKey('email', $inquiries['inquiries'][0]);

        $lead = $this->tool('mv_get_lead', ['reference' => 'LD-PROP1']);
        $this->assertSame('grace@example.com', $lead['customer']['email']);

        $stats = $this->tool('mv_get_lead_statistics', [
            'from' => now()->toDateString(),
            'to' => now()->toDateString(),
        ]);
        $this->assertNull($stats['booking_conversion_rate']);
        $this->assertFalse($stats['booking_conversion_rate_available']);
        $this->assertSame(1, $stats['by_type']['property_inquiry']);
    }

    public function test_uncontacted_leads_and_faqs(): void
    {
        Lead::create([
            'reference' => 'LD-NEW',
            'status' => 'new',
            'source' => 'contact',
            'first_name' => 'New',
            'email' => 'new@example.com',
        ]);
        Lead::create([
            'reference' => 'LD-LATER',
            'status' => 'lead',
            'source' => 'contact',
            'first_name' => 'Later',
            'email' => 'later@example.com',
            'contacted_at' => now(),
        ]);

        $uncontacted = $this->tool('mv_get_uncontacted_leads', []);
        $refs = array_column($uncontacted['leads'], 'reference');
        $this->assertSame(['LD-NEW'], $refs);

        $faq = Faq::create(['scope' => 'general', 'is_active' => true, 'sort_order' => 1, 'page_keys' => ['home']]);
        FaqTranslation::create([
            'faq_id' => $faq->id,
            'locale' => 'en',
            'question' => 'Where do I pick up the car?',
            'answer' => 'Miami FL 33122',
        ]);

        $found = $this->tool('mv_search_faqs', ['query' => 'pick up']);
        $this->assertSame('Where do I pick up the car?', $found['faqs'][0]['translations']['en']['question']);

        $this->mcpCall('tools/call', [
            'name' => 'mv_search_faqs',
            'arguments' => ['query' => 'a'],
        ])->assertOk()->assertJsonPath('result.isError', true);
    }

    public function test_resources_and_overview_do_not_invent_conversion(): void
    {
        $resource = $this->mcpCall('resources/read', ['uri' => 'mv://context/properties'])->assertOk();
        $text = $resource->json('result.contents.0.text');
        $this->assertStringContainsString('"reservations_supported":false', $text);

        $overview = $this->tool('mv_get_business_overview', [
            'from' => now()->toDateString(),
            'to' => now()->toDateString(),
        ]);
        $this->assertFalse($overview['limitations']['booking_conversion_rate_supported']);
        $this->assertFalse($overview['limitations']['property_reservations_supported']);
        $this->assertArrayNotHasKey('email', $overview);

        $fleet = $this->tool('mv_get_fleet_summary', []);
        $this->assertSame(0, $fleet['total']);

        $site = $this->tool('mv_get_site_information', []);
        $this->assertSame('MV Miami Rental', $site['brand']);
        $this->assertIsBool($site['stripe_checkout_configured']);
    }

    public function test_rate_limit_and_non_post_methods(): void
    {
        config(['mcp.rate_limit_per_minute' => 1]);
        RateLimiter::clear('mcp-tool:'.McpAccessToken::query()->first()->id);

        $this->mcpCall('ping')->assertOk();
        $this->mcpCall('ping')->assertStatus(429);

        RateLimiter::clear('mcp-tool:'.McpAccessToken::query()->first()->id);
        $this->getJson('/mcp', $this->headers())->assertStatus(405);
    }

    public function test_issue_command_stores_a_hash_and_customer_chat_stays_open(): void
    {
        $this->artisan('mcp:issue', [
            'name' => 'Aide production',
            'workspace' => 'ws_prod',
            '--user' => $this->admin->email,
        ])->assertSuccessful();

        $row = McpAccessToken::query()->where('workspace_id', 'ws_prod')->first();
        $this->assertNotNull($row);
        $this->assertNull($row->expires_at);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $row->token_hash);
        $this->assertStringStartsWith('mvmcp_', $row->token_prefix);

        $this->artisan('mcp:revoke', ['token' => (string) $row->id])->assertSuccessful();
        $this->assertNotNull($row->fresh()->revoked_at);

        config(['ai.enabled' => true, 'mcp.enabled' => true]);
        $this->postJson('/booking-chat/start')->assertOk()->assertJsonPath('step', 'choose_type');
    }

    protected function tool(string $name, array $arguments): array
    {
        return $this->mcpCall('tools/call', [
            'name' => $name,
            'arguments' => $arguments,
        ])->assertOk()->json('result.structuredContent');
    }

    protected function mcpCall(string $method, array $params = [], array $headers = [])
    {
        return $this->postJson('/mcp', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => $method,
            'params' => $params === [] ? new \stdClass() : $params,
        ], array_merge($this->headers(), $headers));
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->plain,
            'X-Aide-Workspace-Id' => 'ws_aide',
            'Accept' => 'application/json, text/event-stream',
        ];
    }

    protected function issueFor(User $user, $expiresAt): string
    {
        return app(McpTokenService::class)->issue('Aide', 'ws_aide', $user->id, $expiresAt)['plain'];
    }

    protected function storeToken(User $user, string $workspace): string
    {
        $plain = 'mvmcp_agenttoken'.$user->id.str_repeat('x', 20);

        McpAccessToken::create([
            'name' => 'agent',
            'token_hash' => hash('sha256', $plain),
            'token_prefix' => substr($plain, 0, 16),
            'workspace_id' => $workspace,
            'user_id' => $user->id,
            'scopes' => ['operations:read'],
        ]);

        return $plain;
    }

    protected function vehicle(string $status): Vehicle
    {
        return Vehicle::create([
            'slug' => 'fleet-'.$status.'-'.uniqid(),
            'make' => 'Tesla',
            'model' => 'Model S',
            'status' => $status,
            'price_per_day' => 100,
        ]);
    }

    protected function booking(Vehicle $vehicle, string $status, string $start, string $end, float $total = 100): Booking
    {
        return Booking::create([
            'reference' => 'BK'.strtoupper(uniqid()),
            'vehicle_id' => $vehicle->id,
            'pickup_at' => $start.' 12:00:00',
            'return_at' => $end.' 12:00:00',
            'start_date' => $start,
            'end_date' => $end,
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.com',
            'status' => $status,
            'subtotal' => $total,
            'taxes' => 0,
            'total' => $total,
            'currency' => 'USD',
        ]);
    }
}
