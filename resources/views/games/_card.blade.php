<a class="game" href="{{ route('games.show', $game) }}" title="{{ $game->title }}">
    <x-game-cover :game="$game" label />
    <strong class="game-name">{{ $game->title }}</strong>
    <span class="game-sub">{{ $game->year }} · {{ $game->genreLabel() }}</span>
    <span class="game-stats">
        @if ($game->rating)
            <span class="game-rating"><x-icon name="star" filled />{{ \App\Support\Fmt::num($game->rating, 1) }}</span>
        @endif
        @if ($game->hours_to_beat)
            <span class="game-hours"><x-icon name="clock" />{{ $game->hours_to_beat }}h</span>
        @endif
    </span>
</a>
