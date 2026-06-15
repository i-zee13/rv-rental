<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $fillable = [
        'page_key', 'locale', 'label', 'meta_title', 'meta_description', 'meta_keywords',
        'og_title', 'og_description', 'og_image', 'og_type', 'twitter_card', 'twitter_site',
        'robots', 'canonical', 'schema_json', 'google_verification', 'bing_verification', 'noindex',
    ];

    protected $casts = [
        'noindex' => 'boolean',
    ];

    public const PAGE_LABELS = [
        'global' => 'Global Defaults',
        'home' => 'Homepage',
        'search' => 'Fleet / Search',
        'properties.search' => 'Homes & Apartments Search',
        'properties.show' => 'Property Detail (fallback)',
        'contact' => 'Contact',
        'about' => 'About Us',
        'blog.index' => 'Blog Listing',
        'blog.show' => 'Blog Post (fallback)',
        'pages.show' => 'CMS Page (fallback)',
        'vehicles.show' => 'Vehicle Detail (fallback)',
        'booking.step1' => 'Booking — Select Vehicle',
        'leads.thank-you' => 'Lead Thank You',
    ];

    public static function forPage(string $pageKey, ?string $locale = null): ?self
    {
        $locale = $locale ?? app()->getLocale();

        return static::where('page_key', $pageKey)
            ->where('locale', $locale)
            ->first()
            ?? static::where('page_key', $pageKey)->where('locale', 'en')->first();
    }

    public function displayLabel(): string
    {
        return $this->label ?? self::PAGE_LABELS[$this->page_key] ?? $this->page_key;
    }
}
