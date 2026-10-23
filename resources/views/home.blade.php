@extends('layouts.feed')

@section('title', __('Início'))

@section('content')
    {{-- ABAS --}}
    <div class="tabs">
        <a href="{{ route('home') }}" @class(['tab', 'is-active' => $tab === 'para-voce']) @if ($tab === 'para-voce') aria-current="page" @endif>{{ __('Para você') }}</a>
        <a href="{{ route('home', ['aba' => 'seguindo']) }}" @class(['tab', 'is-active' => $tab === 'seguindo']) @if ($tab === 'seguindo') aria-current="page" @endif>{{ __('Seguindo') }}</a>
    </div>

    {{-- Busca compacta (a da coluna da direita some em telas menores) --}}
    <form class="search search-mini" method="GET" action="{{ route('home') }}" role="search">
        <x-icon name="search" />
        <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('Buscar publicações') }}" aria-label="{{ __('Buscar publicações') }}" maxlength="80">
        @if ($tab === 'seguindo') <input type="hidden" name="aba" value="seguindo"> @endif
    </form>

    @if (session('status'))
        <div class="flash" role="status">{{ session('status') }}</div>
    @endif

    {{-- FILTROS ATIVOS --}}
    @if ($activeGame || $search !== '')
        <div class="filters">
            <span class="muted">{{ __('Mostrando:') }}</span>
            @if ($activeGame)
                <span class="chip">{{ $activeGame->title }}</span>
            @endif
            @if ($search !== '')
                <span class="chip">“{{ $search }}”</span>
            @endif
            <a class="filters-clear" href="{{ $tab === 'seguindo' ? route('home', ['aba' => 'seguindo']) : route('home') }}">
                <x-icon name="x" /> {{ __('Limpar') }}
            </a>
        </div>
    @endif

    {{-- RECOMENDADOS --}}
    @if ($recommended->isNotEmpty())
        <section class="recs" aria-labelledby="recs-title">
            <h2 id="recs-title" class="section-title">{{ __('Recomendados para você') }}</h2>
            <div class="games">
                @foreach ($recommended as $game)
                    @include('games._card', ['game' => $game])
                @endforeach
            </div>
        </section>
    @endif

    {{-- NOVA PUBLICAÇÃO --}}
    <form id="composer" class="composer" method="POST" action="{{ route('posts.store') }}" novalidate>
        @csrf
        <x-avatar :user="auth()->user()" :size="44" />

        <div class="composer-main">
            <label for="body" class="sr-only">{{ __('O que você está jogando?') }}</label>
            <textarea id="body" name="body" rows="2" maxlength="2000" data-autogrow data-counter="#body-count"
                      placeholder="{{ __('O que você está jogando?') }}"
                      @class(['invalid' => $errors->has('body')])>{{ old('body') }}</textarea>
            @error('body') <p class="error">{{ $message }}</p> @enderror

            <div class="composer-row">
                <div class="composer-fields">
                    <label for="game_id" class="sr-only">{{ __('Jogo') }}</label>
                    <select id="game_id" name="game_id" @class(['invalid' => $errors->has('game_id')])>
                        <option value="">{{ __('Escolha um jogo (opcional)') }}</option>
                        @foreach ($games as $game)
                            <option value="{{ $game->id }}" @selected((string) old('game_id') === (string) $game->id)>{{ $game->title }}</option>
                        @endforeach
                    </select>

                    <x-rating-picker id="post-rating" :value="old('rating')" />
                </div>
            </div>
            @error('game_id') <p class="error">{{ $message }}</p> @enderror
            @error('rating') <p class="error">{{ $message }}</p> @enderror

            <div class="composer-foot">
                <label class="check">
                    <input type="checkbox" name="contains_spoilers" value="1" @checked(old('contains_spoilers'))>
                    {{ __('Contém spoilers') }}
                </label>
                <span class="counter" id="body-count" aria-hidden="true"></span>
                <button type="submit" class="btn-primary">{{ __('Publicar') }}</button>
            </div>
        </div>
    </form>

    {{-- FEED --}}
    <div class="feed">
        @forelse ($posts as $post)
            @include('feed.post', ['post' => $post])
        @empty
            <div class="empty">
                @if ($activeGame || $search !== '')
                    {{ __('Nenhuma publicação encontrada. Tente outro termo ou limpe os filtros.') }}
                @elseif ($tab === 'seguindo')
                    {{ __('Você ainda não segue ninguém com publicações. Siga pessoas na coluna ao lado para vê-las aqui.') }}
                @else
                    {{ __('Ainda não há publicações. Seja o primeiro a publicar!') }}
                @endif
            </div>
        @endforelse
    </div>

    @if ($posts->hasPages())
        <nav class="pager" aria-label="{{ __('Paginação') }}">
            @if ($posts->previousPageUrl())
                <a href="{{ $posts->previousPageUrl() }}">{{ __('← Mais recentes') }}</a>
            @else
                <span></span>
            @endif
            @if ($posts->nextPageUrl())
                <a href="{{ $posts->nextPageUrl() }}">{{ __('Mais antigas →') }}</a>
            @endif
        </nav>
    @endif
@endsection

@section('rail')
    <form class="search" method="GET" action="{{ route('home') }}" role="search">
        <x-icon name="search" />
        <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('Buscar publicações (aperte /)') }}" aria-label="{{ __('Buscar publicações') }}" maxlength="80" data-search>
        @if ($tab === 'seguindo') <input type="hidden" name="aba" value="seguindo"> @endif
    </form>

    <section class="card" aria-labelledby="trend-title">
        <h2 id="trend-title">{{ __('Em alta na comunidade') }}</h2>
        @forelse ($trending as $item)
            <div class="trend">
                <span class="muted">{{ $item->game?->title ?? __('Comunidade') }}</span>
                <strong>{{ \Illuminate\Support\Str::limit($item->title ?: $item->body, 80) }}</strong>
                <span class="muted">{{ trans_choice(':count reação|:count reações', $item->reactions_count) }}</span>
            </div>
        @empty
            <p class="card-empty">{{ __('As publicações mais reagidas da semana aparecem aqui.') }}</p>
        @endforelse
    </section>

    <section class="card" aria-labelledby="people-title">
        <h2 id="people-title">{{ __('Pessoas para seguir') }}</h2>
        @forelse ($suggestions as $person)
            <div class="person">
                <x-avatar :user="$person" :size="40" />
                <div class="person-text">
                    <strong>{{ $person->name }}</strong>
                    @if ($person->username)
                        <span class="muted">{{ '@'.$person->username }}</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('users.follow', $person) }}" data-ajax="follow">
                    @csrf
                    <button type="submit" class="btn-outline" data-follow-btn>{{ __('Seguir') }}</button>
                </form>
            </div>
        @empty
            <p class="card-empty">{{ __('Você já segue todo mundo por aqui.') }}</p>
        @endforelse
    </section>

    <section class="card" aria-labelledby="news-title">
        <h2 id="news-title">{{ __('Notícias') }}</h2>
        @forelse ($news as $item)
            <div class="news">
                <span class="muted">{{ $item->source }} · {{ \App\Support\Fmt::shortDate($item->published_at) }}</span>
                @if ($item->href())
                    <a href="{{ $item->href() }}" @if ($item->isExternal()) rel="noopener noreferrer" target="_blank" @endif><strong>{{ __($item->title) }}</strong></a>
                @else
                    <strong>{{ __($item->title) }}</strong>
                @endif
            </div>
        @empty
            <p class="card-empty">{{ __('Sem notícias por enquanto.') }}</p>
        @endforelse
    </section>
@endsection
