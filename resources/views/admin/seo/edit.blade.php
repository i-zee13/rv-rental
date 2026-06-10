@extends('admin.layout')

@section('title', 'Edit SEO — ' . $seo->displayLabel())

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.seo.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Back to SEO</a>
    <h1 class="text-xl font-semibold mt-2 mb-1">Edit SEO</h1>
    <p class="text-sm text-gray-500 mb-6">{{ $seo->displayLabel() }} <span class="font-mono text-xs">({{ $seo->page_key }} / {{ $seo->locale }})</span></p>

    <form method="POST" action="{{ route('admin.seo.update', $seo->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        <section class="bg-white border rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-gray-900">Basic Meta</h2>
            <div>
                <label class="block text-sm font-medium mb-1">Meta Title <span class="text-gray-400">(50–60 chars ideal)</span></label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $seo->meta_title) }}" maxlength="70"
                    class="w-full border rounded-lg px-3 py-2 text-sm" data-char-count="meta_title_count">
                <p class="text-xs text-gray-400 mt-1" id="meta_title_count"></p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Meta Description <span class="text-gray-400">(150–160 chars ideal)</span></label>
                <textarea name="meta_description" rows="3" maxlength="320" class="w-full border rounded-lg px-3 py-2 text-sm" data-char-count="meta_desc_count">{{ old('meta_description', $seo->meta_description) }}</textarea>
                <p class="text-xs text-gray-400 mt-1" id="meta_desc_count"></p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Keywords <span class="text-gray-400">(comma separated)</span></label>
                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seo->meta_keywords) }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Miami car rental, luxury cars">
            </div>
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium mb-1">Robots</label>
                    <select name="robots" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @foreach(['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'] as $r)
                        <option value="{{ $r }}" {{ old('robots', $seo->robots) === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="flex items-center gap-2 text-sm mt-6">
                    <input type="checkbox" name="noindex" value="1" {{ old('noindex', $seo->noindex) ? 'checked' : '' }}>
                    Force noindex
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Canonical URL <span class="text-gray-400">(optional)</span></label>
                <input type="url" name="canonical" value="{{ old('canonical', $seo->canonical) }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="https://mvmiamirental.com/">
            </div>
        </section>

        <section class="bg-white border rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-gray-900">Open Graph &amp; Social</h2>
            <div>
                <label class="block text-sm font-medium mb-1">OG Title</label>
                <input type="text" name="og_title" value="{{ old('og_title', $seo->og_title) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">OG Description</label>
                <textarea name="og_description" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('og_description', $seo->og_description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">OG Image URL</label>
                <input type="text" name="og_image" value="{{ old('og_image', $seo->og_image) }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="/theme/img/carousel-1.jpg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Upload OG Image</label>
                <x-dropify-input
                    name="og_image_upload"
                    :default-file="$seo->og_image ? asset($seo->og_image) : null"
                    height="180"
                    message="Drag & drop social share image (1200×630 recommended)"
                />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">OG Type</label>
                    <input type="text" name="og_type" value="{{ old('og_type', $seo->og_type) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Twitter Card</label>
                    <select name="twitter_card" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @foreach(['summary', 'summary_large_image'] as $c)
                        <option value="{{ $c }}" {{ old('twitter_card', $seo->twitter_card) === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Twitter @site</label>
                <input type="text" name="twitter_site" value="{{ old('twitter_site', $seo->twitter_site) }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="@mvmiamirental">
            </div>
        </section>

        @if($seo->page_key === 'global')
        <section class="bg-white border rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-gray-900">Webmaster Verification</h2>
            <div>
                <label class="block text-sm font-medium mb-1">Google Site Verification</label>
                <input type="text" name="google_verification" value="{{ old('google_verification', $seo->google_verification) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Bing Site Verification</label>
                <input type="text" name="bing_verification" value="{{ old('bing_verification', $seo->bing_verification) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </section>
        @endif

        <section class="bg-white border rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-gray-900">JSON-LD Schema <span class="text-gray-400 font-normal text-sm">(optional)</span></h2>
            <textarea name="schema_json" rows="8" class="w-full border rounded-lg px-3 py-2 text-sm font-mono" placeholder='{"@type":"LocalBusiness","name":"MV Miami Rental"}'>{{ old('schema_json', $seo->schema_json) }}</textarea>
            @error('schema_json')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        </section>

        <div class="flex gap-3">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium">Save SEO</button>
            <a href="{{ route('admin.seo.index') }}" class="border px-6 py-2.5 rounded-lg">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelectorAll('[data-char-count]').forEach(function (el) {
    var counter = document.getElementById(el.dataset.charCount);
    function update() {
        if (counter) counter.textContent = el.value.length + ' characters';
    }
    el.addEventListener('input', update);
    update();
});
</script>
@endpush
@endsection
