@php($summary = $post->reactionSummary(auth()->id()))
<article class="post" id="post-{{ $post->id }}">
    <x-avatar :user="$post->user" :size="44" />

    <div class="post-main">
        <header class="post-head">
            <div class="post-who">
                <strong class="post-name">{{ $post->user->name }}</strong>
                @if ($post->user->username)
                    <span class="muted">{{ '@'.$post->user->username }}</span>
                @endif
                <span class="muted" aria-hidden="true">·</span>
                <time class="muted" datetime="{{ $post->created_at->toIso8601String() }}" title="{{ \App\Support\Fmt::dateTime($post->created_at) }}">{{ $post->created_at->diffForHumans(['short' => true]) }}</time>
            </div>

            @if ($post->user_id === auth()->id())
                <form method="POST" action="{{ route('posts.destroy', $post) }}" data-confirm="{{ __('Remover esta publicação?') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="icon-btn danger" aria-label="{{ __('Excluir publicação') }}" title="{{ __('Excluir') }}"><x-icon name="trash" /></button>
                </form>
            @endif
        </header>

        @if ($post->contains_spoilers || $post->category->value !== 'general')
            <div class="tags">
                @if ($post->category->value !== 'general')
                    <span class="tag">{{ $post->category->label() }}</span>
                @endif
                @if ($post->contains_spoilers)
                    <span class="tag spoiler">{{ __('Spoiler') }}</span>
                @endif
            </div>
        @endif

        @if ($post->title)
            <h3 class="post-title">{{ $post->title }}</h3>
        @endif

        <div @class(['spoiler-wrap', 'hidden' => $post->contains_spoilers])>
            <p class="post-body">{!! nl2br(e($post->body)) !!}</p>
            @if ($post->contains_spoilers)
                <button type="button" class="spoiler-reveal" data-reveal>{{ __('Mostrar spoiler') }}</button>
            @endif
        </div>

        @if ($post->game)
            <a class="game-chip" href="{{ route('home', ['jogo' => $post->game->id]) }}" title="{{ __('Ver publicações sobre :game', ['game' => $post->game->title]) }}">
                <x-game-cover :game="$post->game" class="cover-sm" />
                <span class="game-chip-text">
                    <strong>{{ $post->game->title }}</strong>
                    <span class="muted">{{ $post->game->year }} · {{ $post->game->genreLabel() }}
                        @if ($post->rating) <x-stars :value="$post->rating" /> @endif
                    </span>
                </span>
            </a>
        @endif

        <footer class="post-foot">
            <form class="reactions" method="POST" action="{{ route('posts.react', $post) }}" data-ajax="reaction">
                @csrf
                @foreach ($reactionTypes as $type)
                    @php($state = $summary[$type->value])
                    <button type="submit" name="type" value="{{ $type->value }}"
                            @class(['reaction', 'is-active' => $state['active']])
                            data-type="{{ $type->value }}"
                            aria-pressed="{{ $state['active'] ? 'true' : 'false' }}"
                            aria-label="{{ $type->label() }}" title="{{ $type->label() }}">
                        <x-icon :name="$type->icon()" /><span class="count" data-count>{{ $state['count'] ?: '' }}</span>
                    </button>
                @endforeach
            </form>

            <form method="POST" action="{{ route('posts.save', $post) }}" data-ajax="save">
                @csrf
                <button type="submit" @class(['save-btn', 'is-active' => $post->saved_by_me]) aria-pressed="{{ $post->saved_by_me ? 'true' : 'false' }}">
                    <x-icon name="bookmark" :filled="$post->saved_by_me" /><span data-save-label>{{ $post->saved_by_me ? __('Salvo') : __('Salvar') }}</span>
                </button>
            </form>
        </footer>
    </div>
</article>
