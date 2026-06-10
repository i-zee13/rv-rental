@extends('admin.layout')

@section('title','Edit Page')

@section('content')
<div class="max-w-3xl bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Edit Page</h1>

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label class="block mb-2">Slug
            <input name="slug" class="w-full border rounded px-2 py-2" value="{{ old('slug', $page->slug) }}">
            @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </label>

        <label class="block mb-2">Published
            <input type="checkbox" name="is_published" value="1" {{ $page->is_published ? 'checked' : '' }}>
        </label>

        <h3 class="font-semibold mt-4">English</h3>
        <label class="block mb-2">Title
            <input name="title_en" class="w-full border rounded px-2 py-2" value="{{ old('title_en', $t_en->title ?? '') }}">
        </label>
        <label class="block mb-2">Content
            <textarea name="content_en" rows="6" class="w-full border rounded px-2 py-2">{{ old('content_en', $t_en->content ?? '') }}</textarea>
        </label>
        <h4 class="font-semibold mt-3 text-sm text-gray-600">SEO (English)</h4>
        <label class="block mb-2">Meta Title
            <input name="meta_title_en" class="w-full border rounded px-2 py-2" value="{{ old('meta_title_en', $t_en->meta_title ?? '') }}">
        </label>
        <label class="block mb-2">Meta Description
            <textarea name="meta_description_en" rows="2" class="w-full border rounded px-2 py-2">{{ old('meta_description_en', $t_en->meta_description ?? '') }}</textarea>
        </label>

        <h3 class="font-semibold mt-4">Español</h3>
        <label class="block mb-2">Title (es)
            <input name="title_es" class="w-full border rounded px-2 py-2" value="{{ old('title_es', $t_es->title ?? '') }}">
        </label>
        <label class="block mb-2">Content (es)
            <textarea name="content_es" rows="6" class="w-full border rounded px-2 py-2">{{ old('content_es', $t_es->content ?? '') }}</textarea>
        </label>
        <h4 class="font-semibold mt-3 text-sm text-gray-600">SEO (Español)</h4>
        <label class="block mb-2">Meta Title (es)
            <input name="meta_title_es" class="w-full border rounded px-2 py-2" value="{{ old('meta_title_es', $t_es->meta_title ?? '') }}">
        </label>
        <label class="block mb-2">Meta Description (es)
            <textarea name="meta_description_es" rows="2" class="w-full border rounded px-2 py-2">{{ old('meta_description_es', $t_es->meta_description ?? '') }}</textarea>
        </label>

        <div class="mt-4">
            <button class="bg-yellow-500 text-black px-4 py-2 rounded">Update Page</button>
        </div>
    </form>
</div>
@endsection
