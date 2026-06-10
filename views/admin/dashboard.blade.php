@extends('admin.layout')

@section('title','Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white border rounded p-4">
        <div class="text-sm text-gray-500">Total Bookings</div>
        <div class="text-2xl font-bold">{{ $totalBookings }}</div>
    </div>
    <div class="bg-white border rounded p-4">
        <div class="text-sm text-gray-500">Pending</div>
        <div class="text-2xl font-bold">{{ $pendingBookings }}</div>
    </div>
    <div class="bg-white border rounded p-4">
        <div class="text-sm text-gray-500">Revenue</div>
        <div class="text-2xl font-bold">${{ number_format($revenue,2) }}</div>
    </div>
    <div class="bg-white border rounded p-4">
        <div class="text-sm text-gray-500">Available Vehicles</div>
        <div class="text-2xl font-bold">{{ $availableVehicles }}</div>
    </div>
</div>

<div class="mt-6">
    <h3 class="font-semibold">Quick Actions</h3>
    <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="{{ route('admin.vehicles.index') }}" class="block bg-white border rounded p-3 text-center">Manage Vehicles</a>
        <a href="#" class="block bg-white border rounded p-3 text-center">Manage Bookings</a>
        <a href="#" class="block bg-white border rounded p-3 text-center">Pages & CMS</a>
        <a href="#" class="block bg-white border rounded p-3 text-center">Reports</a>
    </div>
</div>
@endsection
