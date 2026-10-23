@php
    $themeOpts = ['purple' => __('Roxo'), 'dark' => __('Preto'), 'light' => __('Branco')];
    $accentOpts = ['purple' => ['#7c5ce6', __('Roxo')], 'teal' => ['#14a89c', __('Verde-água')], 'orange' => ['#e8671c', __('Laranja')], 'pink' => ['#e0457b', __('Rosa')], 'blue' => ['#1f7be0', __('Azul')], 'green' => ['#2f9e44', __('Verde')]];
    $densityOpts = ['comfortable' => __('Confortável'), 'compact' => __('Compacto')];
    $textOpts = ['normal' => __('Normal'), 'large' => __('Grande')];
@endphp

<h2 class="panel-title">{{ __('Aparência') }}</h2>
<p class="muted panel-text">{{ __('Os ajustes valem na hora e ficam salvos na sua conta.') }} <span id="ap-saved" class="ap-saved" role="status" data-saved="{{ __('Salvo.') }}" data-error="{{ __('Não foi possível salvar. Tente de novo.') }}"></span></p>

<div class="ap-list">
    <div class="ap-row">
        <div class="ap-label">
            <strong>{{ __('Tema') }}</strong>
            <small>{{ __('Roxo (padrão), preto ou branco') }}</small>
        </div>
        <div class="seg" role="group" aria-label="{{ __('Tema') }}">
            @foreach ($themeOpts as $value => $label)
                <button type="button" class="seg-btn" data-ap-key="theme" data-ap-value="{{ $value }}" aria-pressed="{{ $appearance['theme'] === $value ? 'true' : 'false' }}">
                    <span class="seg-dot t-{{ $value }}"></span>{{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="ap-row">
        <div class="ap-label">
            <strong>{{ __('Cor de destaque') }}</strong>
            <small>{{ __('Botões, links e realces') }}</small>
        </div>
        <div class="accent-picks" role="group" aria-label="{{ __('Cor de destaque') }}">
            @foreach ($accentOpts as $value => [$hex, $label])
                <button type="button" class="accent-dot" style="--c: {{ $hex }}" data-ap-key="accent" data-ap-value="{{ $value }}"
                        aria-label="{{ $label }}" title="{{ $label }}" aria-pressed="{{ $appearance['accent'] === $value ? 'true' : 'false' }}"></button>
            @endforeach
        </div>
    </div>

    <div class="ap-row">
        <div class="ap-label">
            <strong>{{ __('Densidade') }}</strong>
            <small>{{ __('O modo compacto mostra mais coisas na tela') }}</small>
        </div>
        <div class="seg" role="group" aria-label="{{ __('Densidade') }}">
            @foreach ($densityOpts as $value => $label)
                <button type="button" class="seg-btn" data-ap-key="density" data-ap-value="{{ $value }}" aria-pressed="{{ $appearance['density'] === $value ? 'true' : 'false' }}">{{ $label }}</button>
            @endforeach
        </div>
    </div>

    <div class="ap-row">
        <div class="ap-label">
            <strong>{{ __('Tamanho do texto') }}</strong>
            <small>{{ __('Deixa tudo um pouco maior') }}</small>
        </div>
        <div class="seg" role="group" aria-label="{{ __('Tamanho do texto') }}">
            @foreach ($textOpts as $value => $label)
                <button type="button" class="seg-btn" data-ap-key="text_size" data-ap-value="{{ $value }}" aria-pressed="{{ $appearance['text_size'] === $value ? 'true' : 'false' }}">{{ $label }}</button>
            @endforeach
        </div>
    </div>

    <label class="switch-row">
        <span><strong>{{ __('Reduzir movimento') }}</strong><small class="muted">{{ __('Desliga animações e transições') }}</small></span>
        <input type="checkbox" class="switch" data-ap-toggle="reduce_motion" @checked($appearance['reduce_motion'])>
    </label>

    <label class="switch-row">
        <span><strong>{{ __('Alto contraste') }}</strong><small class="muted">{{ __('Bordas e textos mais fortes') }}</small></span>
        <input type="checkbox" class="switch" data-ap-toggle="high_contrast" @checked($appearance['high_contrast'])>
    </label>

    <label class="switch-row">
        <span><strong>{{ __('Esconder spoilers por padrão') }}</strong><small class="muted">{{ __('Publicações com spoiler ficam ocultas até você tocar') }}</small></span>
        <input type="checkbox" class="switch" data-ap-toggle="hide_spoilers" @checked($appearance['hide_spoilers'])>
    </label>

    <div class="ap-row">
        <div class="ap-label">
            <strong>{{ __('Idioma') }}</strong>
            <small>{{ __('Português (Brasil) ou inglês (EUA)') }}</small>
        </div>
        <form class="lang-form" method="POST" action="{{ route('locale.update') }}">
            @csrf
            <select name="locale" class="field" aria-label="{{ __('Idioma') }}" data-autosubmit>
                @foreach ($locales as $code => $name)
                    <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $code === 'pt_BR' ? 'PT-BR · ' : 'US · ' }}{{ $name }}</option>
                @endforeach
            </select>
            <noscript><button type="submit" class="btn-outline">OK</button></noscript>
        </form>
    </div>
</div>
