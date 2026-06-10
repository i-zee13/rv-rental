@extends('admin.layout')

@section('title','Blog Posts')

@section('content')
<div class="admin-page-header">
    <h1 class="text-xl font-semibold">Blog Posts</h1>
    <a href="{{ route('admin.blog.create') }}" class="bg-yellow-500 text-black px-3 py-2 rounded">Add Post</a>
</div>

@if(session('success'))<div class="text-green-600 mb-3">{{ session('success') }}</div>@endif

<div class="admin-table-wrap">
<table class="w-full bg-white border rounded">
    <thead class="bg-gray-50">
        <tr>
            <th class="p-3 text-left">#</th>
            <th class="p-3 text-left">Slug</th>
            <th class="p-3 text-left">Title (EN)</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Actions</th>
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
                    <a href="{{ route('admin.blog.edit', $p->id) }}" class="text-blue-600 mr-2">Edit</a>
                    <form action="{{ route('admin.blog.destroy', $p->id) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>

<div class="mt-4">{{ $posts->links() }}</div>
@endsection
