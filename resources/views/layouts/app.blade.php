<!DOCTYPE html>
<html lang="{{ \App\Support\Locales::htmlLang() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lobby') — Lobby</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Inter:wght@400;500;600;700;800&family=Press+Start+2P&display=swap" rel="stylesheet">

    <style>
        :root {
            --background: #17131f;
            --surface: #211b2b;
            --purple: #a78bfa;
            --purple-light: #c4b5fd;
            --text: #f2edf9;
            --muted: #a69caf;
            --border: #393044;
            --green: #b6e3a1;
            --danger: #f2a1b0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--background);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }

        a { color: inherit; text-decoration: none; }

        .container { width: min(1100px, calc(100% - 40px)); margin: 0 auto; }

        header { border-bottom: 1px solid var(--border); }

        .navbar {
            min-height: 78px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .logo {
            font-family: 'Press Start 2P', monospace;
            font-size: 17px;
            color: var(--purple-light);
            letter-spacing: -2px;
        }
        .logo span { color: var(--text); }

        .nav-links { display: flex; align-items: center; gap: 22px; }
        .nav-links a, .nav-links button {
            background: none;
            border: 0;
            color: var(--muted);
            font: inherit;
            font-size: 13px;
            cursor: pointer;
            transition: color .2s;
        }
        .nav-links a:hover, .nav-links button:hover { color: var(--text); }
        .nav-links .nav-button {
            border: 1px solid var(--purple);
            padding: 10px 16px;
            color: var(--purple-light);
        }
        .nav-links .nav-button:hover { background: var(--purple-light); color: var(--background); }

        main { flex: 1; display: flex; align-items: center; padding: 60px 0; }

        .card {
            width: min(440px, 100%);
            margin: 0 auto;
            padding: 36px;
            background: var(--surface);
            border: 1px solid var(--border);
        }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            color: var(--green);
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .eyebrow::before { content: ""; width: 8px; height: 8px; background: var(--green); }

        h1 { font-size: 32px; line-height: 1.15; letter-spacing: -1.5px; font-weight: 800; }
        .subtitle { margin: 10px 0 28px; color: var(--muted); font-size: 14px; }

        .field { margin-bottom: 18px; }
        label { display: block; margin-bottom: 7px; font-size: 12px; font-weight: 600; color: var(--purple-light); }

        input[type=text], input[type=email], input[type=password] {
            width: 100%;
            padding: 13px 14px;
            background: var(--background);
            border: 1px solid var(--border);
            color: var(--text);
            font: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color .2s;
        }
        input:focus { border-color: var(--purple); }
        input.invalid { border-color: var(--danger); }

        .error { margin-top: 6px; color: var(--danger); font-size: 12px; }

        .check { display: flex; align-items: center; gap: 8px; margin-bottom: 22px; color: var(--muted); font-size: 13px; }
        .check input { accent-color: var(--purple); }

        .button-primary {
            width: 100%;
            padding: 15px 20px;
            border: 0;
            background: var(--purple-light);
            color: #21172e;
            font: inherit;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }
        .button-primary:hover { background: #ded2ff; transform: translateY(-2px); }

        .alt { margin-top: 22px; text-align: center; color: var(--muted); font-size: 13px; }
        .alt a { color: var(--purple-light); font-weight: 600; }
        .alt a:hover { text-decoration: underline; }

        @media (max-width: 560px) {
            .container { width: calc(100% - 32px); }
            .logo { font-size: 14px; }
            .card { padding: 26px 22px; }
            main { padding: 36px 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header>
        <div class="container navbar">
            <a href="{{ auth()->check() ? route('home') : route('landing') }}" class="logo" aria-label="{{ __('Lobby - início') }}">LOB<span>BY</span></a>

            <nav class="nav-links" aria-label="{{ __('Navegação principal') }}">
                <form method="POST" action="{{ route('locale.update') }}">
                    @csrf
                    <input type="hidden" name="locale" value="{{ app()->getLocale() === 'en' ? 'pt_BR' : 'en' }}">
                    <button type="submit" aria-label="{{ app()->getLocale() === 'en' ? 'Mudar para Português (BR)' : 'Switch to English (US)' }}">{{ app()->getLocale() === 'en' ? 'PT-BR' : 'EN-US' }}</button>
                </form>

                @auth
                    <span style="color: var(--muted); font-size: 13px;">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">{{ __('Sair') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">{{ __('Entrar') }}</a>
                    <a href="{{ route('register') }}" class="nav-button">{{ __('Criar conta') }}</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            @yield('content')
        </div>
    </main>
    @include('partials.gubbo')
</body>
</html>
