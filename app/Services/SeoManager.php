<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\SeoMeta as SeoMetaRecord;
use App\Models\Vehicle;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoManager
{
    public function applyForRequest(Request $request): void
    {
        $routeName = $request->route()?->getName() ?? 'global';
        $locale = app()->getLocale();
        $global = SeoMetaRecord::forPage('global', $locale);
        $pageMeta = SeoMetaRecord::forPage($routeName, $locale);

        $dynamic = $this->resolveDynamicMeta($request, $routeName, $locale);

        $title = $dynamic['title']
            ?? $pageMeta?->meta_title
            ?? $global?->meta_title
            ?? config('app.name');

        $description = $dynamic['description']
            ?? $pageMeta?->meta_description
            ?? $global?->meta_description
            ?? config('seotools.meta.defaults.description', '');

        $keywords = $dynamic['keywords']
            ?? $pageMeta?->meta_keywords
            ?? $global?->meta_keywords;

        $ogTitle = $pageMeta?->og_title
            ?? $dynamic['og_title']
            ?? $global?->og_title
            ?? $title;

        $ogDescription = $pageMeta?->og_description
            ?? $dynamic['og_description']
            ?? $global?->og_description
            ?? $description;

        $defaultOgImage = config('seotools.default_og_image', '/theme/img/THOR-Vision-Vehicle-TVV-electric-rv-2.jpg');
        $ogImage = $dynamic['og_image']
            ?? $pageMeta?->og_image
            ?? $global?->og_image
            ?? $defaultOgImage;
        $ogType = $dynamic['og_type'] ?? $pageMeta?->og_type ?? 'website';

        $canonical = $dynamic['canonical']
            ?? $pageMeta?->canonical
            ?? $request->url();

        $robots = ($pageMeta?->noindex || $global?->noindex)
            ? 'noindex,nofollow'
            : ($pageMeta?->robots ?? $global?->robots ?? 'index,follow');

        $siteName = config('app.name', 'MV Miami Rental');
        $separator = config('seotools.meta.defaults.separator', ' | ');

        SEOMeta::setTitle($title, false);
        SEOMeta::setDescription(Str::limit(strip_tags($description), 320));
        if ($keywords) {
            SEOMeta::setKeywords(array_filter(array_map('trim', explode(',', $keywords))));
        }
        SEOMeta::setCanonical($canonical);
        SEOMeta::setRobots($robots);

        OpenGraph::setTitle($ogTitle);
        OpenGraph::setDescription(Str::limit(strip_tags($ogDescription), 320));
        OpenGraph::setUrl($canonical);
        OpenGraph::setSiteName($siteName);
        OpenGraph::setType($ogType);
        OpenGraph::addImage($this->absoluteUrl($ogImage));

        TwitterCard::setType($pageMeta?->twitter_card ?? $global?->twitter_card ?? 'summary_large_image');
        TwitterCard::setTitle($ogTitle);
        TwitterCard::setDescription(Str::limit(strip_tags($ogDescription), 200));
        if ($twitterSite = $pageMeta?->twitter_site ?? $global?->twitter_site) {
            TwitterCard::setSite($twitterSite);
        }
        TwitterCard::setImage($this->absoluteUrl($ogImage));

        $this->applyJsonLd($pageMeta, $global, $dynamic, $title, $description, $canonical, $ogImage);

        $this->applyWebmasterTags($global);
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
        ];

        if ($routeName === 'vehicles.show') {
            $id = $request->route('id');
            $vehicle = Vehicle::with(['translations', 'images'])->find($id);
            if (!$vehicle) {
                return $empty;
            }

            $t = $vehicle->translations->firstWhere('locale', $locale) ?? $vehicle->translations->first();
            $name = $t->title ?? trim($vehicle->make . ' ' . $vehicle->model);

            return [
                'title' => $t->meta_title ?? ($name . ' Rental Miami'),
                'description' => $t->meta_description ?? Str::limit(strip_tags($t->description ?? ''), 160),
                'keywords' => $t->meta_keywords ?? "rent {$name}, Miami car rental, luxury rental",
                'og_title' => $t->meta_title ?? $name,
                'og_description' => $t->meta_description ?? Str::limit(strip_tags($t->description ?? ''), 160),
                'og_image' => $vehicle->images->first()?->path,
                'og_type' => 'product',
                'canonical' => route('vehicles.show', $vehicle->id),
                'schema' => [
                    '@type' => 'Product',
                    'name' => $name,
                    'description' => Str::limit(strip_tags($t->description ?? ''), 300),
                    'image' => $vehicle->images->first()?->path ? $this->absoluteUrl($vehicle->images->first()->path) : null,
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

        if ($routeName === 'blog.show') {
            $slug = $request->route('slug');
            $post = BlogPost::with('translations')->where('slug', $slug)->where('status', 'published')->first();
            if (!$post) {
                return $empty;
            }

            $t = $post->translations->firstWhere('locale', $locale) ?? $post->translations->first();

            return [
                'title' => $t->meta_title ?? $t->title,
                'description' => $t->meta_description ?? Str::limit(strip_tags($t->excerpt ?? $t->content ?? ''), 160),
                'keywords' => null,
                'og_title' => $t->meta_title ?? $t->title,
                'og_description' => $t->meta_description ?? Str::limit(strip_tags($t->excerpt ?? ''), 160),
                'og_image' => $post->featured_image,
                'og_type' => 'article',
                'canonical' => route('blog.show', $slug),
                'schema' => [
                    '@type' => 'Article',
                    'headline' => $t->title,
                    'description' => Str::limit(strip_tags($t->excerpt ?? $t->content ?? ''), 300),
                ],
            ];
        }

        if ($routeName === 'properties.search') {
            return [
                'title' => 'Homes & Apartments for Rent in Miami',
                'description' => 'Browse houses, apartments, condos and villas for monthly rent in Miami. Filter by price, beds, baths, pets and amenities.',
                'keywords' => 'Miami rentals, apartments for rent, houses for rent, monthly rental',
                'og_title' => 'Homes & Apartments for Rent',
                'og_description' => 'Find your next home in Miami — browse rental listings with photos, amenities and inquiry forms.',
                'og_image' => null,
                'og_type' => 'website',
                'canonical' => route('properties.search'),
                'schema' => null,
            ];
        }

        if ($routeName === 'properties.show') {
            $id = $request->route('id');
            $property = \App\Models\Property::with(['translations', 'images'])->find($id);
            if (!$property) {
                return $empty;
            }

            $t = $property->translations->firstWhere('locale', $locale) ?? $property->translations->first();
            $name = $t->title ?? $property->fullAddress();

            return [
                'title' => $t->meta_title ?? ($name . ' for Rent'),
                'description' => $t->meta_description ?? Str::limit(strip_tags($t->description ?? ''), 160),
                'keywords' => null,
                'og_title' => $t->meta_title ?? $name,
                'og_description' => $t->meta_description ?? Str::limit(strip_tags($t->description ?? ''), 160),
                'og_image' => $property->images->first()?->path,
                'og_type' => 'website',
                'canonical' => route('properties.show', $property->id),
                'schema' => null,
            ];
        }

        if ($routeName === 'about') {
            $page = Page::with('translations')->whereIn('slug', ['about-us', 'about'])->where('is_published', true)->first();
            if (!$page) {
                return $empty;
            }

            $t = $page->translations->firstWhere('locale', $locale) ?? $page->translations->first();

            return [
                'title' => $t->meta_title ?? $t->title,
                'description' => $t->meta_description ?? Str::limit(strip_tags($t->content ?? ''), 160),
                'keywords' => null,
                'og_title' => $t->meta_title ?? $t->title,
                'og_description' => $t->meta_description ?? Str::limit(strip_tags($t->content ?? ''), 160),
                'og_image' => null,
                'og_type' => 'website',
                'canonical' => route('about'),
                'schema' => null,
            ];
        }

        if ($routeName === 'pages.show') {
            $slug = $request->route('slug');
            $page = Page::with('translations')->where('slug', $slug)->where('is_published', true)->first();
            if (!$page) {
                return $empty;
            }

            $t = $page->translations->firstWhere('locale', $locale) ?? $page->translations->first();

            return [
                'title' => $t->meta_title ?? $t->title,
                'description' => $t->meta_description ?? Str::limit(strip_tags($t->content ?? ''), 160),
                'keywords' => null,
                'og_title' => $t->meta_title ?? $t->title,
                'og_description' => $t->meta_description ?? Str::limit(strip_tags($t->content ?? ''), 160),
                'og_image' => null,
                'og_type' => 'website',
                'canonical' => route('pages.show', ['slug' => $slug]),
                'schema' => null,
            ];
        }

        return $empty;
    }

    protected function applyJsonLd(
        ?SeoMetaRecord $pageMeta,
        ?SeoMetaRecord $global,
        array $dynamic,
        string $title,
        string $description,
        string $canonical,
        ?string $ogImage
    ): void {
        $custom = $pageMeta?->schema_json ?? $global?->schema_json;
        if ($custom) {
            $decoded = json_decode($custom, true);
            if (is_array($decoded)) {
                JsonLd::setType($decoded['@type'] ?? 'WebPage');
                JsonLd::setTitle($decoded['name'] ?? $decoded['headline'] ?? $title);
                JsonLd::setDescription($decoded['description'] ?? $description);
                JsonLd::setUrl($decoded['url'] ?? $canonical);
                if (!empty($decoded['image'])) {
                    JsonLd::addImage($this->absoluteUrl($decoded['image']));
                }

                return;
            }
        }

        if (!empty($dynamic['schema'])) {
            $schema = $dynamic['schema'];
            JsonLd::setType($schema['@type'] ?? 'WebPage');
            JsonLd::setTitle($schema['name'] ?? $schema['headline'] ?? $title);
            JsonLd::setDescription($schema['description'] ?? $description);
            JsonLd::setUrl($canonical);
            if (!empty($schema['image'])) {
                JsonLd::addImage($this->absoluteUrl($schema['image']));
            }
            if (!empty($schema['offers'])) {
                JsonLd::addValue('offers', $schema['offers']);
            }

            return;
        }

        JsonLd::setType('WebSite');
        JsonLd::setTitle($title);
        JsonLd::setDescription($description);
        JsonLd::setUrl($canonical);
        if ($ogImage) {
            JsonLd::addImage($this->absoluteUrl($ogImage));
        }
    }

    protected function applyWebmasterTags(?SeoMetaRecord $global): void
    {
        if (!$global) {
            return;
        }
        if ($global->google_verification) {
            SEOMeta::addMeta('google-site-verification', $global->google_verification);
        }
        if ($global->bing_verification) {
            SEOMeta::addMeta('msvalidate.01', $global->bing_verification);
        }
    }

    protected function absoluteUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return url($path);
    }
}
