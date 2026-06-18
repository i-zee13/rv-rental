<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\SeoMeta;
use App\Services\SeoEntityService;
use App\Support\PublicMedia;
use App\Support\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::with(['translations', 'images', 'type.translations'])->latest()->get();

        return view('admin.properties.index', compact('properties'));
    }

    public function create()
    {
        $types = PropertyType::with('translations')->where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.properties.create', compact('types'));
    }

    public function store(StorePropertyRequest $request)
    {
        $data = $request->validated();

        $property = Property::create([
            'property_type_id' => $data['property_type_id'] ?? null,
            'reference' => 'PR' . strtoupper(uniqid()),
            'slug' => Slug::unique(trim($data['title_en'] ?? 'property'), Property::class),
            'address_line1' => $data['address_line1'],
            'city' => $data['city'] ?? 'Miami',
            'state' => $data['state'] ?? 'FL',
            'zip' => $data['zip'] ?? null,
            'neighborhood' => $data['neighborhood'] ?? null,
            'bedrooms' => $data['bedrooms'],
            'bathrooms' => $data['bathrooms'],
            'sqft' => $data['sqft'] ?? null,
            'price_per_month' => $data['price_per_month'],
            'price_per_week' => $data['price_per_week'] ?? null,
            'price_per_night' => $data['price_per_night'] ?? null,
            'security_deposit' => $data['security_deposit'] ?? 0,
            'cleaning_fee' => $data['cleaning_fee'] ?? 0,
            'max_guests' => $data['max_guests'] ?? null,
            'min_nights' => $data['min_nights'] ?? 30,
            'pets_allowed' => $request->boolean('pets_allowed'),
            'furnished' => $request->boolean('furnished'),
            'featured' => $request->boolean('featured'),
            'instant_book' => $request->boolean('instant_book'),
            'amenities' => $data['amenities'] ?? [],
            'available_from' => $data['available_from'] ?? null,
            'status' => $data['status'],
        ]);

        $this->syncTranslations($property, $data);
        $this->syncSeo($property, $request);
        $this->storeImages($property, $request);

        return redirect()->route('admin.properties.index')->with('success', 'Property created.');
    }

    public function edit($id)
    {
        $property = Property::with(['translations', 'images'])->findOrFail($id);
        $types = PropertyType::with('translations')->where('is_active', true)->orderBy('sort_order')->get();
        $seo = SeoMeta::forEntity(SeoMeta::ENTITY_PROPERTY, $property->id, 'en');

        return view('admin.properties.edit', compact('property', 'types', 'seo'));
    }

    public function update(UpdatePropertyRequest $request, $id)
    {
        $data = $request->validated();
        $property = Property::findOrFail($id);

        $property->update([
            'property_type_id' => $data['property_type_id'] ?? $property->property_type_id,
            'slug' => $property->slug ?: Slug::unique(
                trim($data['title_en'] ?? $property->title() ?? 'property'),
                Property::class,
                $property->id
            ),
            'address_line1' => $data['address_line1'] ?? $property->address_line1,
            'city' => $data['city'] ?? $property->city,
            'state' => $data['state'] ?? $property->state,
            'zip' => $data['zip'] ?? $property->zip,
            'neighborhood' => $data['neighborhood'] ?? $property->neighborhood,
            'bedrooms' => $data['bedrooms'] ?? $property->bedrooms,
            'bathrooms' => $data['bathrooms'] ?? $property->bathrooms,
            'sqft' => $data['sqft'] ?? $property->sqft,
            'price_per_month' => $data['price_per_month'] ?? $property->price_per_month,
            'price_per_week' => $data['price_per_week'] ?? $property->price_per_week,
            'price_per_night' => $data['price_per_night'] ?? $property->price_per_night,
            'security_deposit' => $data['security_deposit'] ?? $property->security_deposit,
            'cleaning_fee' => $data['cleaning_fee'] ?? $property->cleaning_fee,
            'max_guests' => $data['max_guests'] ?? $property->max_guests,
            'min_nights' => $data['min_nights'] ?? $property->min_nights,
            'pets_allowed' => $request->boolean('pets_allowed'),
            'furnished' => $request->boolean('furnished'),
            'featured' => $request->boolean('featured'),
            'instant_book' => $request->boolean('instant_book'),
            'amenities' => $data['amenities'] ?? $property->amenities ?? [],
            'available_from' => $data['available_from'] ?? $property->available_from,
            'status' => $data['status'] ?? $property->status,
        ]);

        $this->syncTranslations($property, $data);
        $this->syncSeo($property, $request);
        $this->storeImages($property, $request);

        if ($request->filled('delete_image_ids')) {
            foreach ($property->images()->whereIn('id', $request->delete_image_ids)->get() as $img) {
                $this->deleteImageFile($img->path);
                $img->delete();
            }
        }

        return redirect()->route('admin.properties.index')->with('success', 'Property updated.');
    }

    public function destroyImage($propertyId, $imageId)
    {
        $property = Property::findOrFail($propertyId);
        $image = $property->images()->findOrFail($imageId);
        $this->deleteImageFile($image->path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    protected function syncTranslations(Property $property, array $data): void
    {
        $property->translations()->updateOrCreate(
            ['locale' => 'en'],
            [
                'title' => $data['title_en'],
                'description' => $data['description_en'] ?? null,
                'meta_title' => Str::limit(trim(($data['title_en'] ?? '') . ' | ' . config('app.name')), 255, ''),
                'meta_description' => isset($data['description_en'])
                    ? Str::limit(trim(strip_tags($data['description_en'])), 320, '')
                    : null,
            ]
        );

        $property->translations()->updateOrCreate(
            ['locale' => 'es'],
            [
                'title' => trim($data['title_es'] ?? '') ?: ($data['title_en'] ?? ''),
                'description' => $data['description_es'] ?? null,
                'meta_title' => Str::limit(trim($data['title_es'] ?? $data['title_en'] ?? '') . ' | ' . config('app.name'), 255, ''),
                'meta_description' => isset($data['description_es'])
                    ? Str::limit(trim(strip_tags($data['description_es'])), 320, '')
                    : (isset($data['description_en']) ? Str::limit(trim(strip_tags($data['description_en'])), 320, '') : null),
            ]
        );
    }

    protected function syncSeo(Property $property, Request $request): void
    {
        if (! $request->has('seo')) {
            return;
        }

        app(SeoEntityService::class)->sync(
            SeoEntityService::TYPE_PROPERTY,
            $property->id,
            $request->input('seo', []),
            'en'
        );
    }

    protected function storeImages(Property $property, Request $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $sort = (int) $property->images()->max('sort_order');

        foreach ($request->file('images') as $file) {
            $property->images()->create([
                'path' => PublicMedia::store($file, 'properties'),
                'alt_text' => $property->title(),
                'sort_order' => ++$sort,
            ]);
        }
    }

    protected function deleteImageFile(string $path): void
    {
        PublicMedia::deleteByUrl($path);
    }
}
