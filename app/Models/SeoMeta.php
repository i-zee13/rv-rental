<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    public const ENTITY_VEHICLE = 'vehicle';

    public const ENTITY_PROPERTY = 'property';

    public const ENTITY_BLOG_POST = 'blog_post';

    public const ENTITY_PAGE = 'page';

    protected $fillable = [
        'page_key', 'entity_type', 'entity_id', 'locale', 'label',
        'meta_title', 'meta_description', 'meta_keywords',
        'og_title', 'og_description', 'og_image', 'og_type',
        'twitter_card', 'twitter_site', 'robots', 'canonical',
        'schema_json', 'google_verification', 'bing_verification', 'noindex',
    ];

    protected $casts = [
        'noindex' => 'boolean',
        'entity_id' => 'integer',
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
            ->whereNull('entity_type')
            ->whereNull('entity_id')
            ->where('locale', $locale)
            ->first()
            ?? static::where('page_key', $pageKey)
                ->whereNull('entity_type')
                ->whereNull('entity_id')
                ->where('locale', 'en')
                ->first();
    }

    public static function forEntity(string $entityType, int $entityId, ?string $locale = null): ?self
    {
        $locale = $locale ?? app()->getLocale();

        return static::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('locale', $locale)
            ->first()
            ?? static::where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->where('locale', 'en')
                ->first();
    }

    public function displayLabel(): string
    {
        if ($this->entity_type && $this->entity_id) {
            return ucfirst(str_replace('_', ' ', $this->entity_type)).' #'.$this->entity_id;
        }

        return $this->label ?? self::PAGE_LABELS[$this->page_key] ?? $this->page_key;
    }
}
