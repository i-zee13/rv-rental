@extends('admin.layout')

@section('title','Create Post')

@section('content')
<div class="max-w-3xl bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Create Post</h1>

    <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="block mb-2">Slug
            <input name="slug" class="w-full border rounded px-2 py-2" value="{{ old('slug') }}">
        </label>

        <label class="block mb-2">Status
            <select name="status" class="w-full border rounded">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </label>

        <div class="mb-4">
            <div class="text-sm font-medium mb-2">Featured Image</div>
            <x-dropify-input name="featured_image" height="200" message="Blog cover image for listings & social share" />
            @error('featured_image')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <h3 class="font-semibold mt-4">English</h3>
        <label class="block mb-2">Title
            <input name="title_en" class="w-full border rounded px-2 py-2" value="{{ old('title_en') }}">
        </label>
        <label class="block mb-2">Content
            <textarea name="content_en" rows="6" class="w-full border rounded px-2 py-2">{{ old('content_en') }}</textarea>
        </label>

        <h3 class="font-semibold mt-4">Español</h3>
        <label class="block mb-2">Title (es)
            <input name="title_es" class="w-full border rounded px-2 py-2" value="{{ old('title_es') }}">
        </label>
        <label class="block mb-2">Content (es)
            <textarea name="content_es" rows="6" class="w-full border rounded px-2 py-2">{{ old('content_es') }}</textarea>
        </label>

        <div class="mt-4">
            <button class="bg-yellow-500 text-black px-4 py-2 rounded">Save Post</button>
        </div>
    </form>
</div>
@endsection
