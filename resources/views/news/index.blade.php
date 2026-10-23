@extends('layouts.feed')

@section('title', __('Notícias'))

@section('content')
    <header class="page-head"><h1>{{ __('Notícias') }}</h1></header>

    <div class="page-body">
        <p class="lead">{{ __('Resumos escritos pela equipe do Lobby, sempre com link para a matéria original.') }}@if ($updatedAt) {{ __('Atualizado em :date.', ['date' => \App\Support\Fmt::longDate($updatedAt)]) }}@endif</p>

        <nav class="pills" aria-label="{{ __('Filtrar por categoria') }}">
            <a href="{{ route('news.index') }}" @class(['pill', 'is-active' => $category === '']) @if ($category === '') aria-current="true" @endif>{{ __('Todas') }}</a>
            @foreach (\App\Models\NewsItem::CATEGORIES as $key => $label)
                <a href="{{ route('news.index', ['categoria' => $key]) }}" @class(['pill', 'is-active' => $category === $key]) @if ($category === $key) aria-current="true" @endif>{{ __($label) }}</a>
            @endforeach
        </nav>

        @if ($items->isEmpty())
            <p class="empty">{{ __('Nenhuma notícia nesta categoria.') }}</p>
        @else
            <div class="news-list">
                @foreach ($items as $item)
                    <article @class(['news-card', 'is-featured' => $loop->first])>
                        <span class="tag">{{ $item->categoryLabel() }}</span>
                        <h2 class="news-title">{{ __($item->title) }}</h2>
                        @if ($item->summary)
                            <p class="news-summary">{{ __($item->summary) }}</p>
                        @endif
                        <p class="news-source">
                            @if ($loop->first){{ __('Fonte:') }} @endif<strong>{{ $item->source }}</strong> · {{ \App\Support\Fmt::shortDate($item->published_at) }}
                            @if ($item->href())
                                · <a class="news-link" href="{{ $item->href() }}" @if ($item->isExternal()) rel="noopener noreferrer" target="_blank" @endif>{{ __('Leia na fonte') }}</a>
                            @endif
                        </p>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection
