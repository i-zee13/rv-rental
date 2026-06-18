@extends('admin.layout')

@section('title', 'Vehicle Categories')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="text-xl font-semibold">Vehicle Categories</h1>
        <p class="text-sm text-gray-500 mt-1">Used in search filters and vehicle listings</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <form method="POST" action="{{ route('admin.categories.seed-defaults') }}" onsubmit="return confirm('Load default categories (Cars, SUVs, RVs, etc.)? Existing data will not be deleted.')">
            @csrf
            <button type="submit" class="border border-gray-300 px-4 py-2 rounded text-sm">Load Default Categories</button>
        </form>
        <a href="{{ route('admin.categories.create') }}" class="bg-yellow-500 text-black px-4 py-2 rounded font-medium">Add Category</a>
    </div>
</div>

<div class="admin-table-wrap bg-white border rounded-lg p-3">
    <table class="admin-datatable w-full text-sm display">
        <thead class="text-left">
            <tr>
                <th class="p-3">Slug</th>
                <th class="p-3">Name (EN)</th>
                <th class="p-3">Name (ES)</th>
                <th class="p-3">Vehicles</th>
                <th class="p-3">Status</th>
                <th class="p-3 no-sort">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
                @php
                    $en = $cat->translations->firstWhere('locale', 'en');
                    $es = $cat->translations->firstWhere('locale', 'es');
                @endphp
                <tr class="border-t">
                    <td class="p-3 font-mono text-xs">{{ $cat->slug }}</td>
                    <td class="p-3 font-medium">{{ $en->name ?? '—' }}</td>
                    <td class="p-3">{{ $es->name ?? '—' }}</td>
                    <td class="p-3">{{ $cat->vehicles_count }}</td>
                    <td class="p-3">
                        @if($cat->is_active)
                            <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">Inactive</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <x-admin-table-actions
                            :view="route('search', ['category' => $cat->slug])"
                            :edit="route('admin.categories.edit', $cat->id)"
                            :delete-action="route('admin.categories.destroy', $cat->id)"
                            delete-confirm="Delete this category?"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500">
                        No categories yet.
                        <form method="POST" action="{{ route('admin.categories.seed-defaults') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-indigo-600 underline">Load defaults</button>
                        </form>
                        or <a href="{{ route('admin.categories.create') }}" class="text-indigo-600">add one</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
