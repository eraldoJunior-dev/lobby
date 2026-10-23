@php
    $ap = auth()->check() ? auth()->user()->appearance() : \App\Support\Appearance::DEFAULTS;
    $otherLocale = app()->getLocale() === 'en' ? 'pt_BR' : 'en';
@endphp
<!DOCTYPE html>
<html lang="{{ \App\Support\Locales::htmlLang() }}"
      data-theme="{{ $ap['theme'] }}" data-accent="{{ $ap['accent'] }}" data-density="{{ $ap['density'] }}"
      data-text="{{ $ap['text_size'] }}" @if ($ap['reduce_motion']) data-motion="reduce" @endif @if ($ap['high_contrast']) data-contrast="high" @endif
      data-spoilers="{{ $ap['hide_spoilers'] ? 'hide' : 'show' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="appearance-url" content="{{ route('settings.appearance') }}">
    <title>@yield('title', 'Lobby') — Lobby</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Press+Start+2P&display=swap" rel="stylesheet">

    {{-- Se uma capa não carregar, tenta a próxima fonte; sem nenhuma, some a imagem e aparece o degradê com o título. --}}
    <script>
        window.LOBBY_I18N = {
            saved: @json(__('Salvo')), save: @json(__('Salvar')),
            following: @json(__('Seguindo')), follow: @json(__('Seguir')),
            sessionExpired: @json(__('Sua sessão expirou. Recarregue a página.')),
            failed: @json(__('Não foi possível concluir. Tente de novo.')),
        };
        window.lobbyCoverFail = function (img) {
            try {
                var alts = JSON.parse(img.getAttribute('data-alts') || '[]');
                var next = alts.shift();
                if (next) { img.setAttribute('data-alts', JSON.stringify(alts)); img.src = next; return; }
            } catch (e) {}
            img.remove();
        };
    </script>

    <link rel="stylesheet" href="{{ asset('css/feed.css') }}?v={{ filemtime(public_path('css/feed.css')) }}">
<style>
.lobby-platform-logo{display:block;width:32px;height:32px;object-fit:contain;flex-shrink:0}
.lobby-platform-logo--inline{display:inline-block;width:17px;height:17px;vertical-align:-3px;margin-left:4px}
.lobby-platform-choice .lobby-platform-logo{margin:0 auto 6px}
.lobby-profile-platform{display:inline-flex;align-items:center}
@media(max-width:600px){.lobby-platform-logo{width:27px;height:27px}.lobby-platform-logo--inline{width:16px;height:16px}}
</style>
<style>
.lobby-modal-symbol .icon {width:44px;height:44px;vertical-align:middle;stroke-width:1.6}
.lobby-notification-empty > .icon {display:block;width:34px;height:34px;margin:0 auto 12px}
.lobby-notification-icon > .icon {width:21px;height:21px;vertical-align:middle}
</style>
</head>
<body>
    @include('partials.icons')
    @include('partials.platform-onboarding')

    <div class="shell">

        {{-- MENU LATERAL --}}
        <aside class="sidebar">
            <a href="{{ route('home') }}" class="brand" aria-label="{{ __('Lobby — início') }}">LOB<span>BY</span></a>

            @php
                // [rota, ícone, nome, padrões de rota que deixam o item ativo] — rota nula = ainda não existe.
                $navItems = [
                    ['home', 'home', 'Início', ['home']],
                    ['games.index', 'gamepad', 'Jogos', ['games.*']],
                    ['guides.index', 'book', 'Guias', ['guides.*']],
                    [null, 'list', 'Listas', []],
                    ['forums.index', 'forum', 'Fóruns', ['forums.*', 'threads.*']],
                    ['news.index', 'news', 'Notícias', ['news.*']],
                    ['chat', 'chat', 'Chat', ['chat']],
                    ['notifications.index', 'bell', 'Notificações', ['notifications.*']],
                    ['profile.show', 'user', 'Perfil', ['profile.*']],
                    ['settings.edit', 'sliders', 'Configurações', ['settings.*']],
                ];
            @endphp
            <nav class="nav" aria-label="{{ __('Navegação principal') }}">
                @foreach ($navItems as [$route, $icon, $label, $patterns])
                    @if ($route)
                        @php($active = $patterns && request()->routeIs(...$patterns))
                        <a href="{{ route($route) }}" @class(['nav-item', 'is-active' => $active]) @if ($active) aria-current="page" @endif>
                            <x-icon :name="$icon" /><span class="nav-label">{{ __($label) }}</span>@if ($route === 'notifications.index' && ($unreadCount = auth()->user()->unreadNotifications()->count()))<span class="lobby-unread-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>@endif
                        </a>
                    @else
                        {{-- Ainda não existe; aparece apagado para mostrar o que vem por aí --}}
                        <span class="nav-item is-soon" aria-disabled="true" title="{{ __(':label — em breve', ['label' => __($label)]) }}">
                            <x-icon :name="$icon" /><span class="nav-label">{{ __($label) }}</span>
                        </span>
                    @endif
                @endforeach
            </nav>

            @if (request()->routeIs('home'))
                <a href="#composer" class="new-post" data-focus-composer>
                    <x-icon name="plus" /><span class="nav-label">{{ __('Nova publicação') }}</span>
                </a>
            @else
                <a href="{{ route('home') }}#composer" class="new-post">
                    <x-icon name="plus" /><span class="nav-label">{{ __('Nova publicação') }}</span>
                </a>
            @endif

            <div class="side-foot">
                <div class="me" title="{{ auth()->user()->name }}">
                    <x-avatar :user="auth()->user()" :size="36" />
                    <span class="nav-label me-text">
                        <strong>{{ auth()->user()->name }}</strong>
                        @if (auth()->user()->username)
                            <small>{{ '@'.auth()->user()->username }} @foreach (\App\Support\Platforms::selected(auth()->user()) as $platform)<span title="{{ \App\Support\Platforms::OPTIONS[$platform][0] }}"><img src="{{ asset('assets/platforms/'.$platform.'.svg') }}" alt="" class="lobby-platform-logo lobby-platform-logo--inline"></span>@endforeach</small>
                        @endif
                    </span>
                </div>

                <button type="button" class="ghost-btn" data-theme-cycle aria-label="{{ __('Trocar tema') }}" title="{{ __('Trocar tema') }}">
                    <x-icon name="palette" />
                    <span class="nav-label theme-btn-label"><span class="tn tn-purple">{{ __('Tema roxo') }}</span><span class="tn tn-dark">{{ __('Tema preto') }}</span><span class="tn tn-light">{{ __('Tema branco') }}</span></span>
                </button>

                <form method="POST" action="{{ route('locale.update') }}">
                    @csrf
                    <input type="hidden" name="locale" value="{{ $otherLocale }}">
                    <button type="submit" class="ghost-btn" aria-label="{{ $otherLocale === 'en' ? 'Switch to English (US)' : 'Mudar para Português (BR)' }}" title="{{ $otherLocale === 'en' ? 'English (US)' : 'Português (BR)' }}">
                        <x-icon name="globe" /><span class="nav-label">{{ $otherLocale === 'en' ? 'English (US)' : 'Português (BR)' }}</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="ghost-btn" aria-label="{{ __('Sair') }}">
                        <x-icon name="logout" /><span class="nav-label">{{ __('Sair') }}</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- FEED --}}
        <main class="center">
            @yield('content')
        </main>

        {{-- COLUNA DA DIREITA --}}
        <aside class="rail" aria-label="{{ __('Destaques') }}">
            @hasSection('rail')
                @yield('rail')
            @else
                @include('partials.rail')
            @endif
        </aside>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>
    <script src="{{ asset('js/feed.js') }}?v={{ filemtime(public_path('js/feed.js')) }}" defer></script>
    @include('partials.gubbo')
</body>
</html>
