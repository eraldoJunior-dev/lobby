@extends('layouts.feed')

@section('title', __('Busca'))

@section('content')
    <header class="page-head"><h1>{{ __('Busca') }}</h1></header>

    <div class="page-body">
        <form class="filter-row" method="GET" action="{{ route('search') }}" role="search">
            <input type="search" class="field" name="q" value="{{ $q }}" placeholder="{{ __('Buscar em tudo') }}" aria-label="{{ __('Buscar em tudo') }}" maxlength="80" autofocus>
        </form>

        @if (! $enough)
            <p class="empty">{{ __('Digite pelo menos 2 letras para buscar em jogos, guias, tópicos, notícias e pessoas.') }}</p>
        @elseif ($games->isEmpty() && $guides->isEmpty() && $threads->isEmpty() && $news->isEmpty() && $people->isEmpty())
            <p class="empty">{{ __('Nada encontrado para “:q”.', ['q' => $q]) }}</p>
        @else
            @if ($games->isNotEmpty())
                <h2 class="section-title">{{ __('Jogos') }}</h2>
                <ul class="result-list card-box">
                    @foreach ($games as $game)
                        <li><a href="{{ route('games.show', $game) }}"><strong>{{ $game->title }}</strong> <span class="muted">{{ $game->year }} · {{ $game->genreLabel() }}</span></a></li>
                    @endforeach
                </ul>
            @endif

            @if ($guides->isNotEmpty())
                <h2 class="section-title">{{ __('Guias') }}</h2>
                <ul class="result-list card-box">
                    @foreach ($guides as $guide)
                        <li><a href="{{ route('guides.show', $guide) }}"><strong>{{ __($guide->title) }}</strong> <span class="muted">{{ $guide->typeLabel() }}</span></a></li>
                    @endforeach
                </ul>
            @endif

            @if ($threads->isNotEmpty())
                <h2 class="section-title">{{ __('Tópicos') }}</h2>
                <ul class="result-list card-box">
                    @foreach ($threads as $thread)
                        <li><a href="{{ route('threads.show', $thread) }}"><strong>{{ __($thread->title) }}</strong> <span class="muted">{{ $thread->game->title }}</span></a></li>
                    @endforeach
                </ul>
            @endif

            @if ($news->isNotEmpty())
                <h2 class="section-title">{{ __('Notícias') }}</h2>
                <ul class="result-list card-box">
                    @foreach ($news as $item)
                        <li><a href="{{ route('news.index', ['categoria' => $item->category]) }}"><strong>{{ __($item->title) }}</strong> <span class="muted">{{ $item->source }}</span></a></li>
                    @endforeach
                </ul>
            @endif

            @if ($people->isNotEmpty())
                <h2 class="section-title">{{ __('Pessoas') }}</h2>
                <ul class="result-list card-box">
                    @foreach ($people as $person)
                        <li><a href="{{ route('profile.show', $person->username) }}"><strong>{{ $person->name }}</strong> <span class="muted">{{ '@'.$person->username }}</span></a></li>
                    @endforeach
                </ul>
            @endif
        @endif
    </div>
@endsection
