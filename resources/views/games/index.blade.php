@extends('layouts.feed')

@section('title', __('Jogos'))

@section('content')
    <header class="page-head"><h1>{{ __('Jogos') }}</h1></header>

    <div class="page-body">
        @php
            $query = fn (array $extra = []) => array_filter(
                array_merge(['q' => $search, 'ordem' => $sort !== 'populares' ? $sort : null, 'genero' => $genre], $extra),
                fn ($v) => filled($v)
            );
        @endphp

        <nav class="pills" aria-label="{{ __('Filtrar por gênero') }}">
            <a href="{{ route('games.index', $query(['genero' => null])) }}" @class(['pill', 'is-active' => $genre === '']) @if ($genre === '') aria-current="true" @endif>{{ __('Todos') }}</a>
            @foreach ($genres as $g)
                <a href="{{ route('games.index', $query(['genero' => $g])) }}" @class(['pill', 'is-active' => $genre === $g]) @if ($genre === $g) aria-current="true" @endif>{{ __($g) }}</a>
            @endforeach
        </nav>

        <form class="filter-row" method="GET" action="{{ route('games.index') }}" role="search">
            @if ($genre !== '') <input type="hidden" name="genero" value="{{ $genre }}"> @endif
            <input type="search" class="field" name="q" value="{{ $search }}" placeholder="{{ __('Filtrar por nome') }}" aria-label="{{ __('Filtrar por nome') }}" maxlength="80" data-autosubmit-delay>
            <select name="ordem" class="field" aria-label="{{ __('Ordenar por') }}" data-autosubmit>
                <option value="populares" @selected($sort === 'populares')>{{ __('Mais populares') }}</option>
                <option value="nota" @selected($sort === 'nota')>{{ __('Melhor avaliados') }}</option>
                <option value="recentes" @selected($sort === 'recentes')>{{ __('Mais recentes') }}</option>
                <option value="az" @selected($sort === 'az')>A–Z</option>
            </select>
            <noscript><button class="btn-outline" type="submit">{{ __('Filtrar') }}</button></noscript>
        </form>

        @if ($games->isEmpty())
            <p class="empty">{{ __('Nenhum jogo encontrado.') }} <a href="{{ route('games.index') }}">{{ __('Limpar filtros') }}</a></p>
        @else
            <div class="games-grid">
                @foreach ($games as $game)
                    @include('games._card', ['game' => $game])
                @endforeach
            </div>
        @endif
    </div>
@endsection
