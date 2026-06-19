<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Faq extends Model
{
    public const SCOPE_GENERAL = 'general';

    public const SCOPE_VEHICLE = 'vehicle';

    public const SCOPE_PROPERTY = 'property';

    public const SCOPES = [
        self::SCOPE_GENERAL => 'General (pick pages)',
        self::SCOPE_VEHICLE => 'Vehicles (all car detail pages)',
        self::SCOPE_PROPERTY => 'Homes & Apartments (all listing pages)',
    ];

    /** Route/page keys assignable to general FAQs */
    public const PAGE_OPTIONS = [
        'home' => 'Homepage',
        'search' => 'Fleet / Search',
        'vehicles.show' => 'Vehicle detail pages',
        'properties.search' => 'Homes search',
        'properties.show' => 'Property detail pages',
        'contact' => 'Contact',
        'about' => 'About Us',
        'blog.index' => 'Blog listing',
        'blog.show' => 'Blog posts',
        'booking.step1' => 'Booking flow',
        'leads.thank-you' => 'Lead thank-you page',
    ];

    protected $fillable = [
        'scope',
        'page_keys',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'page_keys' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(FaqTranslation::class);
    }

    public function translation(?string $locale = null): ?FaqTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'en')
            ?? $this->translations->first();
    }

    public function scopeLabel(): string
    {
        return self::SCOPES[$this->scope] ?? ucfirst($this->scope);
    }

    /**
     * FAQs for a public page (general + optional vehicle/property scopes).
     */
    public static function forPage(string $pageKey, array $extraScopes = []): Collection
    {
        return static::query()
            ->with('translations')
            ->where('is_active', true)
            ->where(function (Builder $q) use ($pageKey, $extraScopes) {
                foreach ($extraScopes as $scope) {
                    $q->orWhere('scope', $scope);
                }

                $q->orWhere(function (Builder $q2) use ($pageKey) {
                    $q2->where('scope', self::SCOPE_GENERAL)
                        ->where(function (Builder $q3) use ($pageKey) {
                            $q3->whereNull('page_keys')
                                ->orWhereJsonContains('page_keys', $pageKey);
                        });
                });
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(fn (self $faq) => filled($faq->translation()?->question));
    }
}
