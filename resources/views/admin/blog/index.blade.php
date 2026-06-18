@extends('admin.layout')

@section('title','Blog Posts')

@section('content')
<div class="admin-page-header">
    <h1 class="text-xl font-semibold">Blog Posts</h1>
    <a href="{{ route('admin.blog.create') }}" class="bg-yellow-500 text-black px-3 py-2 rounded">Add Post</a>
</div>

@if(session('success'))<div class="text-green-600 mb-3">{{ session('success') }}</div>@endif

<div class="admin-table-wrap bg-white border rounded-lg p-3">
<table class="admin-datatable w-full display">
    <thead>
        <tr>
            <th class="p-3 text-left">#</th>
            <th class="p-3 text-left">Slug</th>
            <th class="p-3 text-left">Title (EN)</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left no-sort">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($posts as $p)
            @php $t = $p->translations->firstWhere('locale','en') ?? $p->translations->first(); @endphp
            <tr class="border-t">
                <td class="p-3">{{ $p->id }}</td>
                <td class="p-3">{{ $p->slug }}</td>
                <td class="p-3">{{ $t->title ?? '-' }}</td>
                <td class="p-3">{{ $p->status }}</td>
                <td class="p-3">
                    <x-admin-table-actions
                        :view="$p->status === 'published' ? route('blog.show', $p->slug) : null"
                        :edit="route('admin.blog.edit', $p->id)"
                        :delete-action="route('admin.blog.destroy', $p->id)"
                        delete-confirm="Delete this post?"
                    />
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection
