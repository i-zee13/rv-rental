<?php

namespace Tests\Feature;

use App\Models\SeoMeta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SeoSeeder::class);
    }

    public function test_homepage_includes_seo_meta_tags(): void
    {
        SeoMeta::where('page_key', 'home')->update([
            'meta_title' => 'Test Miami Rentals Home',
            'meta_description' => 'Test description for homepage SEO.',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Test Miami Rentals Home', false);
        $response->assertSee('Test description for homepage SEO.', false);
        $response->assertSee('og:title', false);
        $response->assertSee('application/ld+json', false);
    }

    public function test_admin_seo_index_requires_auth(): void
    {
        $this->get(route('admin.seo.index'))->assertRedirect(route('admin.login'));
    }
}
