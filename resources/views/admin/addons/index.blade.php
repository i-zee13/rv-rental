@extends('admin.layout')

@section('title', 'Add-ons')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="text-xl font-semibold">Booking Add-ons</h1>
        <p class="text-sm text-gray-500 mt-1">Optional extras shown in booking step 2</p>
    </div>
    <a href="{{ route('admin.addons.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium">Add Add-on</a>
</div>

@if(session('success'))<div class="text-green-600 mb-3">{{ session('success') }}</div>@endif

<div class="admin-table-wrap bg-white border rounded-lg p-3">
    <table class="admin-datatable w-full text-sm display">
        <thead class="text-left text-xs uppercase text-gray-500">
            <tr>
                <th class="p-3">Code</th>
                <th class="p-3">Title</th>
                <th class="p-3">Price</th>
                <th class="p-3">Status</th>
                <th class="p-3 no-sort">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($addons as $addon)
            @php $t = $addon->translation('en'); @endphp
            <tr class="border-t">
                <td class="p-3 font-mono text-xs">{{ $addon->code }}</td>
                <td class="p-3 font-medium">{{ $t?->title ?? '—' }}</td>
                <td class="p-3">${{ number_format($addon->price, 2) }}</td>
                <td class="p-3">
                    @if($addon->is_active)
                        <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs">Active</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">Inactive</span>
                    @endif
                </td>
                <td class="p-3">
                    <x-admin-table-actions
                        :edit="route('admin.addons.edit', $addon->id)"
                        :delete-action="route('admin.addons.destroy', $addon->id)"
                        delete-confirm="Delete this add-on?"
                    />
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="p-6 text-center text-gray-500">No add-ons yet. <a href="{{ route('admin.addons.create') }}" class="text-indigo-600">Create one</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
