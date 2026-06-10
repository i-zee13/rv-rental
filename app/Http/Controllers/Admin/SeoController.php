<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeoController extends Controller
{
    public function index()
    {
        $metas = SeoMeta::orderByRaw("CASE page_key WHEN 'global' THEN 0 ELSE 1 END")
            ->orderBy('page_key')
            ->orderBy('locale')
            ->get()
            ->groupBy('locale');

        return view('admin.seo.index', compact('metas'));
    }

    public function edit($id)
    {
        $seo = SeoMeta::findOrFail($id);

        return view('admin.seo.edit', compact('seo'));
    }

    public function update(Request $request, $id)
    {
        $seo = SeoMeta::findOrFail($id);

        $data = $request->validate([
            'label' => 'nullable|string|max:191',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:320',
            'meta_keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:70',
            'og_description' => 'nullable|string|max:320',
            'og_image' => 'nullable|string|max:500',
            'og_image_upload' => 'nullable|image|max:2048',
            'og_type' => 'nullable|string|max:50',
            'twitter_card' => 'nullable|string|max:50',
            'twitter_site' => 'nullable|string|max:50',
            'robots' => 'nullable|string|max:100',
            'canonical' => 'nullable|url|max:500',
            'schema_json' => 'nullable|string',
            'google_verification' => 'nullable|string|max:191',
            'bing_verification' => 'nullable|string|max:191',
            'noindex' => 'nullable|boolean',
        ]);

        if ($request->hasFile('og_image_upload')) {
            $path = $request->file('og_image_upload')->store('seo', 'public');
            $data['og_image'] = Storage::url($path);
        }

        unset($data['og_image_upload']);

        if (!empty($data['schema_json'])) {
            $decoded = json_decode($data['schema_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['schema_json' => 'Invalid JSON-LD format.']);
            }
        }

        $seo->update([
            ...$data,
            'noindex' => $request->boolean('noindex'),
        ]);

        return redirect()->route('admin.seo.index')->with('success', 'SEO settings saved for ' . $seo->displayLabel() . '.');
    }
}
