@extends('admin.layout')

@section('title','Vehicles')

@section('content')
<div class="admin-page-header">
    <h1 class="text-xl font-semibold">Vehicles</h1>
    <a href="{{ route('admin.vehicles.create') }}" class="bg-yellow-500 text-black px-3 py-2 rounded">Add Vehicle</a>
</div>

<div class="admin-table-wrap bg-white border rounded-lg p-3">
<table class="admin-datatable w-full display">
    <thead>
        <tr>
            <th class="p-3 text-left">#</th>
            <th class="p-3 text-left">Title</th>
            <th class="p-3 text-left">Category</th>
            <th class="p-3 text-left">Price/day</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left no-sort">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vehicles as $v)
            @php
                $t = $v->translations->firstWhere('locale', app()->getLocale()) ?? $v->translations->first();
                $categoryLabel = '-';
                if ($v->category) {
                    $catT = $v->category->translations->firstWhere('locale', app()->getLocale())
                        ?? $v->category->translations->first();
                    $categoryLabel = $catT->name ?? $v->category->slug ?? '-';
                }
            @endphp
            <tr class="border-t">
                <td class="p-3">{{ $v->id }}</td>
                <td class="p-3">{{ $t->title ?? $v->make.' '.$v->model }}</td>
                <td class="p-3">{{ $categoryLabel }}</td>
                <td class="p-3">${{ number_format($v->price_per_day,2) }}</td>
                <td class="p-3">{{ ucfirst($v->status) }}</td>
                <td class="p-3">
                    <x-admin-table-actions
                        :view="$v->slug ? route('vehicles.show', $v) : null"
                        :edit="route('admin.vehicles.edit', $v->id)"
                    />
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection
