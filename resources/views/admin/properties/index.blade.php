@extends('admin.layout')

@section('title', 'Properties')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Homes & Apartments</h1>
    <a href="{{ route('admin.properties.create') }}" class="bg-yellow-500 text-black px-4 py-2 rounded font-medium">+ Add Property</a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">Listing</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Beds/Baths</th>
                <th class="px-4 py-3">Price/mo</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($properties as $property)
                @php $t = $property->translation(); @endphp
                <tr class="border-t">
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $t->title ?? $property->address_line1 }}</div>
                        <div class="text-gray-500 text-xs">{{ $property->reference }} · {{ $property->neighborhood ?? $property->city }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $property->type?->translatedName() ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $property->bedrooms }} bd / {{ $property->bathrooms }} ba</td>
                    <td class="px-4 py-3">${{ number_format($property->price_per_month, 0) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs {{ $property->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100' }}">
                            {{ ucfirst($property->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('properties.show', $property) }}" class="text-blue-600 mr-3" target="_blank">View</a>
                        <a href="{{ route('admin.properties.edit', $property->id) }}" class="text-yellow-700">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No properties yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $properties->links() }}</div>
@endsection
