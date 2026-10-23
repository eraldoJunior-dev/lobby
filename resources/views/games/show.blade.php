@extends('layouts.feed')

@section('title', $game->title)

@section('content')
    @php
        // Metadados verificados da Steam ou RAWG, com atribuição da fonte.
        // Não usar notas/horas ilustrativas do seeder como dados editoriais.
        $verified = $steamFacts;
        $status = $mine?->status;
        $isFav = (bool) ($mine?->is_favorite ?? false);
        $myRating = $mine?->rating !== null ? (float) $mine->rating : 0;
        $histMax = max(1, $histogram->max());
        $statusButtons = [
            'zerado' => __('Joguei'),
            'jogando' => __('Jogando'),
            'backlog' => __('Quero jogar'),
        ];
    @endphp

    <header class="page-head">
        <a class="back" href="{{ route('games.index') }}">← {{ __('Voltar aos jogos') }}</a>
    </header>

    <div class="page-body game-page game-detail-page">
        @if (session('status'))
            <div class="flash" role="status">{{ session('status') }}</div>
        @endif

        {{-- TOPO --}}
        <section class="game-hero game-detail-hero">
            <div class="game-detail-glow" aria-hidden="true"></div>
            <x-game-cover :game="$game" label class="game-hero-cover" />

            <div class="game-hero-info">
                <h1 class="game-h1">{{ $game->title }}</h1>
                <p class="muted game-meta">{{ $game->year ?: __('Ano não informado') }}@if ($game->genre) · {{ $game->genreLabel() }}@endif @if ($verified) · {{ ($verified['developer'] ?: __('Desenvolvedor não informado')) }} · {{ ($verified['publisher'] ?: __('Editora não informada')) }}@endif</p>@if ($verified)<p class="muted">{{ $verified['platforms'] }}</p>@endif
                <p class="game-detail-intro">{{ $verified['description'] ?? __('Explore as avaliações da comunidade, acompanhe seu progresso e descubra outros jogos parecidos.') }}</p>

                <div class="game-score">
                    @if ($memberAvg !== null)
                        <div class="score-main"><strong class="score-num">{{ \App\Support\Fmt::num($memberAvg, 1) }}</strong><span class="muted score-count">{{ $ratedCount }} {{ __('avaliações reais dos membros do Lobby') }}</span></div>
                    @else
                        <div class="score-main"><strong class="score-num">—</strong><span class="muted score-count">{{ __('Sem avaliações dos membros do Lobby') }}</span></div>
                    @endif
                    @if ($ratedCount > 0)
                        <div class="histogram" role="img" aria-label="{{ __('Distribuição das notas') }}">
                            @foreach ($histogram as $stars => $count)
                                <span class="hist-col" title="{{ $stars }} ★ · {{ $count }}"><span class="hist-bar" style="height: {{ max(8, (int) round($count / $histMax * 100)) }}%"></span></span>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Ações --}}
                <div class="game-actions">
                    @foreach ($statusButtons as $key => $label)
                        <form method="POST" action="{{ route('games.status', $game) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ $key }}">
                            <button type="submit" @class(['btn-outline', 'is-selected' => $status === $key]) aria-pressed="{{ $status === $key ? 'true' : 'false' }}">{{ $label }}</button>
                        </form>
                    @endforeach

                    <form method="POST" action="{{ route('games.favorite', $game) }}">
                        @csrf
                        <button type="submit" @class(['btn-outline', 'is-selected' => $isFav]) aria-pressed="{{ $isFav ? 'true' : 'false' }}">
                            <x-icon name="heart" :filled="$isFav" /> {{ __('Favorito') }}
                        </button>
                    </form>

                    <a class="btn-outline" href="{{ route('forums.show', $game) }}"><x-icon name="forum" /> {{ __('Fórum') }}@if ($threadsCount) <span class="muted">({{ $threadsCount }})</span>@endif</a>
                    <a class="btn-outline" href="{{ route('home', ['jogo' => $game->id]) }}"><x-icon name="news" /> {{ __('Publicações') }}@if ($postsCount) <span class="muted">({{ $postsCount }})</span>@endif</a>
                </div>
            </div>
        </section>

        {{-- Informações editoriais: apenas valores com fonte verificável. --}}
        <h2 class="section-title">{{ __('Tempo para zerar') }}</h2>
        <section class="card-box time-box">
            @if ($completionTimes)
                <div class="game-detail-columns">
                    <div><p class="muted">{{ __('História principal') }}</p><strong>{{ $completionTimes['main_hours'] !== null ? number_format($completionTimes['main_hours'], 1, ',', '.') . ' h' : '—' }}</strong></div>
                    <div><p class="muted">{{ __('Completar 100%') }}</p><strong>{{ $completionTimes['completionist_hours'] !== null ? number_format($completionTimes['completionist_hours'], 1, ',', '.') . ' h' : '—' }}</strong></div>
                </div>
                <a href="{{ $completionTimes['source_url'] }}" target="_blank" rel="noopener noreferrer">{{ __('Fonte dos tempos') }}</a>
            @else
                <p class="muted">{{ __('Ainda não há tempos verificados para este jogo.') }}</p>
                <a href="https://howlongtobeat.com/?q={{ urlencode($game->title) }}" target="_blank" rel="noopener noreferrer">{{ __('Consultar HowLongToBeat') }}</a>
            @endif
        </section>
        <div class="game-detail-columns">
            <section class="game-detail-panel card-box">
                <h2>{{ __('Onde jogar') }}</h2>
                @if ($verified)
                    <p>{{ $verified['platforms'] ?: __('Plataformas não informadas na Steam') }}</p>
                    @if ($verified['price'])<p><strong>{{ $verified['price'] }}</strong> <span class="muted">{{ $verified['source'] === 'Steam Store' ? __('(Steam Brasil; sujeito a mudanças)') : '' }}</span></p>@endif
                    @if (!empty($verified['stores']))
                        <div class="game-store-list">
                            @foreach ($verified['stores'] as $store)
                                <p><a href="{{ $store['url'] }}" target="_blank" rel="noopener noreferrer">{{ $store['name'] }}</a> — <strong>{{ $store['price'] ?? __('Preço não disponível') }}</strong></p>
                            @endforeach
                        </div>
                    @endif
                    <p class="muted">{{ __('Preços variam por país e promoção. Consulte o valor atualizado na loja.') }}</p>
                    <a class="btn-outline" href="{{ $verified['store_url'] }}" target="_blank" rel="noopener noreferrer">{{ $verified['source'] === 'Steam Store' ? __('Ver na Steam') : __('Ver na RAWG') }}</a>
                    <p class="muted small">{{ __('Fonte:') }} {{ $verified['source'] }}</p>
                @else
                    <p class="muted">{{ __('Dados da loja indisponíveis ou jogo sem identificação Steam. Nenhum preço inventado é exibido.') }}</p>
                @endif
            </section>
            <section class="game-detail-panel card-box">
                <h2>{{ __('Conquistas') }}</h2>
                @if ($verified)
                    @if ($verified['achievements'] !== null)<strong>{{ $verified['achievements'] }} {{ $verified['source'] === 'Steam Store' ? __('conquistas na Steam') : __('conquistas registradas na RAWG') }}</strong>@else<p class="muted">{{ __('Quantidade de conquistas não informada pela fonte.') }}</p>@endif
                    <p><a href="{{ $verified['achievements_url'] }}" target="_blank" rel="noopener noreferrer">{{ __('Ver conquistas e fonte') }}</a></p>
                @else
                    <p class="muted">{{ __('Quantidade de conquistas não verificada.') }}</p>
                @endif
            </section>
        </div>

        {{-- GUIAS --}}
        @if ($guides->isNotEmpty())
            <h2 class="section-title">{{ __('Guias deste jogo') }}</h2>
            <ul class="result-list card-box">
                @foreach ($guides as $guide)
                    <li><a href="{{ route('guides.show', $guide) }}"><strong>{{ __($guide->title) }}</strong> <span class="muted">{{ __($guide->typeLabel()) }}</span></a></li>
                @endforeach
            </ul>
        @endif

        {{-- SUA BIBLIOTECA --}}
        <h2 class="section-title">{{ __('Seu tempo de jogo e biblioteca') }}</h2>
        <section class="card-box lib-box">
            @error('hours') <p class="error lib-error">{{ $message }}</p> @enderror
            <form class="lib-row" method="POST" action="{{ route('games.hours', $game) }}">
                @csrf
                <div class="lib-text">
                    <strong>{{ __('Registrar horas jogadas') }}</strong>
                    <small class="muted">{{ __('Acompanhe seu tempo para ver os totais no seu perfil.') }}</small>
                </div>
                <input type="number" name="hours" class="field lib-input" min="1" max="5000" inputmode="numeric" placeholder="0" aria-label="{{ __('Horas') }}" required>
                <button type="submit" class="btn-outline">{{ __('Registrar') }}</button>
            </form>
        </section>

        <section class="game-detail-panel card-box game-detail-list-note">
            <div><h2>{{ __('Adicionar a uma lista') }}</h2><p class="muted">{{ __('As listas personalizadas ainda não estão disponíveis nesta versão. Você pode usar Quero jogar para guardar este jogo na sua biblioteca.') }}</p></div>
        </section>

        {{-- SIMILARES --}}
        @if ($similar->isNotEmpty())
            <h2 class="section-title">{{ __('Jogos parecidos') }}</h2>
            <div class="games-grid similar-grid">
                @foreach ($similar as $other)
                    @include('games._card', ['game' => $other])
                @endforeach
            </div>
        @endif

        {{-- SUA AVALIAÇÃO --}}
        <h2 class="section-title" id="avaliacoes">{{ __('Sua avaliação') }}</h2>
        <form class="card-box form review-form" method="POST" action="{{ route('games.review', $game) }}">
            @csrf
            <x-rating-picker id="game-rating" :value="old('rating', $myRating)" />
            @error('rating') <p class="error">{{ $message }}</p> @enderror

            <div class="form-row">
                <label class="sr-only" for="review">{{ __('Resenha') }}</label>
                <textarea id="review" name="review" class="field" rows="4" maxlength="2000" placeholder="{{ __('O que você achou? (opcional)') }}">{{ old('review', $mine?->review) }}</textarea>
                @error('review') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">{{ __('Publicar avaliação') }}</button>
            </div>
        </form>

        {{-- AVALIAÇÕES DA COMUNIDADE --}}
        <h2 class="section-title">{{ __('Avaliações da comunidade') }}</h2>
        @if ($reviews->isEmpty())
            <p class="card-box empty-box">{{ __('Ninguém avaliou ainda. Seja a primeira pessoa!') }}</p>
        @else
            <div class="reviews">
                @foreach ($reviews as $member)
                    <article class="card-box review">
                        <div class="byline">
                            <x-avatar :user="$member" :size="32" />
                            <a href="{{ route('profile.show', $member->username) }}"><strong>{{ $member->name }}</strong></a>
                            @if ($member->pivot->rating)<x-stars :value="$member->pivot->rating" />@endif
                        </div>
                        <p class="prose">{!! nl2br(e($member->pivot->review)) !!}</p>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection
