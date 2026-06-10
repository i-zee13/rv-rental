@extends('admin.layout')

@section('title','Add Vehicle')

@section('content')
<div class="max-w-3xl bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Add Vehicle</h1>

    @if(session('success'))
        <div class="text-green-600 mb-3">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.vehicles.store') }}" method="POST" enctype="multipart/form-data">
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

            <label class="block">
                <div class="text-sm">Price per day (USD)</div>
                <input name="price_per_day" class="w-full border rounded px-2 py-2" value="{{ old('price_per_day') }}">
                @error('price_per_day')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </label>

            <label class="block md:col-span-2">
                <div class="text-sm">Images (multiple)</div>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full">
                @error('images.*')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </label>

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

        <div class="mt-4">
            <button class="bg-yellow-500 text-black px-4 py-2 rounded">Save Vehicle</button>
        </div>
    </form>
</div>
@endsection
