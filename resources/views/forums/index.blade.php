@extends('layouts.feed')

@section('title', __('Fóruns'))

@section('content')
    <header class="page-head">
        <h1>{{ __('Fóruns') }}</h1>
        <a class="btn-primary" href="{{ route('threads.create') }}">{{ __('Novo tópico') }}</a>
    </header>

    <div class="page-body">
        <div class="stat-tiles">
            @foreach ([['forums', 'fóruns'], ['threads', 'tópicos'], ['replies', 'respostas'], ['solved', 'resolvidos']] as [$key, $label])
                <div class="stat-tile">
                    <strong>{{ \App\Support\Fmt::num($stats[$key], 0) }}</strong>
                    <span>{{ __($label) }}</span>
                </div>
            @endforeach
        </div>

        <form class="filter-row" method="GET" action="{{ route('forums.index') }}" role="search">
            <input type="search" class="field" name="q" value="{{ $search }}" placeholder="{{ __('Buscar em todos os fóruns') }}" aria-label="{{ __('Buscar em todos os fóruns') }}" maxlength="80" data-autosubmit-delay>
        </form>

        @if ($forums->isEmpty())
            <p class="empty">{{ __('Nenhum fórum encontrado.') }} <a href="{{ route('forums.index') }}">{{ __('Limpar busca') }}</a></p>
        @else
            <div class="games-grid">
                @foreach ($forums as $forum)
                    <a class="game" href="{{ route('forums.show', $forum) }}">
                        <x-game-cover :game="$forum" label />
                        <strong class="game-name">{{ $forum->title }}</strong>
                        <span class="game-sub">{{ trans_choice(':count tópico|:count tópicos', (int) $forum->threads_count) }} · {{ \App\Support\Fmt::num($forum->members_count, 0) }} {{ __('membros') }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
