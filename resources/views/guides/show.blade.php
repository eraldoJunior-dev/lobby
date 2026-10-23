@extends('layouts.feed')

@section('title', __($guide->title))

@section('content')
    @php
        $total = $items->count();
        $done = count($doneIds);
        $percent = $total > 0 ? (int) round($done / $total * 100) : 0;
    @endphp

    <header class="page-head">
        <a class="back" href="{{ route('guides.index') }}">{{ __('← Guias') }}</a>
    </header>

    <div class="page-body">
        <div class="guide-hero">
            <x-game-cover :game="$guide->game" class="guide-cover" />
            <div class="guide-info">
                <span class="tag">{{ $guide->typeLabel() }}</span>
                <h1 class="guide-h1">{{ __($guide->title) }}</h1>
                <span class="guide-meta">
                    <span class="meta-item">{{ $guide->game->title }}</span>
                    <span class="diff" role="img" aria-label="{{ __('Dificuldade :n de 5', ['n' => $guide->difficulty]) }}">
                        @for ($i = 1; $i <= 5; $i++)<span @class(['diff-dot', 'is-on' => $i <= $guide->difficulty])></span>@endfor
                    </span>
                    @if ($guide->hours_label)
                        <span class="meta-item"><x-icon name="clock" />{{ __($guide->hours_label) }}</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $total }}" aria-valuenow="{{ $done }}" aria-label="{{ __('Progresso do guia') }}">
            <div class="progress-bar"><span style="width: {{ $percent }}%"></span></div>
            <span class="progress-text">{{ $done }} {{ __('de') }} {{ $total }} {{ __('concluídos ·') }} {{ $percent }}%</span>
        </div>

        <ul class="checklist card-box">
            @foreach ($items as $item)
                @php($isDone = in_array($item->id, $doneIds, true))
                <li id="item-{{ $item->id }}">
                    <form method="POST" action="{{ route('guides.toggle', $item) }}">
                        @csrf
                        <button type="submit" @class(['check-row', 'is-done' => $isDone]) aria-pressed="{{ $isDone ? 'true' : 'false' }}">
                            <span class="check-box"><x-icon name="check" /></span>
                            <span class="check-text">{{ __($item->title) }}</span>
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
