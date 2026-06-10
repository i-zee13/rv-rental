@extends('admin.layout')

@section('title', 'SEO Settings')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="text-xl font-semibold">SEO Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Manage meta tags, Open Graph &amp; JSON-LD via <strong>artesaos/seotools</strong></p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 border border-green-200 rounded-lg px-4 py-3 mb-4 text-sm">{{ session('success') }}</div>
@endif

<div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6 text-sm text-blue-900">
    <strong>How it works:</strong> Global defaults apply to every page. Per-page settings override globals.
    Blog posts, CMS pages, and vehicles use their own SEO fields when set — otherwise fallbacks below apply.
</div>

@foreach($metas as $locale => $items)
<div class="mb-8">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-3">{{ strtoupper($locale) }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($items as $seo)
        <div class="bg-white border rounded-xl p-5 hover:shadow-md transition {{ $seo->page_key === 'global' ? 'ring-2 ring-indigo-200' : '' }}">
            <div class="flex items-start justify-between gap-2 mb-2">
                <h3 class="font-semibold text-gray-900">{{ $seo->displayLabel() }}</h3>
                @if($seo->noindex)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">noindex</span>
                @endif
            </div>
            <p class="text-xs text-gray-400 font-mono mb-3">{{ $seo->page_key }}</p>
            <p class="text-sm text-gray-600 line-clamp-2 mb-1"><strong>Title:</strong> {{ $seo->meta_title ?: '—' }}</p>
            <p class="text-sm text-gray-500 line-clamp-2 mb-4"><strong>Desc:</strong> {{ Str::limit($seo->meta_description, 90) ?: '—' }}</p>
            <a href="{{ route('admin.seo.edit', $seo->id) }}" class="text-indigo-600 text-sm font-medium hover:underline">Edit SEO →</a>
        </div>
        @endforeach
    </div>
</div>
@endforeach
@endsection
