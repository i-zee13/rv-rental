@extends('admin.layout')

@section('title','Bookings')

@section('content')
<div class="admin-page-header">
    <h1 class="text-xl font-semibold">Bookings</h1>
</div>

<div class="admin-table-wrap bg-white border rounded-lg p-3">
    <table class="admin-datatable w-full display">
        <thead>
            <tr>
                <th class="p-3 text-left">#</th>
                <th class="p-3 text-left">Reference</th>
                <th class="p-3 text-left">Vehicle</th>
                <th class="p-3 text-left">Dates</th>
                <th class="p-3 text-left">Total</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-left no-sort">Actions</th>
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
                    <td class="p-3">
                        <x-admin-table-actions
                            :view="route('admin.bookings.show', $b->id)"
                            view-target="_self"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
