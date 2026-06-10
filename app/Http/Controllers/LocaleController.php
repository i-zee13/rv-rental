<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function setLocale(Request $request)
    {
        $locale = $request->get('locale', config('app.locale'));
        if (!in_array($locale, ['en','es'])) {
            $locale = config('app.locale');
        }

        session(['app_locale' => $locale]);

        return redirect()->back()->with('success',
            $locale === 'es' ? 'Idioma cambiado a Español.' : 'Language switched to English.'
        );
    }
}
