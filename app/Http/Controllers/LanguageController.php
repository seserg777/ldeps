<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Switch application language.
     *
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLanguage(string $locale)
    {
        // Validate locale
        $supportedLocales = ['uk', 'ru', 'en'];

        if (!in_array($locale, $supportedLocales)) {
            $locale = 'uk'; // Default to Ukrainian
        }

        // Set locale in session
        Session::put('locale', $locale);
        App::setLocale($locale);

        // Redirect back to previous page
        return Redirect::back();
    }
}
