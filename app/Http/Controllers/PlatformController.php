<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
class PlatformController extends Controller {
    public function update(Request $request): RedirectResponse {
        $data = $request->validate(['platforms' => ['required','array','min:1','max:5'], 'platforms.*' => ['string', Rule::in(array_keys(\App\Support\Platforms::OPTIONS))]]);
        $user = $request->user(); $settings = $user->settings ?? [];
        $settings['gaming_platforms'] = array_values(array_unique($data['platforms']));
        $settings['platforms_onboarded'] = true;
        $user->settings = $settings; $user->save();
        return back()->with('status', 'Plataformas salvas no perfil!');
    }
}
