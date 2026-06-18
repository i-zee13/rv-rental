@extends('admin.layout')

@section('title', $property ? 'Edit Property' : 'Add Property')

@section('content')
<div class="max-w-4xl bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">{{ $property ? 'Edit Property' : 'Add Property' }}</h1>

    <form action="{{ $property ? route('admin.properties.update', $property->id) : route('admin.properties.store') }}"
          method="POST" enctype="multipart/form-data" data-ai-type="property">
        @csrf
        @if($property) @method('PUT') @endif

        @php
            $en = $property?->translations->firstWhere('locale', 'en');
            $es = $property?->translations->firstWhere('locale', 'es');
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block md:col-span-2">
                <div class="text-sm font-medium">Title (English) *</div>
                <input name="title_en" class="w-full border rounded px-2 py-2" value="{{ old('title_en', $en->title ?? '') }}" required>
            </label>

            <label class="block md:col-span-2">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <div class="text-sm">Description (English)</div>
                    <x-admin-ai-desc-btn entity="property" />
                </div>
                <textarea name="description_en" rows="4" class="w-full border rounded px-2 py-2">{{ old('description_en', $en->description ?? '') }}</textarea>
            </label>

            <label class="block md:col-span-2">
                <div class="text-sm font-medium text-indigo-700">Título (Español)</div>
                <input name="title_es" class="w-full border rounded px-2 py-2" value="{{ old('title_es', $es->title ?? '') }}">
            </label>

            <label class="block md:col-span-2">
                <div class="text-sm">Descripción (Español)</div>
                <textarea name="description_es" rows="4" class="w-full border rounded px-2 py-2">{{ old('description_es', $es->description ?? '') }}</textarea>
            </label>

            <label class="block">
                <div class="text-sm">Property Type</div>
                <select name="property_type_id" class="w-full border rounded px-2 py-2">
                    <option value="">— Select —</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ old('property_type_id', $property->property_type_id ?? '') == $type->id ? 'selected' : '' }}>
                            {{ $type->translatedName() }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <div class="text-sm">Status</div>
                <select name="status" class="w-full border rounded px-2 py-2">
                    @foreach(['available','unavailable','rented','hidden'] as $st)
                        <option value="{{ $st }}" {{ old('status', $property->status ?? 'available') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block md:col-span-2">
                <div class="text-sm">Street Address *</div>
                <input name="address_line1" class="w-full border rounded px-2 py-2" value="{{ old('address_line1', $property->address_line1 ?? '') }}" required>
            </label>

            <label class="block">
                <div class="text-sm">Neighborhood</div>
                <input name="neighborhood" class="w-full border rounded px-2 py-2" value="{{ old('neighborhood', $property->neighborhood ?? '') }}">
            </label>

            <label class="block">
                <div class="text-sm">City</div>
                <input name="city" class="w-full border rounded px-2 py-2" value="{{ old('city', $property->city ?? 'Miami') }}">
            </label>

            <label class="block">
                <div class="text-sm">Bedrooms *</div>
                <input type="number" name="bedrooms" min="0" class="w-full border rounded px-2 py-2" value="{{ old('bedrooms', $property->bedrooms ?? 1) }}">
            </label>

            <label class="block">
                <div class="text-sm">Bathrooms *</div>
                <input type="number" step="0.5" name="bathrooms" min="0" class="w-full border rounded px-2 py-2" value="{{ old('bathrooms', $property->bathrooms ?? 1) }}">
            </label>

            <label class="block">
                <div class="text-sm">Sq Ft</div>
                <input type="number" name="sqft" class="w-full border rounded px-2 py-2" value="{{ old('sqft', $property->sqft ?? '') }}">
            </label>

            <label class="block">
                <div class="text-sm">Price / Month (USD) *</div>
                <input type="number" step="0.01" name="price_per_month" class="w-full border rounded px-2 py-2" value="{{ old('price_per_month', $property->price_per_month ?? '') }}">
            </label>

            <label class="block">
                <div class="text-sm">Security Deposit</div>
                <input type="number" step="0.01" name="security_deposit" class="w-full border rounded px-2 py-2" value="{{ old('security_deposit', $property->security_deposit ?? 0) }}">
            </label>

            <label class="block">
                <div class="text-sm">Min Nights</div>
                <input type="number" name="min_nights" class="w-full border rounded px-2 py-2" value="{{ old('min_nights', $property->min_nights ?? 30) }}">
            </label>

            <div class="md:col-span-2 flex flex-wrap gap-4 py-2">
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="featured" value="1" {{ old('featured', $property->featured ?? false) ? 'checked' : '' }}> Featured</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="pets_allowed" value="1" {{ old('pets_allowed', $property->pets_allowed ?? false) ? 'checked' : '' }}> Pet Friendly</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="furnished" value="1" {{ old('furnished', $property->furnished ?? false) ? 'checked' : '' }}> Furnished</label>
            </div>

            <div class="md:col-span-2">
                <div class="text-sm font-medium mb-2">Amenities</div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach(\App\Models\Property::AMENITY_OPTIONS as $key => $label)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="amenities[]" value="{{ $key }}"
                                {{ in_array($key, old('amenities', $property->amenities ?? [])) ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-sm font-medium mb-2">Photos</div>
                <x-dropify-input name="images[]" :multiple="true" :multiple-class="true" height="200"
                    message="Upload property photos (multiple)" />
                @if($property && $property->images->count())
                    <div class="grid grid-cols-4 gap-2 mt-4">
                        @foreach($property->images as $img)
                            <div class="relative">
                                <img src="{{ $img->publicUrl() }}" class="w-full h-20 object-cover rounded border" alt="">
                                <label class="absolute bottom-1 left-1 bg-white/90 text-xs px-1 rounded">
                                    <input type="checkbox" name="delete_image_ids[]" value="{{ $img->id }}"> Remove
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if($property && $property->slug)
            <p class="mt-4 text-sm text-gray-600">Public URL: <a href="{{ route('properties.show', $property) }}" class="text-indigo-600" target="_blank">{{ route('properties.show', $property) }}</a></p>
        @endif

        <x-admin-seo-fields :seo="$seo ?? null" entity="property" />

        <div class="mt-6 flex gap-3">
            <button class="bg-yellow-500 text-black px-4 py-2 rounded font-medium">Save Property</button>
            <a href="{{ route('admin.properties.index') }}" class="px-4 py-2 border rounded">Cancel</a>
        </div>
    </form>
</div>
@endsection
