@extends('admin.layout')

@section('title','Edit Vehicle')

@section('content')
<div class="max-w-3xl bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Edit Vehicle</h1>

    @if(session('success'))
        <div class="text-green-600 mb-3">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.vehicles.update', $vehicle->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
                <div class="text-sm">Make</div>
                <input name="make" class="w-full border rounded px-2 py-2" value="{{ old('make', $vehicle->make) }}">
                @error('make')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </label>

            <label class="block">
                <div class="text-sm">Model</div>
                <input name="model" class="w-full border rounded px-2 py-2" value="{{ old('model', $vehicle->model) }}">
                @error('model')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </label>

            <label class="block">
                <div class="text-sm">Year</div>
                <input name="year" class="w-full border rounded px-2 py-2" value="{{ old('year', $vehicle->year) }}">
            </label>

            <label class="block">
                <div class="text-sm">Price per day (USD)</div>
                <input name="price_per_day" class="w-full border rounded px-2 py-2" value="{{ old('price_per_day', $vehicle->price_per_day) }}">
                @error('price_per_day')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </label>

            <div class="block md:col-span-2">
                <div class="text-sm font-medium mb-2">Add More Images</div>
                <x-dropify-repeater name="images[]" group-id="vehicle-images-edit" height="160" />
                @error('images.*')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <label class="block">
                <div class="text-sm">Seats</div>
                <input name="seats" class="w-full border rounded px-2 py-2" value="{{ old('seats', $vehicle->seats) }}">
            </label>

            <label class="block">
                <div class="text-sm">Bags</div>
                <input name="bags" class="w-full border rounded px-2 py-2" value="{{ old('bags', $vehicle->bags) }}">
            </label>

            <label class="block md:col-span-2">
                <div class="text-sm">Status</div>
                <select name="status" class="w-full border rounded px-2 py-2">
                    <option value="available" {{ old('status', $vehicle->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status', $vehicle->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    <option value="maintenance" {{ old('status', $vehicle->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="booked" {{ old('status', $vehicle->status) == 'booked' ? 'selected' : '' }}>Booked</option>
                    <option value="hidden" {{ old('status', $vehicle->status) == 'hidden' ? 'selected' : '' }}>Hidden</option>
                </select>
            </label>
        </div>

        @php
            $en = $vehicle->translations->firstWhere('locale', 'en');
            $es = $vehicle->translations->firstWhere('locale', 'es');
        @endphp

        <div class="mt-6 border-t pt-6">
            <h3 class="font-semibold text-gray-800 mb-3">English Content</h3>
            <div class="grid grid-cols-1 gap-4">
                <label class="block">
                    <div class="text-sm">Display Title (English)</div>
                    <input name="title_en" class="w-full border rounded px-2 py-2" value="{{ old('title_en', $en->title ?? '') }}" placeholder="Leave blank to use Make + Model">
                </label>
                <label class="block">
                    <div class="text-sm">Description (English)</div>
                    <textarea name="description_en" rows="4" class="w-full border rounded px-2 py-2">{{ old('description_en', $en->description ?? '') }}</textarea>
                </label>
            </div>
        </div>

        <div class="mt-6 border-t pt-6">
            <h3 class="font-semibold text-gray-800 mb-3">Contenido en Español</h3>
            <div class="grid grid-cols-1 gap-4">
                <label class="block">
                    <div class="text-sm">Título (Español)</div>
                    <input name="title_es" class="w-full border rounded px-2 py-2" value="{{ old('title_es', $es->title ?? '') }}">
                </label>
                <label class="block">
                    <div class="text-sm">Descripción (Español)</div>
                    <textarea name="description_es" rows="4" class="w-full border rounded px-2 py-2">{{ old('description_es', $es->description ?? '') }}</textarea>
                </label>
            </div>
        </div>

        <div class="mt-4">
            <button class="bg-yellow-500 text-black px-4 py-2 rounded">Update Vehicle</button>
        </div>
    </form>

    @if($vehicle->images && $vehicle->images->count())
        <div class="mt-6">
            <h3 class="font-semibold mb-2">Existing Images</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($vehicle->images as $img)
                    <div class="border rounded p-2">
                        <img src="{{ $img->publicUrl() }}" alt="" class="w-full h-32 object-cover mb-2">
                        <form method="POST" action="{{ route('admin.vehicles.images.destroy', [$vehicle->id, $img->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="w-full bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
