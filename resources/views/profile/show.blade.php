@extends('layouts.feed')

@section('title', $isMe ? __('Perfil') : $profile->name)

@section('content')
    @php($hue = ($profile->id * 67) % 360)

    <div class="page-body profile">
        @if (session('status'))
            <div class="flash" role="status">{{ session('status') }}</div>
        @endif

        <section class="profile-card card-box">
            <div class="profile-banner" style="--hue: {{ $hue }}"></div>
            <div class="profile-main">
                <x-avatar :user="$profile" :size="92" class="profile-avatar" />

                <div class="profile-id">
                    <h1>{{ $profile->name }} <span class="level" title="{{ __('Nível :n', ['n' => $level]) }}">LV {{ $level }}</span></h1>
                    <span class="muted">{{ '@'.$profile->username }} @foreach (\App\Support\Platforms::selected($profile) as $platform)<span class="lobby-profile-platform" title="{{ \App\Support\Platforms::OPTIONS[$platform][0] }}"><img src="{{ asset('assets/platforms/'.$platform.'.svg') }}" alt="" class="lobby-platform-logo lobby-platform-logo--inline"></span>@endforeach @if ($profile->pronouns) · {{ $profile->pronouns }}@endif</span>
                </div>

                @if ($isPublic)
                    @if ($profile->status)<p class="profile-status">{{ $profile->status }}</p>@endif
                    @if ($profile->bio)<p class="profile-bio">{{ $profile->bio }}</p>@endif

                    @if ($profile->location || $profile->website || $profile->favorite_genre)
                        <p class="profile-extra muted">
                            @if ($profile->location)<span>{{ $profile->location }}</span>@endif
                            @if ($profile->favorite_genre)<span>{{ __('Gênero favorito:') }} {{ $profile->favorite_genre }}</span>@endif
                            @if ($profile->website)<a href="{{ $profile->website }}" rel="noopener noreferrer nofollow" target="_blank">{{ preg_replace('#^https?://#', '', rtrim($profile->website, '/')) }}</a>@endif
                        </p>
                    @endif

                    <dl class="profile-numbers">
                        <div><dt>{{ __('avaliados') }}</dt><dd>{{ $ratedCount }}</dd></div>
                        <div><dt>{{ __('nota média') }}</dt><dd>{{ $avgScore !== null ? \App\Support\Fmt::num($avgScore, 1) : '—' }}</dd></div>
                        <div><dt>{{ __('tópicos') }}</dt><dd>{{ $threadsCount }}</dd></div>
                        <div><dt>{{ __('reputação') }}</dt><dd>{{ $reputation }}</dd></div>
                        <div><dt>{{ __('seguidores') }}</dt><dd>{{ $followersCount }}</dd></div>
                        <div><dt>{{ __('seguindo') }}</dt><dd>{{ $followingCount }}</dd></div>
                    </dl>
                @endif

                <div class="profile-actions">
                    @if ($isMe)
                        <a class="btn-outline" href="{{ route('settings.edit', ['aba' => 'perfil']) }}"><x-icon name="edit" /> {{ __('Editar perfil') }}</a>
                    @else
                        <form method="POST" action="{{ route('users.follow', $profile) }}" data-ajax="follow">
                            @csrf
                            <button type="submit" class="btn-outline @if ($isFollowing) is-active @endif" data-follow-btn>{{ $isFollowing ? __('Seguindo') : __('Seguir') }}</button>
                        </form>
                    @endif
                </div>
            </div>
        </section>

        @if (! $isPublic)
            <p class="empty">{{ __('Este perfil é privado.') }}</p>
        @elseif (! $showLibrary)
            <p class="empty">{{ $profile->name }} {{ __('não compartilha a biblioteca de jogos.') }}</p>
        @else
            <h2 class="section-title">{{ $isMe ? __('Suas estatísticas') : __('Estatísticas') }}</h2>
            <div class="stat-tiles">
                @foreach ([[$finished, 'zerados'], [$hours, 'horas jogadas'], [$backlog, 'backlog'], [$reviews, 'resenhas']] as [$value, $label])
                    <div class="stat-tile"><strong>{{ $value }}</strong><span>{{ __($label) }}</span></div>
                @endforeach
            </div>

            <section class="card-box genres" aria-labelledby="genres-title">
                <h2 id="genres-title" class="box-title">{{ __('Gêneros favoritos') }}</h2>
                @forelse ($topGenres as $genre => $count)
                    <div class="genre-row">
                        <span>{{ __($genre) }}</span>
                        <span class="genre-bar"><span style="width: {{ (int) round($count / $topGenres->max() * 100) }}%"></span></span>
                        <span class="genre-count">{{ $count }}</span>
                    </div>
                @empty
                    <p class="card-empty">{{ __('Os gêneros mais jogados aparecem aqui.') }}</p>
                @endforelse
            </section>

            <h2 class="section-title">{{ __('Favoritos') }}</h2>
            @if ($favorites->isEmpty())
                <p class="empty">{{ __('Nenhum jogo favorito ainda.') }}</p>
            @else
                <div class="games-grid">
                    @foreach ($favorites as $game)
                        <a class="game" href="{{ route('games.show', $game) }}">
                            <x-game-cover :game="$game" label />
                            <strong class="game-name">{{ $game->title }}</strong>
                            <span class="game-sub">{{ $game->year }} · {{ $game->genreLabel() }}</span>
                            <span class="game-stats">
                                @if ($game->pivot->rating)
                                    <span class="game-rating"><x-icon name="star" filled />{{ \App\Support\Fmt::num($game->pivot->rating, 1) }}</span>
                                @endif
                                @if ($game->pivot->hours_logged)
                                    <span class="game-hours"><x-icon name="clock" />{{ $game->pivot->hours_logged }}h</span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
@endsection
