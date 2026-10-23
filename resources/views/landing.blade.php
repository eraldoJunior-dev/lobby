<!DOCTYPE html>
<html lang="{{ \App\Support\Locales::htmlLang() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ __('Lobby — Sua comunidade gamer') }}</title>

    <meta name="description" content="{{ __('O Lobby é uma rede social gamer para compartilhar opiniões sobre jogos, encontrar comunidades e conectar jogadores do mundo todo.') }}">

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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 90px;
        }

        body {
            background: var(--background);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }

        a {
            color: inherit;
            text-decoration: none;
            -webkit-tap-highlight-color: transparent;
        }

        .container {
            width: min(1100px, calc(100% - 40px));
            margin: 0 auto;
        }

        /* CABEÇALHO */

        header {
            border-bottom: 1px solid var(--border);
        }

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

        .logo span {
            color: var(--text);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav-links a {
            color: var(--muted);
            font-size: 13px;
            transition: color .2s;
        }

        .nav-links button {
            background: none;
            border: 0;
            color: var(--muted);
            font: inherit;
            font-size: 13px;
            cursor: pointer;
            transition: color .2s;
        }
        .nav-links button:hover { color: var(--text); }

        .nav-links a:hover {
            color: var(--text);
        }

        .nav-links .nav-button {
            border: 1px solid var(--purple);
            padding: 10px 16px;
            color: var(--purple-light);
        }

        .nav-links .nav-button:hover {
            background: var(--purple-light);
            color: var(--background);
        }

        /* INÍCIO */

        .hero {
            min-height: 590px;
            display: flex;
            align-items: center;
            padding: 80px 0;
            border-bottom: 1px solid var(--border);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            align-items: center;
            gap: 65px;
        }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 27px;
            color: var(--green);
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 8px;
            height: 8px;
            background: var(--green);
        }

        h1 {
            max-width: 650px;
            font-size: clamp(45px, 6vw, 75px);
            line-height: 1.08;
            letter-spacing: -4px;
            font-weight: 800;
        }

        h1 span {
            color: var(--purple-light);
        }

        .hero-description {
            max-width: 460px;
            margin-top: 25px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.9;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 22px;
            margin-top: 32px;
        }

        .button-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 15px 20px;
            background: var(--purple-light);
            color: #21172e;
            font-size: 13px;
            font-weight: 800;
            transition: background .2s, transform .2s;
        }

        .button-primary:hover {
            background: #ded2ff;
            transform: translateY(-2px);
        }

        .text-link {
            color: var(--muted);
            font-size: 13px;
        }

        .text-link:hover {
            color: var(--text);
        }

        /* ESPAÇO RESERVADO PARA O MASCOTE */

        .mascot-area {
            min-height: 340px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            border: 1px dashed #554666;
            background: #1d1727;
        }

        .mascot-area::before,
        .mascot-area::after {
            content: "";
            position: absolute;
            width: 55px;
            height: 55px;
            border-color: #4a3b5c;
            border-style: solid;
        }

        .mascot-area::before {
            top: 15px;
            left: 15px;
            border-width: 1px 0 0 1px;
        }

        .mascot-area::after {
            right: 15px;
            bottom: 15px;
            border-width: 0 1px 1px 0;
        }

        .mascot-placeholder {
            color: #7d688f;
            font-family: 'Press Start 2P', monospace;
            font-size: 38px;
            letter-spacing: -5px;
        }

        .mascot-note {
            margin-top: 20px;
            color: #8e7d9e;
            font-family: 'DM Mono', monospace;
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .mascot-caption {
            position: absolute;
            bottom: 18px;
            left: 18px;
            color: #796b89;
            font-family: 'DM Mono', monospace;
            font-size: 10px;
        }

        /* APRESENTAÇÃO */

        .about {
            padding: 80px 0;
            border-bottom: 1px solid var(--border);
        }

        .about-grid {
            display: grid;
            grid-template-columns: .7fr 1.3fr;
            gap: 70px;
            align-items: start;
        }

        .section-label {
            margin-bottom: 18px;
            color: var(--purple-light);
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        h2 {
            font-size: clamp(30px, 4vw, 43px);
            line-height: 1.2;
            letter-spacing: -1.8px;
        }

        .about-text {
            color: var(--muted);
            font-size: 16px;
            line-height: 2;
        }

        .about-text strong {
            color: var(--text);
            font-weight: 600;
        }

        /* FUNCIONALIDADES */

        .features {
            padding: 85px 0;
            border-bottom: 1px solid var(--border);
        }

        .features-heading {
            max-width: 600px;
            margin-bottom: 45px;
        }

        .features-heading h2 {
            margin-bottom: 15px;
        }

        .features-heading p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.9;
        }

        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .feature {
            min-height: 230px;
            padding: 30px;
            background: var(--surface);
            border: 1px solid var(--border);
            transition: border-color .2s, transform .2s;
        }

        .feature:hover {
            border-color: #76608f;
            transform: translateY(-3px);
        }

        .feature-number {
            margin-bottom: 28px;
            color: var(--purple-light);
            font-family: 'DM Mono', monospace;
            font-size: 11px;
        }

        .feature h3 {
            margin-bottom: 12px;
            font-size: 23px;
            letter-spacing: -.5px;
        }

        .feature p {
            max-width: 390px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.9;
        }

        /* CHAMADA FINAL */

        .final-cta {
            padding: 90px 0;
            text-align: center;
        }

        .final-cta .eyebrow {
            justify-content: center;
        }

        .final-cta h2 {
            max-width: 650px;
            margin: 0 auto;
            font-size: clamp(35px, 5vw, 55px);
        }

        .final-cta p {
            max-width: 460px;
            margin: 20px auto 30px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.9;
        }

        /* RODAPÉ */

        footer {
            border-top: 1px solid var(--border);
            padding: 25px 0;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .footer-copy {
            color: var(--muted);
            font-size: 11px;
        }

        .footer-note {
            color: #796b89;
            font-family: 'DM Mono', monospace;
            font-size: 10px;
        }

        /* CELULARES E TABLETS */

        @media (max-width: 800px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 45px;
            }

            .hero {
                padding: 65px 0;
            }

            .mascot-area {
                min-height: 260px;
            }

            .about-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .features-grid {
                grid-template-columns: 1fr 1fr;
            }

            .about,
            .features {
                padding: 65px 0;
            }
        }

        @media (max-width: 560px) {
            .container {
                width: calc(100% - 32px);
            }

            .navbar {
                min-height: 70px;
            }

            .logo {
                font-size: 14px;
            }

            .nav-links {
                gap: 14px;
            }

            .nav-links a {
                font-size: 11px;
            }

            .nav-links a:nth-child(1),
            .nav-links a:nth-child(2) {
                display: none;
            }

            h1 {
                font-size: 45px;
                letter-spacing: -2.5px;
            }

            .hero-description {
                font-size: 14px;
            }

            .hero-actions {
                gap: 17px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .feature {
                min-height: auto;
                padding: 25px;
            }

            .feature-number {
                margin-bottom: 20px;
            }

            .final-cta {
                padding: 70px 0;
            }

            .footer-inner {
                align-items: flex-start;
                flex-direction: column;
            }
        }
        .mascot-area { border-style: solid; }
        .gubbo-hero { display: block; width: min(100%, 360px); height: auto; object-fit: contain; padding: 20px 16px 0; }
        .mascot-area .mascot-note { margin: 8px 0 30px; color: var(--purple-light); }
    </style>
</head>

<body>

    <!-- CABEÇALHO -->

    <header>
        <div class="container navbar">

            <a href="#inicio" class="logo" aria-label="{{ __('Lobby - início') }}">
                LOB<span>BY</span>
            </a>

            <nav class="nav-links" aria-label="{{ __('Navegação principal') }}">
                <form method="POST" action="{{ route('locale.update') }}">
                    @csrf
                    <input type="hidden" name="locale" value="{{ app()->getLocale() === 'en' ? 'pt_BR' : 'en' }}">
                    <button type="submit" aria-label="{{ app()->getLocale() === 'en' ? 'Mudar para Português (BR)' : 'Switch to English (US)' }}">{{ app()->getLocale() === 'en' ? 'PT-BR' : 'EN-US' }}</button>
                </form>

                <a href="#sobre">{{ __('O Lobby') }}</a>
                <a href="#funcionalidades">{{ __('Funcionalidades') }}</a>
                @auth
                    <a href="{{ route('home') }}" class="nav-button">{{ __('Ir para o Lobby') }}</a>
                @else
                    <a href="{{ route('login') }}">{{ __('Entrar') }}</a>
                    <a href="{{ route('register') }}" class="nav-button">{{ __('Criar conta') }}</a>
                @endauth
            </nav>

        </div>
    </header>


    <main>

        <!-- ABERTURA -->

        <section class="hero" id="inicio">
            <div class="container hero-grid">

                <div>
                    <div class="eyebrow">
                        {{ __('Sua comunidade gamer') }}
                    </div>

                    <h1>
                        {{ __('O mundo dos games é melhor') }}
                        <span>{{ __('quando compartilhado.') }}</span>
                    </h1>

                    <p class="hero-description">
                        {{ __('O Lobby é uma rede social que conecta gamers do mundo todo para compartilhar opiniões, descobrir jogos e construir uma comunidade unida pela paixão por games.') }}
                    </p>

                    <div class="hero-actions">
                        @auth
                            <a href="{{ route('home') }}" class="button-primary">
                                {{ __('Ir para o Lobby') }} <span>↗</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="button-primary">
                                {{ __('Criar conta') }} <span>↗</span>
                            </a>
                        @endauth

                        <a href="#funcionalidades" class="text-link">
                            {{ __('Explorar funcionalidades') }}
                        </a>
                    </div>
                </div>

                <div class="mascot-area" aria-label="Gubbo, mascote do Lobby">
                    <img class="gubbo-hero" src="{{ asset('images/lobi-gamer.png') }}" alt="Gubbo, o mascote roxo do Lobby, segurando um controle" width="1100" height="1000" fetchpriority="high">
                    <p class="mascot-note">Gubbo · Player 2 pronto</p>
                    <span class="mascot-caption">LOBBY.EXE</span>
                </div>

            </div>
        </section>


        <!-- SOBRE O LOBBY -->

        <section class="about" id="sobre">
            <div class="container about-grid">

                <div>
                    <div class="section-label">
                        {{ __('Sobre o projeto') }}
                    </div>

                    <h2>
                        {{ __('Mais que jogar.') }}<br>
                        {{ __('Compartilhar.') }}
                    </h2>
                </div>

                <p class="about-text">
                    {!! __('O <strong>Lobby</strong> nasce com a ideia de aproximar jogadores de diferentes lugares, criando um espaço para trocar opiniões, encontrar pessoas com interesses em comum e descobrir novas experiências no universo dos games.') !!}

                    <br><br>

                    {{ __('Um lugar para compartilhar o que você joga e conhecer quem joga também.') }}
                </p>

            </div>
        </section>


        <!-- FUNCIONALIDADES -->

        <section class="features" id="funcionalidades">
            <div class="container">

                <div class="features-heading">
                    <div class="section-label">
                        {{ __('O que vem por aí') }}
                    </div>

                    <h2>
                        {{ __('Feito para quem joga.') }}
                    </h2>

                    <p>
                        {{ __('Dois espaços para começar a conectar jogadores e suas experiências.') }}
                    </p>
                </div>

                <div class="features-grid">

                    <article class="feature">
                        <div class="feature-number">
                            01 / REVIEWS
                        </div>

                        <h3>{{ __('Reviews de gamers') }}</h3>

                        <p>
                            {{ __('Avalie jogos, compartilhe sua opinião e descubra o que outros jogadores pensam antes de começar sua próxima aventura.') }}
                        </p>
                    </article>

                    <article class="feature">
                        <div class="feature-number">
                            02 / COMUNIDADES
                        </div>

                        <h3>{{ __('Comunidades') }}</h3>

                        <p>
                            {{ __('Encontre jogadores com gostos parecidos, participe de comunidades e converse sobre os jogos que fazem parte do seu mundo.') }}
                        </p>
                    </article>

                </div>

            </div>
        </section>


        <!-- CHAMADA FINAL -->

        <section class="final-cta" id="entrar">
            <div class="container">

                <div class="eyebrow">
                    {{ __('Feito para gamers') }}
                </div>

                <h2>
                    {{ __('Seu jogo. Sua opinião. Sua comunidade.') }}
                </h2>

                <p>
                    {{ __('O Lobby está em desenvolvimento. Estamos trabalhando para criar um novo espaço para a comunidade gamer.') }}
                </p>

                @auth
                    <a href="{{ route('home') }}" class="button-primary">
                        {{ __('Ir para o Lobby') }} <span>↗</span>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="button-primary">
                        {{ __('Criar minha conta') }} <span>↗</span>
                    </a>
                @endauth

            </div>
        </section>

    </main>


    <!-- RODAPÉ -->

    <footer>
        <div class="container footer-inner">

            <a href="#inicio" class="logo" aria-label="{{ __('Voltar ao início') }}">
                LOB<span>BY</span>
            </a>

            <p class="footer-copy">
                {{ __('Um projeto criado para conectar gamers.') }}
            </p>

            <span class="footer-note">
                GG WP
            </span>

        </div>
    </footer>

    @include('partials.gubbo')
</body>
</html>
