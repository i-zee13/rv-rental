@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')

{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    @foreach([
        ['label'=>'Total Bookings','value'=>$totalBookings,'icon'=>'📋','color'=>'bg-blue-50 text-blue-600','border'=>'border-blue-100'],
        ['label'=>'Pending','value'=>$pendingBookings,'icon'=>'⏳','color'=>'bg-yellow-50 text-yellow-600','border'=>'border-yellow-100'],
        ['label'=>'Revenue','value'=>'$'.number_format($revenue,2),'icon'=>'💰','color'=>'bg-green-50 text-green-600','border'=>'border-green-100'],
        ['label'=>'Available Vehicles','value'=>$availableVehicles,'icon'=>'🚗','color'=>'bg-purple-50 text-purple-600','border'=>'border-purple-100'],
        ['label'=>'New Leads','value'=>$newLeads,'icon'=>'📨','color'=>'bg-orange-50 text-orange-600','border'=>'border-orange-100'],
        ['label'=>'Total Leads','value'=>$totalLeads,'icon'=>'📬','color'=>'bg-teal-50 text-teal-600','border'=>'border-teal-100'],
    ] as $stat)
    <div class="bg-white rounded-2xl border {{ $stat['border'] }} shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-500">{{ $stat['label'] }}</div>
            <div class="w-9 h-9 rounded-xl {{ $stat['color'] }} flex items-center justify-center text-lg">{{ $stat['icon'] }}</div>
        </div>
        <div class="text-3xl font-extrabold text-gray-900">{{ $stat['value'] }}</div>
    </div>
    @endforeach
</div>

{{-- Quick Actions --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
    <h2 class="font-bold text-gray-900 mb-4">Quick Actions</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="{{ route('admin.vehicles.create') }}"
            class="flex flex-col items-center gap-2 p-4 bg-indigo-50 border border-indigo-100 rounded-xl hover:bg-indigo-100 transition text-center">
            <span class="text-2xl">➕</span>
            <span class="text-sm font-semibold text-indigo-700">Add Vehicle</span>
        </a>
        <a href="{{ route('admin.bookings.index') }}"
            class="flex flex-col items-center gap-2 p-4 bg-yellow-50 border border-yellow-100 rounded-xl hover:bg-yellow-100 transition text-center">
            <span class="text-2xl">📋</span>
            <span class="text-sm font-semibold text-yellow-700">View Bookings</span>
        </a>
        <a href="{{ route('admin.leads.index') }}"
            class="flex flex-col items-center gap-2 p-4 bg-orange-50 border border-orange-100 rounded-xl hover:bg-orange-100 transition text-center">
            <span class="text-2xl">📨</span>
            <span class="text-sm font-semibold text-orange-700">View Leads</span>
        </a>
        <a href="{{ route('admin.blog.create') }}"
            class="flex flex-col items-center gap-2 p-4 bg-green-50 border border-green-100 rounded-xl hover:bg-green-100 transition text-center">
            <span class="text-2xl">✍️</span>
            <span class="text-sm font-semibold text-green-700">New Blog Post</span>
        </a>
        <a href="{{ route('admin.pages.create') }}"
            class="flex flex-col items-center gap-2 p-4 bg-purple-50 border border-purple-100 rounded-xl hover:bg-purple-100 transition text-center">
            <span class="text-2xl">📄</span>
            <span class="text-sm font-semibold text-purple-700">New Page</span>
        </a>
    </div>
</div>

{{-- Management Links --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    <a href="{{ route('admin.vehicles.index') }}"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center text-2xl">🚗</div>
            <div>
                <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition">Vehicles</div>
                <div class="text-xs text-gray-500">Manage fleet inventory</div>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.bookings.index') }}"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 flex items-center justify-center text-2xl">📋</div>
            <div>
                <div class="font-bold text-gray-900 group-hover:text-yellow-600 transition">Bookings</div>
                <div class="text-xs text-gray-500">Reservations & payments</div>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.blog.index') }}"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center text-2xl">✍️</div>
            <div>
                <div class="font-bold text-gray-900 group-hover:text-green-600 transition">Blog</div>
                <div class="text-xs text-gray-500">Posts & content</div>
            </div>
        </div>
    </a>
</div>

@endsection
