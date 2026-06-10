@extends('admin.layout')

@section('title','Edit Post')

@section('content')
<div class="max-w-3xl bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Edit Post</h1>

    <form action="{{ route('admin.blog.update', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <label class="block mb-2">Slug
            <input name="slug" class="w-full border rounded px-2 py-2" value="{{ old('slug', $post->slug) }}">
        </label>

        <label class="block mb-2">Status
            <select name="status" class="w-full border rounded">
                <option value="draft" {{ $post->status=='draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ $post->status=='published' ? 'selected' : '' }}>Published</option>
            </select>
        </label>

        <div class="mb-4">
            <div class="text-sm font-medium mb-2">Featured Image</div>
            <x-dropify-input
                name="featured_image"
                height="200"
                :default-file="$post->featured_image ? asset($post->featured_image) : null"
                message="Blog cover image for listings & social share"
            />
            @error('featured_image')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <h3 class="font-semibold mt-4">English</h3>
        <label class="block mb-2">Title
            <input name="title_en" class="w-full border rounded px-2 py-2" value="{{ old('title_en', $t_en->title ?? '') }}">
        </label>
        <label class="block mb-2">Content
            <textarea name="content_en" rows="6" class="w-full border rounded px-2 py-2">{{ old('content_en', $t_en->content ?? '') }}</textarea>
        </label>

        <h3 class="font-semibold mt-4">Español</h3>
        <label class="block mb-2">Title (es)
            <input name="title_es" class="w-full border rounded px-2 py-2" value="{{ old('title_es', $t_es->title ?? '') }}">
        </label>
        <label class="block mb-2">Content (es)
            <textarea name="content_es" rows="6" class="w-full border rounded px-2 py-2">{{ old('content_es', $t_es->content ?? '') }}</textarea>
        </label>

        <div class="mt-4">
            <button class="bg-yellow-500 text-black px-4 py-2 rounded">Update Post</button>
        </div>
    </form>
</div>
@endsection
