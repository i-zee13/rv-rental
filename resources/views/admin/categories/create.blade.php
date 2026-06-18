@extends('admin.layout')

@section('title', 'Add Category')

@section('content')
<h1 class="text-xl font-semibold mb-4">New Vehicle Category</h1>

<form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white border rounded-lg p-6 max-w-lg space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">Name (English) *</label>
        <input type="text" name="name_en" value="{{ old('name_en') }}" class="w-full border rounded-lg px-3 py-2" required>
        @error('name_en')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Nombre (Español)</label>
        <input type="text" name="name_es" value="{{ old('name_es') }}" class="w-full border rounded-lg px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Slug (optional)</label>
        <input type="text" name="slug" value="{{ old('slug') }}" class="w-full border rounded-lg px-3 py-2" placeholder="auto from English name">
        @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}> Active (show on website)
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-yellow-500 text-black px-4 py-2 rounded font-medium">Save Category</button>
        <a href="{{ route('admin.categories.index') }}" class="border px-4 py-2 rounded-lg">Cancel</a>
    </div>
</form>
@endsection
