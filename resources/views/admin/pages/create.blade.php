@extends('admin.layout')

@section('title','Create Page')

@section('content')
<div class="max-w-3xl bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Create Page</h1>

    <form action="{{ route('admin.pages.store') }}" method="POST">
        @csrf
        <label class="block mb-2">Slug
            <input name="slug" class="w-full border rounded px-2 py-2" value="{{ old('slug') }}">
            @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </label>

        <label class="block mb-2">Published
            <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
        </label>

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
            <button class="bg-yellow-500 text-black px-4 py-2 rounded">Save Page</button>
        </div>
    </form>
</div>
@endsection
