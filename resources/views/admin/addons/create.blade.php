@extends('admin.layout')

@section('title', 'Add Add-on')

@section('content')
<h1 class="text-xl font-semibold mb-4">New Add-on</h1>

<form method="POST" action="{{ route('admin.addons.store') }}" class="bg-white border rounded-lg p-6 max-w-lg space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">Code (unique)</label>
        <input type="text" name="code" value="{{ old('code') }}" class="w-full border rounded-lg px-3 py-2" placeholder="gps" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Title</label>
        <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded-lg px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('description') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Price ($)</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', '0') }}" class="w-full border rounded-lg px-3 py-2" required>
    </div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> Active</label>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_taxable" value="1" checked> Taxable</label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Save</button>
        <a href="{{ route('admin.addons.index') }}" class="border px-4 py-2 rounded-lg">Cancel</a>
    </div>
</form>
@endsection
