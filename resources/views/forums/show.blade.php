@extends('layouts.feed')

@section('title', __('Fórum de :game', ['game' => $game->title]))

@section('content')
    <header class="page-head">
        <div>
            <a class="back" href="{{ route('forums.index') }}">{{ __('← Fóruns') }}</a>
            <h1>{{ $game->title }}</h1>
        </div>
        <a class="btn-primary" href="{{ route('threads.create', ['jogo' => $game->slug]) }}">{{ __('Novo tópico') }}</a>
    </header>

    <div class="page-body">
        @if ($threads->isEmpty())
            <p class="empty">{{ __('Ainda não há tópicos neste fórum. Seja a primeira pessoa a') }} <a href="{{ route('threads.create', ['jogo' => $game->slug]) }}">{{ __('abrir um') }}</a>.</p>
        @else
            <ul class="thread-list card-box">
                @foreach ($threads as $thread)
                    <li>
                        <a class="thread-row" href="{{ route('threads.show', $thread) }}">
                            <span class="votes"><strong>{{ $thread->votes }}</strong><small>{{ trans_choice('voto|votos', (int) $thread->votes) }}</small></span>
                            <span class="thread-info">
                                <strong class="thread-title">{{ __($thread->title) }}
                                    @if ($thread->is_solved)<span class="tag solved">{{ __('Resolvido') }}</span>@endif
                                </strong>
                                <span class="muted">{{ $thread->user->name }} · {{ $thread->created_at->diffForHumans() }} · {{ trans_choice(':count resposta|:count respostas', (int) $thread->replies_count) }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($threads->hasPages())
                <nav class="pager" aria-label="{{ __('Paginação') }}">
                    @if ($threads->previousPageUrl())
                        <a href="{{ $threads->previousPageUrl() }}">{{ __('← Anteriores') }}</a>
                    @endif
                    @if ($threads->nextPageUrl())
                        <a href="{{ $threads->nextPageUrl() }}">{{ __('Mais antigos →') }}</a>
                    @endif
                </nav>
            @endif
        @endif
    </div>
@endsection
