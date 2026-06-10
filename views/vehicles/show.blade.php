@extends('layouts.app')

@section('title', $vehicle->translations->firstWhere('locale', app()->getLocale())->title ?? $vehicle->make.' '.$vehicle->model)

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2">
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-2">
                @foreach($vehicle->images as $img)
                    <img src="{{ $img->path }}" alt="{{ $img->alt_text ?? '' }}" class="w-full h-48 object-cover rounded">
                @endforeach
            </div>

            <h1 class="text-2xl font-bold">{{ $vehicle->translations->firstWhere('locale', app()->getLocale())->title ?? $vehicle->make.' '.$vehicle->model }}</h1>
            <div class="text-gray-600">{{ $vehicle->translations->firstWhere('locale', app()->getLocale())->description ?? '' }}</div>

            <section class="mt-4">
                <h3 class="font-semibold">Specifications</h3>
                <ul class="grid grid-cols-2 gap-2 mt-2 text-sm text-gray-700">
                    <li>Seats: {{ $vehicle->seats }}</li>
                    <li>Bags: {{ $vehicle->bags }}</li>
                    <li>Transmission: {{ $vehicle->transmission ?? 'Auto' }}</li>
                    <li>Fuel: {{ $vehicle->fuel_type ?? 'Gas' }}</li>
                </ul>
            </section>
        </div>
    </div>

    <aside class="sticky top-6">
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">From</div>
            <div class="text-2xl font-bold">${{ number_format($vehicle->price_per_day,2) }}<span class="text-base font-normal">/day</span></div>
            <form action="{{ route('search') }}" method="GET" class="mt-4">
                <input type="hidden" name="q" value="{{ $vehicle->translations->firstWhere('locale', app()->getLocale())->title ?? $vehicle->make.' '.$vehicle->model }}">
                <button class="w-full bg-yellow-500 text-black px-4 py-2 rounded font-semibold">Book Now</button>
            </form>
        </div>
    </aside>
</div>
@endsection
