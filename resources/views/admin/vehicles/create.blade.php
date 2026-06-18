@extends('admin.layout')

@section('title','Add Vehicle')

@section('content')
<div class="max-w-3xl bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Add Vehicle</h1>

    @if(session('success'))
        <div class="text-green-600 mb-3">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.vehicles.store') }}" method="POST" enctype="multipart/form-data" data-ai-type="vehicle">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
                <div class="text-sm">Make</div>
                <input name="make" class="w-full border rounded px-2 py-2" value="{{ old('make') }}">
                @error('make')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </label>

            <label class="block">
                <div class="text-sm">Model</div>
                <input name="model" class="w-full border rounded px-2 py-2" value="{{ old('model') }}">
                @error('model')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </label>

            <label class="block">
                <div class="text-sm">Year</div>
                <input name="year" class="w-full border rounded px-2 py-2" value="{{ old('year') }}">
            </label>

            <label class="block md:col-span-2">
                <div class="text-sm">Category</div>
                <select name="category_id" class="w-full border rounded px-2 py-2">
                    <option value="">— Select category —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->translatedName('en') }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                <p class="text-xs text-gray-500 mt-1"><a href="{{ route('admin.categories.create') }}" class="text-indigo-600">Add new category</a></p>
            </label>

            <label class="block">
                <div class="text-sm">Price per day (USD)</div>
                <input name="price_per_day" class="w-full border rounded px-2 py-2" value="{{ old('price_per_day') }}">
                @error('price_per_day')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </label>

            <div class="block md:col-span-2">
                <div class="text-sm font-medium mb-2">Vehicle Images</div>
                <x-dropify-input
                    name="images[]"
                    :multiple="true"
                    :multiple-class="true"
                    height="200"
                    message="Drag & drop vehicle photos (select multiple)"
                />
                @error('images.*')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                <p class="text-xs text-gray-500 mt-2">PNG, JPG, WEBP — max 4MB each</p>
            </div>

            <label class="block">
                <div class="text-sm">Seats</div>
                <input name="seats" class="w-full border rounded px-2 py-2" value="{{ old('seats',4) }}">
            </label>

            <label class="block">
                <div class="text-sm">Bags</div>
                <input name="bags" class="w-full border rounded px-2 py-2" value="{{ old('bags',2) }}">
            </label>

            <label class="block md:col-span-2">
                <div class="text-sm">Status</div>
                <select name="status" class="w-full border rounded px-2 py-2">
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="booked">Booked</option>
                    <option value="hidden">Hidden</option>
                </select>
            </label>
        </div>

        <div class="mt-6 border-t pt-6">
            <h3 class="font-semibold text-gray-800 mb-3">English Content</h3>
            <div class="grid grid-cols-1 gap-4">
                <label class="block">
                    <div class="text-sm">Display Title (English)</div>
                    <input name="title_en" class="w-full border rounded px-2 py-2" value="{{ old('title_en') }}" placeholder="Leave blank to use Make + Model">
                </label>
                <label class="block">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <div class="text-sm">Description (English)</div>
                        <x-admin-ai-desc-btn entity="vehicle" />
                    </div>
                    <textarea name="description_en" rows="4" class="w-full border rounded px-2 py-2">{{ old('description_en') }}</textarea>
                </label>
            </div>
        </div>

        <div class="mt-6 border-t pt-6">
            <h3 class="font-semibold text-gray-800 mb-3">Contenido en Español</h3>
            <div class="grid grid-cols-1 gap-4">
                <label class="block">
                    <div class="text-sm">Título (Español)</div>
                    <input name="title_es" class="w-full border rounded px-2 py-2" value="{{ old('title_es') }}">
                </label>
                <label class="block">
                    <div class="text-sm">Descripción (Español)</div>
                    <textarea name="description_es" rows="4" class="w-full border rounded px-2 py-2">{{ old('description_es') }}</textarea>
                </label>
            </div>
        </div>

        <x-admin-seo-fields entity="vehicle" />

        <div class="mt-4">
            <button class="bg-yellow-500 text-black px-4 py-2 rounded">Save Vehicle</button>
        </div>
    </form>
</div>
@endsection
