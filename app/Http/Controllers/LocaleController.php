<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        $locale = in_array($locale, ['en', 'sw'], true) ? $locale : config('app.locale');

        $request->session()->put('locale', $locale);

        return back()->withCookie(cookie()->forever('locale', $locale));
    }
}
