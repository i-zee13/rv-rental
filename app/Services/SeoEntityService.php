<?php

namespace App\Services;

use App\Models\SeoMeta;
use Illuminate\Support\Str;

class SeoEntityService
{
    public const TYPE_VEHICLE = 'vehicle';

    public const TYPE_PROPERTY = 'property';

    public const TYPE_BLOG_POST = 'blog_post';

    public const TYPE_PAGE = 'page';

    public function sync(string $entityType, int $entityId, array $data, string $locale = 'en'): SeoMeta
    {
        $payload = [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'page_key' => $this->fallbackPageKey($entityType),
            'locale' => $locale,
            'meta_title' => Str::limit(trim($data['meta_title'] ?? ''), 255, '') ?: null,
            'meta_description' => isset($data['meta_description'])
                ? Str::limit(trim(strip_tags($data['meta_description'])), 320, '')
                : null,
            'meta_keywords' => Str::limit(trim($data['meta_keywords'] ?? ''), 255, '') ?: null,
            'og_title' => Str::limit(trim($data['og_title'] ?? ''), 255, '') ?: null,
            'og_description' => isset($data['og_description'])
                ? Str::limit(trim(strip_tags($data['og_description'])), 320, '')
                : null,
            'og_image' => trim($data['og_image'] ?? '') ?: null,
            'og_type' => trim($data['og_type'] ?? '') ?: null,
            'robots' => trim($data['robots'] ?? '') ?: 'index,follow',
            'canonical' => trim($data['canonical'] ?? '') ?: null,
            'schema_json' => trim($data['schema_json'] ?? '') ?: null,
            'noindex' => ! empty($data['noindex']),
        ];

        return SeoMeta::updateOrCreate(
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'locale' => $locale,
            ],
            $payload
        );
    }

    protected function fallbackPageKey(string $entityType): string
    {
        return match ($entityType) {
            self::TYPE_VEHICLE => 'vehicles.show',
            self::TYPE_PROPERTY => 'properties.show',
            self::TYPE_BLOG_POST => 'blog.show',
            self::TYPE_PAGE => 'pages.show',
            default => 'global',
        };
    }
}
