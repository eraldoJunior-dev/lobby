@extends('layouts.feed')

@section('title', __('Guias'))

@section('content')
    <header class="page-head"><h1>{{ __('Guias') }}</h1></header>

    <div class="page-body">
        <p class="lead">{{ __('Roteiros passo a passo para troféus de platina, 100% de conclusão e caça a conquistas, com um checklist que salva o seu progresso.') }}</p>

        @php
            $query = fn (array $extra = []) => array_filter(array_merge(['q' => $search, 'tipo' => $type], $extra), fn ($v) => filled($v));
        @endphp

        <nav class="pills" aria-label="{{ __('Filtrar por tipo') }}">
            <a href="{{ route('guides.index', $query(['tipo' => null])) }}" @class(['pill', 'is-active' => $type === '']) @if ($type === '') aria-current="true" @endif>{{ __('Todos') }}</a>
            @foreach (\App\Models\Guide::TYPES as $key => $label)
                <a href="{{ route('guides.index', $query(['tipo' => $key])) }}" @class(['pill', 'is-active' => $type === (string) $key]) @if ($type === (string) $key) aria-current="true" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <form class="filter-row" method="GET" action="{{ route('guides.index') }}" role="search">
            @if ($type !== '') <input type="hidden" name="tipo" value="{{ $type }}"> @endif
            <input type="search" class="field" name="q" value="{{ $search }}" placeholder="{{ __('Buscar guias') }}" aria-label="{{ __('Buscar guias') }}" maxlength="80" data-autosubmit-delay>
        </form>

        @if ($guides->isEmpty())
            <p class="empty">{{ __('Nenhum guia encontrado.') }}</p>
        @else
            <ul class="guide-list card-box">
                @foreach ($guides as $guide)
                    <li>
                        <a class="guide-row" href="{{ route('guides.show', $guide) }}">
                            <x-game-cover :game="$guide->game" class="guide-cover" />
                            <span class="guide-info">
                                <span class="tag">{{ $guide->typeLabel() }}</span>
                                <strong class="guide-title">{{ __($guide->title) }}</strong>
                                <span class="guide-meta">
                                    <span class="diff" role="img" aria-label="{{ __('Dificuldade :n de 5', ['n' => $guide->difficulty]) }}">
                                        @for ($i = 1; $i <= 5; $i++)<span @class(['diff-dot', 'is-on' => $i <= $guide->difficulty])></span>@endfor
                                    </span>
                                    @if ($guide->hours_label)
                                        <span class="meta-item"><x-icon name="clock" />{{ __($guide->hours_label) }}</span>
                                    @endif
                                    <span class="meta-item"><x-icon name="check" />{{ $guide->done_count }}/{{ $guide->items_count }}</span>
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
