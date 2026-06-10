@extends('admin.layout')

@section('title','Vehicles')

@section('content')
<div class="admin-page-header">
    <h1 class="text-xl font-semibold">Vehicles</h1>
    <a href="{{ route('admin.vehicles.create') }}" class="bg-yellow-500 text-black px-3 py-2 rounded">Add Vehicle</a>
</div>

<div class="admin-table-wrap">
<table class="w-full bg-white border rounded">
    <thead class="bg-gray-50">
        <tr>
            <th class="p-3 text-left">#</th>
            <th class="p-3 text-left">Title</th>
            <th class="p-3 text-left">Category</th>
            <th class="p-3 text-left">Price/day</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vehicles as $v)
            @php $t = $v->translations->firstWhere('locale', app()->getLocale()) ?? $v->translations->first(); @endphp
            <tr class="border-t">
                <td class="p-3">{{ $v->id }}</td>
                <td class="p-3">{{ $t->title ?? $v->make.' '.$v->model }}</td>
                <td class="p-3">{{ $v->category->translations->firstWhere('locale', app()->getLocale())->name ?? $v->category->slug ?? '-' }}</td>
                <td class="p-3">${{ number_format($v->price_per_day,2) }}</td>
                <td class="p-3">{{ ucfirst($v->status) }}</td>
                <td class="p-3">
                    <a href="{{ route('admin.vehicles.edit', $v->id) }}" class="text-blue-600 mr-2">Edit</a>
                    <form action="#" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>

<div class="mt-4">{{ $vehicles->links() }}</div>
@endsection
