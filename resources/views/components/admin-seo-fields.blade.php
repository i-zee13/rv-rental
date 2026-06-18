@props(['seo' => null, 'prefix' => 'seo', 'entity' => 'vehicle'])

<div class="mt-6 border-t pt-6 js-admin-seo-section" data-ai-type="{{ $entity }}" data-seo-prefix="{{ $prefix }}">
    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
        <div>
            <h3 class="font-semibold text-gray-800 mb-1">SEO (optional)</h3>
            <p class="text-xs text-gray-500">Leave blank to use auto-generated titles and global defaults. OG image: add manually or we use uploaded photos on the live page.</p>
        </div>
        <button type="button"
            class="js-ai-generate-seo inline-flex items-center gap-1 text-xs font-medium px-3 py-1.5 rounded-full border border-violet-200 bg-violet-50 text-violet-700 hover:bg-violet-100 transition shrink-0"
            title="Generate meta title, description, keywords and OG text from AI">
            ✨ Generate SEO from AI
        </button>
    </div>
    <div class="grid grid-cols-1 gap-4">
        <label class="block">
            <div class="text-sm">Meta Title</div>
            <input name="{{ $prefix }}[meta_title]" class="w-full border rounded px-2 py-2"
                value="{{ old($prefix.'.meta_title', $seo->meta_title ?? '') }}" maxlength="255">
        </label>
        <label class="block">
            <div class="text-sm">Meta Description</div>
            <textarea name="{{ $prefix }}[meta_description]" rows="2" class="w-full border rounded px-2 py-2" maxlength="320">{{ old($prefix.'.meta_description', $seo->meta_description ?? '') }}</textarea>
        </label>
        <label class="block">
            <div class="text-sm">Meta Keywords</div>
            <input name="{{ $prefix }}[meta_keywords]" class="w-full border rounded px-2 py-2"
                value="{{ old($prefix.'.meta_keywords', $seo->meta_keywords ?? '') }}" placeholder="Miami rental, luxury car">
        </label>
        <label class="block">
            <div class="text-sm">OG Title</div>
            <input name="{{ $prefix }}[og_title]" class="w-full border rounded px-2 py-2"
                value="{{ old($prefix.'.og_title', $seo->og_title ?? '') }}">
        </label>
        <label class="block">
            <div class="text-sm">OG Description</div>
            <textarea name="{{ $prefix }}[og_description]" rows="2" class="w-full border rounded px-2 py-2">{{ old($prefix.'.og_description', $seo->og_description ?? '') }}</textarea>
        </label>
        <label class="block">
            <div class="text-sm">OG Image URL</div>
            <input name="{{ $prefix }}[og_image]" class="w-full border rounded px-2 py-2"
                value="{{ old($prefix.'.og_image', $seo->og_image ?? '') }}" placeholder="/storage/... or https://... (optional)">
            <p class="text-xs text-gray-500 mt-1">Leave empty to use the listing&apos;s uploaded images automatically.</p>
        </label>
        <label class="block">
            <div class="text-sm">Canonical URL (optional override)</div>
            <input name="{{ $prefix }}[canonical]" class="w-full border rounded px-2 py-2"
                value="{{ old($prefix.'.canonical', $seo->canonical ?? '') }}" placeholder="https://...">
        </label>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="{{ $prefix }}[noindex]" value="1"
                {{ old($prefix.'.noindex', $seo->noindex ?? false) ? 'checked' : '' }}>
            Hide from search engines (noindex)
        </label>
    </div>
</div>
