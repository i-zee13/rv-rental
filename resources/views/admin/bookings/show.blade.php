@extends('admin.layout')

@section('title', 'Booking #' . ($booking->reference ?? $booking->id))

@section('content')
<div class="max-w-2xl">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm text-gray-500 hover:text-gray-800 mb-1 inline-flex items-center gap-1">← Back to Bookings</a>
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $booking->reference ?? 'BK-'.$booking->id }}</h1>
        </div>
        <span class="px-3 py-1.5 rounded-full text-sm font-bold
            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700' :
               ($booking->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
            {{ ucfirst($booking->status) }}
        </span>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5 space-y-4">
        <h2 class="font-bold text-gray-900 text-base mb-4 pb-2 border-b border-gray-100">Booking Details</h2>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Vehicle</div>
                <div class="font-semibold text-gray-900">{{ $booking->vehicle->make ?? '—' }} {{ $booking->vehicle->model ?? '' }}</div>
            </div>
            <div>
                <div class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Customer</div>
                <div class="font-semibold text-gray-900">{{ $booking->first_name }} {{ $booking->last_name }}</div>
            </div>
            <div>
                <div class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Start Date</div>
                <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}</div>
            </div>
            <div>
                <div class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">End Date</div>
                <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}</div>
            </div>
            <div>
                <div class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Email</div>
                <div class="font-semibold text-gray-900">{{ $booking->email }}</div>
            </div>
            <div>
                <div class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Phone</div>
                <div class="font-semibold text-gray-900">{{ $booking->phone ?? '—' }}</div>
            </div>
            @if($booking->pickup_location)
            <div>
                <div class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Pickup</div>
                <div class="font-semibold text-gray-900">{{ $booking->pickup_location }}</div>
            </div>
            @endif
            @if($booking->dropoff_location)
            <div>
                <div class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Dropoff</div>
                <div class="font-semibold text-gray-900">{{ $booking->dropoff_location }}</div>
            </div>
            @endif
        </div>

        <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Subtotal</span>
                <span>${{ number_format($booking->subtotal ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Taxes</span>
                <span>${{ number_format($booking->taxes ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold text-base pt-1 border-t border-gray-100">
                <span>Total</span>
                <span>${{ number_format($booking->total ?? 0, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Status Update --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="font-bold text-gray-900 text-base mb-4">Update Status</h2>
        <form method="POST" action="{{ route('admin.bookings.status', $booking->id) }}" class="flex items-center gap-3">
            @csrf
            <select name="status"
                class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="pending" {{ $booking->status=='pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="confirmed" {{ $booking->status=='confirmed' ? 'selected' : '' }}>✅ Confirmed</option>
                <option value="cancelled" {{ $booking->status=='cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
            </select>
            <button class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm hover:bg-indigo-700 transition whitespace-nowrap">
                Update
            </button>
        </form>
    </div>
</div>
@endsection
