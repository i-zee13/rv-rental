@php
    $en = $faq?->translations->firstWhere('locale', 'en');
    $es = $faq?->translations->firstWhere('locale', 'es');
@endphp

<form method="POST" action="{{ $faq ? route('admin.faqs.update', $faq->id) : route('admin.faqs.store') }}"
    class="bg-white border rounded-lg p-6 max-w-3xl space-y-5"
    x-data="{ scope: @json(old('scope', $faq->scope ?? 'general')) }">
    @csrf
    @if($faq) @method('PUT') @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Type / Scope *</label>
            <select name="scope" x-model="scope" class="w-full border rounded-lg px-3 py-2" required>
                @foreach(\App\Models\Faq::SCOPES as $value => $label)
                    <option value="{{ $value }}" {{ old('scope', $faq->scope ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('scope')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Sort order</label>
            <input type="number" name="sort_order" min="0" max="9999"
                value="{{ old('sort_order', $faq->sort_order ?? 0) }}"
                class="w-full border rounded-lg px-3 py-2">
            <p class="text-xs text-gray-500 mt-1">Lower numbers appear first.</p>
        </div>
    </div>

    <div x-show="scope === 'general'" x-cloak class="border rounded-lg p-4 bg-gray-50">
        <div class="text-sm font-medium mb-2">Show on pages *</div>
        <p class="text-xs text-gray-500 mb-3">General FAQs only appear on the pages you select below.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach(\App\Models\Faq::PAGE_OPTIONS as $key => $label)
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="page_keys[]" value="{{ $key }}"
                        {{ in_array($key, old('page_keys', $faq->page_keys ?? [])) ? 'checked' : '' }}>
                    {{ $label }}
                </label>
            @endforeach
        </div>
        @error('page_keys')<div class="text-red-600 text-sm mt-2">{{ $message }}</div>@enderror
    </div>

    <div x-show="scope === 'vehicle'" x-cloak class="text-sm text-indigo-800 bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-3">
        This FAQ will appear on <strong>every vehicle detail page</strong> automatically.
    </div>
    <div x-show="scope === 'property'" x-cloak class="text-sm text-indigo-800 bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-3">
        This FAQ will appear on <strong>every home / apartment detail page</strong> automatically.
    </div>

    <div class="border-t pt-5">
        <h3 class="font-semibold mb-3">English</h3>
        <label class="block mb-3">
            <span class="text-sm font-medium">Question *</span>
            <input type="text" name="question_en" value="{{ old('question_en', $en->question ?? '') }}"
                class="w-full border rounded-lg px-3 py-2 mt-1" required maxlength="500">
            @error('question_en')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </label>
        <label class="block">
            <span class="text-sm font-medium">Answer *</span>
            <textarea name="answer_en" rows="4" class="w-full border rounded-lg px-3 py-2 mt-1" required maxlength="5000">{{ old('answer_en', $en->answer ?? '') }}</textarea>
            @error('answer_en')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </label>
    </div>

    <div class="border-t pt-5">
        <h3 class="font-semibold mb-3">Español</h3>
        <label class="block mb-3">
            <span class="text-sm font-medium">Pregunta</span>
            <input type="text" name="question_es" value="{{ old('question_es', $es->question ?? '') }}"
                class="w-full border rounded-lg px-3 py-2 mt-1" maxlength="500">
        </label>
        <label class="block">
            <span class="text-sm font-medium">Respuesta</span>
            <textarea name="answer_es" rows="4" class="w-full border rounded-lg px-3 py-2 mt-1" maxlength="5000">{{ old('answer_es', $es->answer ?? '') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Leave blank to copy English text.</p>
        </label>
    </div>

    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}>
        Active (visible on website)
    </label>

    <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-yellow-500 text-black px-4 py-2 rounded font-medium">Save FAQ</button>
        <a href="{{ route('admin.faqs.index') }}" class="border px-4 py-2 rounded-lg">Cancel</a>
    </div>
</form>
