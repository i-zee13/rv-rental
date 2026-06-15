<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteText;
use Illuminate\Http\Request;

class SiteTextController extends Controller
{
    public function index()
    {
        $definitions = SiteText::defaultDefinitions();
        $stored = SiteText::all()->groupBy('key');

        return view('admin.site-texts.index', compact('definitions', 'stored'));
    }

    public function update(Request $request)
    {
        $definitions = SiteText::defaultDefinitions();
        $texts = $request->input('texts', []);

        foreach ($definitions as $key => $def) {
            foreach (['en', 'es'] as $locale) {
                $value = trim($texts[$key][$locale] ?? '');
                if ($value === '') {
                    SiteText::where('key', $key)->where('locale', $locale)->delete();
                    continue;
                }

                SiteText::updateOrCreate(
                    ['key' => $key, 'locale' => $locale],
                    [
                        'value' => $value,
                        'label' => $def['label'],
                        'group' => $def['group'],
                    ]
                );
            }
        }

        return back()->with('success', 'Site texts updated. Changes apply immediately on the website.');
    }
}
