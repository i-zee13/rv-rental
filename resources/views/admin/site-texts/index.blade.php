@extends('admin.layout')

@section('title', 'Site Texts (EN / ES)')

@section('content')
<div class="max-w-5xl">
    <div class="admin-page-header">
        <div>
            <h1 class="text-xl font-semibold">Site Texts — English & Spanish</h1>
            <p class="text-sm text-gray-500 mt-1">Edit navigation, footer, hero and homepage static text. Visitors see changes when they switch language.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-800 border border-green-200 rounded-lg px-4 py-3 mb-4 text-sm">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.site-texts.update') }}" class="space-y-8">
        @csrf
        @method('PUT')

        @php
            $groupLabels = [
                'nav' => 'Navigation',
                'footer' => 'Footer',
                'hero' => 'Homepage Hero',
                'home' => 'Homepage Sections',
            ];
            $byGroup = collect($definitions)->groupBy(fn($d) => $d['group']);
        @endphp

        @foreach($byGroup as $group => $items)
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b">
                    <h2 class="font-semibold text-gray-800">{{ $groupLabels[$group] ?? ucfirst($group) }}</h2>
                </div>
                <div class="divide-y">
                    @foreach($items as $key => $def)
                        @php
                            $enVal = old("texts.{$key}.en", $stored->get($key)?->firstWhere('locale', 'en')?->value ?? $def['default_en']);
                            $esFile = lang_path('es/ui.php');
                            $esDefaults = is_file($esFile) ? require $esFile : [];
                            $esVal = old("texts.{$key}.es", $stored->get($key)?->firstWhere('locale', 'es')?->value ?? ($esDefaults[$key] ?? ''));
                        @endphp
                        <div class="p-5 grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="lg:col-span-1">
                                <div class="text-sm font-medium text-gray-800">{{ $def['label'] }}</div>
                                <div class="text-xs text-gray-400 font-mono mt-1">{{ $key }}</div>
                            </div>
                            <label class="block">
                                <span class="text-xs font-semibold text-gray-500 uppercase">English</span>
                                <textarea name="texts[{{ $key }}][en]" rows="{{ str_contains($key, '_text') || str_contains($key, '_sub') ? 3 : 2 }}"
                                    class="w-full border rounded-lg px-3 py-2 text-sm mt-1">{{ $enVal }}</textarea>
                            </label>
                            <label class="block">
                                <span class="text-xs font-semibold text-gray-500 uppercase">Español</span>
                                <textarea name="texts[{{ $key }}][es]" rows="{{ str_contains($key, '_text') || str_contains($key, '_sub') ? 3 : 2 }}"
                                    class="w-full border rounded-lg px-3 py-2 text-sm mt-1">{{ $esVal }}</textarea>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex gap-3">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium">Save All Texts</button>
            <a href="{{ route('home') }}" target="_blank" class="px-6 py-2.5 border rounded-lg text-sm">Preview Website</a>
        </div>
    </form>
</div>
@endsection
