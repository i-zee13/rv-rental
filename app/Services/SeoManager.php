<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Property;
use App\Models\SeoMeta as SeoMetaRecord;
use App\Models\Vehicle;
use App\Support\PublicMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoManager
{
    public function buildForRequest(Request $request): array
    {
        $routeName = $request->route()?->getName() ?? 'global';
        $locale = app()->getLocale();
        $global = SeoMetaRecord::forPage('global', $locale);
        $pageMeta = SeoMetaRecord::forPage($routeName, $locale);
        $dynamic = $this->resolveDynamicMeta($request, $routeName, $locale);

        $entityMeta = $dynamic['entity_seo'] ?? null;
        unset($dynamic['entity_seo']);

        $title = $entityMeta?->meta_title
            ?? $dynamic['title']
            ?? $pageMeta?->meta_title
            ?? $global?->meta_title
            ?? config('app.name');

        $description = $entityMeta?->meta_description
            ?? $dynamic['description']
            ?? $pageMeta?->meta_description
            ?? $global?->meta_description
            ?? config('seotools.meta.defaults.description', '');

        $keywords = $entityMeta?->meta_keywords
            ?? $dynamic['keywords']
            ?? $pageMeta?->meta_keywords
            ?? $global?->meta_keywords;

        $ogTitle = $entityMeta?->og_title
            ?? $dynamic['og_title']
            ?? $pageMeta?->og_title
            ?? $global?->og_title
            ?? $title;

        $ogDescription = $entityMeta?->og_description
            ?? $dynamic['og_description']
            ?? $pageMeta?->og_description
            ?? $global?->og_description
            ?? $description;

        $defaultOgImage = config('seotools.default_og_image', '/theme/img/THOR-Vision-Vehicle-TVV-electric-rv-2.jpg');
        $ogImage = $entityMeta?->og_image
            ?? $dynamic['og_image']
            ?? $pageMeta?->og_image
            ?? $global?->og_image
            ?? $defaultOgImage;

        $ogType = $entityMeta?->og_type
            ?? $dynamic['og_type']
            ?? $pageMeta?->og_type
            ?? 'website';

        $canonical = $entityMeta?->canonical
            ?? $dynamic['canonical']
            ?? $pageMeta?->canonical
            ?? $request->url();

        $noindex = $entityMeta?->noindex || $pageMeta?->noindex || $global?->noindex;
        $robots = $noindex
            ? 'noindex,nofollow'
            : ($entityMeta?->robots ?? $pageMeta?->robots ?? $global?->robots ?? 'index,follow');

        $jsonLd = $this->buildJsonLd(
            $entityMeta,
            $pageMeta,
            $global,
            $dynamic,
            $title,
            $description,
            $canonical,
            $ogImage
        );

        return [
            'title' => $title,
            'description' => Str::limit(strip_tags($description), 320),
            'keywords' => $keywords,
            'canonical' => $canonical,
            'robots' => $robots,
            'og' => [
                'type' => $ogType,
                'title' => $ogTitle,
                'description' => Str::limit(strip_tags($ogDescription), 320),
                'image' => $this->absoluteUrl($ogImage),
            ],
            'twitter' => [
                'card' => $pageMeta?->twitter_card ?? $global?->twitter_card ?? 'summary_large_image',
                'title' => $ogTitle,
                'description' => Str::limit(strip_tags($ogDescription), 200),
                'site' => $pageMeta?->twitter_site ?? $global?->twitter_site,
            ],
            'json_ld' => $jsonLd,
            'verification' => [
                'google' => $global?->google_verification,
                'bing' => $global?->bing_verification,
            ],
        ];
    }

    /** @deprecated Use buildForRequest — kept for compatibility */
    public function applyForRequest(Request $request): void
    {
        // SEO is rendered via components/seo-meta.blade.php
    }

    protected function resolveDynamicMeta(Request $request, string $routeName, string $locale): array
    {
        $empty = [
            'title' => null,
            'description' => null,
            'keywords' => null,
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'og_type' => null,
            'canonical' => null,
            'schema' => null,
            'entity_seo' => null,
        ];

        if ($routeName === 'vehicles.show') {
            $vehicle = $this->resolveVehicle($request);
            if (! $vehicle) {
                return $empty;
            }

            $entitySeo = SeoMetaRecord::forEntity(SeoMetaRecord::ENTITY_VEHICLE, $vehicle->id, $locale);
            $t = $vehicle->translations->firstWhere('locale', $locale) ?? $vehicle->translations->first();
            $name = $t->title ?? trim($vehicle->make.' '.$vehicle->model);
            $img = $vehicle->images->first();

            return [
                'entity_seo' => $entitySeo,
                'title' => $t->meta_title ?? ($name.' Rental Miami'),
                'description' => $t->meta_description ?? Str::limit(strip_tags($t->description ?? ''), 160),
                'keywords' => $t->meta_keywords ?? "rent {$name}, Miami car rental, luxury rental",
                'og_title' => $t->meta_title ?? $name,
                'og_description' => $t->meta_description ?? Str::limit(strip_tags($t->description ?? ''), 160),
                'og_image' => $img ? PublicMedia::url($img->path) : null,
                'og_type' => 'product',
                'canonical' => route('vehicles.show', $vehicle->slug),
                'schema' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'Product',
                    'name' => $name,
                    'description' => Str::limit(strip_tags($t->description ?? ''), 300),
                    'image' => $img ? PublicMedia::url($img->path) : null,
                    'url' => route('vehicles.show', $vehicle->slug),
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => (string) $vehicle->price_per_day,
                        'priceCurrency' => env('CURRENCY', 'USD'),
                        'availability' => $vehicle->status === 'available'
                            ? 'https://schema.org/InStock'
                            : 'https://schema.org/OutOfStock',
                    ],
                ],
            ];
        }

        if ($routeName === 'properties.show') {
            $property = $this->resolveProperty($request);
            if (! $property) {
                return $empty;
            }

            $entitySeo = SeoMetaRecord::forEntity(SeoMetaRecord::ENTITY_PROPERTY, $property->id, $locale);
            $t = $property->translations->firstWhere('locale', $locale) ?? $property->translations->first();
            $name = $t->title ?? $property->fullAddress();
            $img = $property->images->first();

            return [
                'entity_seo' => $entitySeo,
                'title' => $t->meta_title ?? ($name.' for Rent'),
                'description' => $t->meta_description ?? Str::limit(strip_tags($t->description ?? ''), 160),
                'keywords' => null,
                'og_title' => $t->meta_title ?? $name,
                'og_description' => $t->meta_description ?? Str::limit(strip_tags($t->description ?? ''), 160),
                'og_image' => $img ? PublicMedia::url($img->path) : null,
                'og_type' => 'website',
                'canonical' => route('properties.show', $property->slug),
                'schema' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'Accommodation',
                    'name' => $name,
                    'description' => Str::limit(strip_tags($t->description ?? ''), 300),
                    'image' => $img ? PublicMedia::url($img->path) : null,
                    'url' => route('properties.show', $property->slug),
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $property->address_line1,
                        'addressLocality' => $property->city,
                        'addressRegion' => $property->state,
                        'postalCode' => $property->zip,
                    ],
                ],
            ];
        }

        if ($routeName === 'blog.show') {
            $slug = $request->route('slug');
            $post = BlogPost::with('translations')->where('slug', $slug)->where('status', 'published')->first();
            if (! $post) {
                return $empty;
            }

            $entitySeo = SeoMetaRecord::forEntity(SeoMetaRecord::ENTITY_BLOG_POST, $post->id, $locale);
            $t = $post->translations->firstWhere('locale', $locale) ?? $post->translations->first();

            return [
                'entity_seo' => $entitySeo,
                'title' => $t->meta_title ?? $t->title,
                'description' => $t->meta_description ?? Str::limit(strip_tags($t->excerpt ?? $t->content ?? ''), 160),
                'keywords' => null,
                'og_title' => $t->meta_title ?? $t->title,
                'og_description' => $t->meta_description ?? Str::limit(strip_tags($t->excerpt ?? ''), 160),
                'og_image' => $post->featured_image,
                'og_type' => 'article',
                'canonical' => route('blog.show', $slug),
                'schema' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => $t->title,
                    'description' => Str::limit(strip_tags($t->excerpt ?? $t->content ?? ''), 300),
                    'url' => route('blog.show', $slug),
                    'datePublished' => optional($post->published_at)->toIso8601String(),
                ],
            ];
        }

        if ($routeName === 'properties.search') {
            return array_merge($empty, [
                'title' => 'Homes & Apartments for Rent in Miami',
                'description' => 'Browse houses, apartments, condos and villas for monthly rent in Miami.',
                'keywords' => 'Miami rentals, apartments for rent, houses for rent',
                'og_title' => 'Homes & Apartments for Rent',
                'og_description' => 'Find your next home in Miami — browse rental listings with photos and amenities.',
                'og_type' => 'website',
                'canonical' => route('properties.search'),
            ]);
        }

        if ($routeName === 'about') {
            $page = Page::with('translations')->whereIn('slug', ['about-us', 'about'])->where('is_published', true)->first();
            if (! $page) {
                return $empty;
            }

            $t = $page->translations->firstWhere('locale', $locale) ?? $page->translations->first();

            return array_merge($empty, [
                'title' => $t->meta_title ?? $t->title,
                'description' => $t->meta_description ?? Str::limit(strip_tags($t->content ?? ''), 160),
                'og_title' => $t->meta_title ?? $t->title,
                'og_description' => $t->meta_description ?? Str::limit(strip_tags($t->content ?? ''), 160),
                'canonical' => route('about'),
            ]);
        }

        if ($routeName === 'pages.show') {
            $slug = $request->route('slug');
            $page = Page::with('translations')->where('slug', $slug)->where('is_published', true)->first();
            if (! $page) {
                return $empty;
            }

            $entitySeo = SeoMetaRecord::forEntity(SeoMetaRecord::ENTITY_PAGE, $page->id, $locale);
            $t = $page->translations->firstWhere('locale', $locale) ?? $page->translations->first();

            return [
                'entity_seo' => $entitySeo,
                'title' => $t->meta_title ?? $t->title,
                'description' => $t->meta_description ?? Str::limit(strip_tags($t->content ?? ''), 160),
                'og_title' => $t->meta_title ?? $t->title,
                'og_description' => $t->meta_description ?? Str::limit(strip_tags($t->content ?? ''), 160),
                'canonical' => route('pages.show', ['slug' => $slug]),
                'schema' => null,
            ];
        }

        return $empty;
    }

    protected function resolveVehicle(Request $request): ?Vehicle
    {
        $slug = $request->route('slug');

        if (! $slug) {
            return null;
        }

        return Vehicle::with(['translations', 'images'])->where('slug', $slug)->first();
    }

    protected function resolveProperty(Request $request): ?Property
    {
        $slug = $request->route('slug');

        if (! $slug) {
            return null;
        }

        return Property::with(['translations', 'images'])->where('slug', $slug)->first();
    }

    protected function buildJsonLd(
        ?SeoMetaRecord $entityMeta,
        ?SeoMetaRecord $pageMeta,
        ?SeoMetaRecord $global,
        array $dynamic,
        string $title,
        string $description,
        string $canonical,
        ?string $ogImage
    ): ?array {
        foreach ([$entityMeta?->schema_json, $pageMeta?->schema_json, $global?->schema_json] as $custom) {
            if ($custom) {
                $decoded = json_decode($custom, true);
                if (is_array($decoded)) {
                    return $decoded + ['@context' => $decoded['@context'] ?? 'https://schema.org'];
                }
            }
        }

        if (! empty($dynamic['schema'])) {
            return $dynamic['schema'];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $title,
            'description' => Str::limit(strip_tags($description), 300),
            'url' => $canonical,
            'image' => $ogImage ? $this->absoluteUrl($ogImage) : null,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => config('app.name'),
                'url' => url('/'),
            ],
        ];
    }

    protected function absoluteUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return url($path);
    }
}
