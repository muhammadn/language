<?php

namespace Akaunting\Language\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Language extends Controller
{
    /**
     * Set locale if it's allowed.
     *
     * @param string                   $locale
     * @param \Illuminate\Http\Request $request
     **/
    private function setLocale($locale, $request)
    {
        // Check if is allowed and set default locale if not
        if (!language()->allowed($locale)) {
            $locale = config('app.locale');
        }

        if (Auth::check()) {
            Auth::user()->setAttribute('locale', $locale)->save();
        } else {
            $request->session()->put('locale', $locale);
        }
    }

    /**
     * Set locale and return home url.
     *
     * @param string                   $locale
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     **/
    public function home($locale, Request $request)
    {
        $this->setLocale($locale, $request);

        $url = config('language.url') ? url('/' . $locale) : url('/');

        return redirect($url);
    }

    /**
     * Set locale and return back.
     *
     * @param string                   $locale
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     **/
    public function back($locale, Request $request)
    {
        $this->setLocale($locale, $request);
	dd($locale);

        $session = $request->session();
	$previous_url = substr($previous_url, 2); // remove the old locale string (eg: 'fr') from url.

        if (config('language.url')) {
            $previous_url = str_replace(env('APP_URL'), '', $session->previousUrl());

            $url = rtrim(env('APP_URL'), '/') . '/' . $locale . '/' . ltrim($previous_url, '/');

            $session->setPreviousUrl($url);
        }

        return redirect($session->previousUrl());
    }
}
