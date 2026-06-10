@extends('admin.layout')

@section('title','Bookings')

@section('content')
<div>
    <h1 class="text-xl font-semibold mb-4">Bookings</h1>
    <div class="admin-table-wrap">
    <table class="w-full bg-white border rounded">
        <thead class="bg-gray-50">
            <tr>
                <th class="p-3 text-left">#</th>
                <th class="p-3 text-left">Reference</th>
                <th class="p-3 text-left">Vehicle</th>
                <th class="p-3 text-left">Dates</th>
                <th class="p-3 text-left">Total</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $b)
                <tr class="border-t">
                    <td class="p-3">{{ $b->id }}</td>
                    <td class="p-3">{{ $b->reference }}</td>
                    <td class="p-3">{{ $b->vehicle->make ?? '' }} {{ $b->vehicle->model ?? '' }}</td>
                    <td class="p-3">{{ $b->start_date }} → {{ $b->end_date }}</td>
                    <td class="p-3">${{ number_format($b->total,2) }}</td>
                    <td class="p-3">{{ ucfirst($b->status) }}</td>
                    <td class="p-3"><a href="{{ route('admin.bookings.show', $b->id) }}" class="text-blue-600">View</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
</div>
@endsection
