@props([
    'entity' => 'vehicle',
    'enField' => 'description_en',
    'esField' => 'description_es',
])

<button type="button"
    class="js-ai-generate-desc inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition"
    data-ai-type="{{ $entity }}"
    data-ai-en-field="{{ $enField }}"
    data-ai-es-field="{{ $esField }}"
    title="Generate SEO-friendly descriptions in English and Spanish">
    ✨ Get Description from AI
</button>
