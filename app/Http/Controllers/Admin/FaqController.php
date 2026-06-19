<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::with('translations')->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateFaq($request);

        $faq = Faq::create([
            'scope' => $data['scope'],
            'page_keys' => $data['scope'] === Faq::SCOPE_GENERAL ? ($data['page_keys'] ?? []) : null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->syncTranslations($faq, $data);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created.');
    }

    public function edit($id)
    {
        $faq = Faq::with('translations')->findOrFail($id);

        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);
        $data = $this->validateFaq($request);

        $faq->update([
            'scope' => $data['scope'],
            'page_keys' => $data['scope'] === Faq::SCOPE_GENERAL ? ($data['page_keys'] ?? []) : null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->syncTranslations($faq, $data);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated.');
    }

    public function destroy($id)
    {
        Faq::findOrFail($id)->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ removed.');
    }

    protected function validateFaq(Request $request): array
    {
        $data = $request->validate([
            'scope' => 'required|in:general,vehicle,property',
            'page_keys' => 'nullable|array',
            'page_keys.*' => 'string|in:'.implode(',', array_keys(Faq::PAGE_OPTIONS)),
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'question_en' => 'required|string|max:500',
            'answer_en' => 'required|string|max:5000',
            'question_es' => 'nullable|string|max:500',
            'answer_es' => 'nullable|string|max:5000',
        ]);

        if ($data['scope'] === Faq::SCOPE_GENERAL && empty($data['page_keys'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'page_keys' => 'Select at least one page for general FAQs.',
            ]);
        }

        return $data;
    }

    protected function syncTranslations(Faq $faq, array $data): void
    {
        $faq->translations()->updateOrCreate(
            ['locale' => 'en'],
            [
                'question' => trim($data['question_en']),
                'answer' => trim($data['answer_en']),
            ]
        );

        $questionEs = trim($data['question_es'] ?? '') ?: trim($data['question_en']);
        $answerEs = trim($data['answer_es'] ?? '') ?: trim($data['answer_en']);

        $faq->translations()->updateOrCreate(
            ['locale' => 'es'],
            [
                'question' => $questionEs,
                'answer' => $answerEs,
            ]
        );
    }
}
