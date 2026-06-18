@extends('admin.layout')

@section('title', 'Properties')

@section('content')
<div class="admin-page-header">
    <h1 class="text-2xl font-semibold">Homes & Apartments</h1>
    <a href="{{ route('admin.properties.create') }}" class="bg-yellow-500 text-black px-4 py-2 rounded font-medium">+ Add Property</a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="admin-table-wrap bg-white rounded-lg border p-3">
    <table class="admin-datatable min-w-full text-sm display">
        <thead class="text-left">
            <tr>
                <th class="px-4 py-3">Listing</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Beds/Baths</th>
                <th class="px-4 py-3">Price/mo</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 no-sort">Actions</th>
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
                    <td class="px-4 py-3">
                        <x-admin-table-actions
                            :view="route('properties.show', $property)"
                            :edit="route('admin.properties.edit', $property->id)"
                        />
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No properties yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
