{{-- Coluna da direita padrão (os dados vêm do View::composer em AppServiceProvider). --}}
<form class="search" method="GET" action="{{ route('search') }}" role="search">
    <x-icon name="search" />
    <input type="search" name="q" placeholder="{{ __('Buscar em tudo (aperte /)') }}" aria-label="{{ __('Buscar em tudo') }}" maxlength="80" data-search>
</form>

<section class="card" aria-labelledby="trend-title">
    <h2 id="trend-title">{{ __('Em alta nos fóruns') }}</h2>
    @forelse ($railTrending as $thread)
        <a class="trend" href="{{ route('threads.show', $thread) }}">
            <span class="muted">{{ $thread->game->title }}</span>
            <strong>{{ \Illuminate\Support\Str::limit(__($thread->title), 80) }}</strong>
            <span class="muted">{{ trans_choice(':count voto|:count votos', (int) $thread->votes) }}</span>
        </a>
    @empty
        <p class="card-empty">{{ __('Os tópicos mais votados dos fóruns aparecem aqui.') }}</p>
    @endforelse
</section>

<section class="card" aria-labelledby="people-title">
    <h2 id="people-title">{{ __('Pessoas para seguir') }}</h2>
    @forelse ($railPeople as $person)
        <div class="person">
            <x-avatar :user="$person" :size="40" />
            <div class="person-text">
                <a href="{{ route('profile.show', $person->username) }}"><strong>{{ $person->name }}</strong></a>
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
    @forelse ($railNews as $item)
        <div class="news">
            <span class="muted">{{ $item->source }} · {{ \App\Support\Fmt::shortDate($item->published_at) }}</span>
            <a href="{{ $item->href() ?: route('news.index') }}" @if ($item->isExternal()) rel="noopener noreferrer" target="_blank" @endif><strong>{{ __($item->title) }}</strong></a>
        </div>
    @empty
        <p class="card-empty">{{ __('Sem notícias por enquanto.') }}</p>
    @endforelse
</section>
