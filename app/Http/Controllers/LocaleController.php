<?php

namespace App\Http\Controllers;

use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    /** Troca o idioma (PT-BR / US). Funciona logado ou não. */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'locale' => ['required', Rule::in(array_keys(Locales::SUPPORTED))],
        ]);

        $locale = $data['locale'];

        $request->session()->put('locale', $locale);

        if ($user = $request->user()) {
            $settings = $user->settings ?? [];
            data_set($settings, 'locale', $locale);
            $user->update(['settings' => $settings]);
        }

        // Volta para a página de onde veio (só dentro do próprio site).
        $back = url()->previous(url('/'));
        $target = str_starts_with($back, url('/')) ? $back : url('/');

        return redirect()->to($target)->withCookie(cookie()->forever('locale', $locale));
    }
}
