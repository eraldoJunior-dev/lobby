<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Ordem: preferência salva na conta > sessão > cookie > idioma padrão do app.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->setting('locale');

        if (! Locales::isSupported($locale)) {
            $locale = $request->hasSession() ? $request->session()->get('locale') : null;
        }

        if (! Locales::isSupported($locale)) {
            $locale = $request->cookie('locale');
        }

        if (! Locales::isSupported($locale)) {
            $locale = config('app.locale');
        }

        if (! Locales::isSupported($locale)) {
            $locale = Locales::DEFAULT;
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
