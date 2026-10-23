@extends('layouts.feed')

@section('title', __($thread->title))

@section('content')
    <header class="page-head">
        <a class="back" href="{{ route('forums.show', $thread->game) }}">{{ __('← Fórum de') }} {{ $thread->game->title }}</a>
    </header>

    <div class="page-body">
        @if (session('status'))
            <div class="flash" role="status">{{ session('status') }}</div>
        @endif

        <article class="thread-main card-box">
            <h1 class="thread-h1">{{ __($thread->title) }} @if ($thread->is_solved)<span class="tag solved">{{ __('Resolvido') }}</span>@endif</h1>
            <div class="byline">
                <x-avatar :user="$thread->user" :size="32" />
                <a href="{{ route('profile.show', $thread->user->username) }}"><strong>{{ $thread->user->name }}</strong></a>
                <span class="muted">· {{ $thread->created_at->diffForHumans() }} · {{ trans_choice(':count voto|:count votos', (int) $thread->votes) }}</span>
            </div>
            <div class="prose">{!! nl2br(e($thread->body)) !!}</div>

            @if ($thread->user_id === auth()->id())
                <form method="POST" action="{{ route('threads.solve', $thread) }}">
                    @csrf
                    <button type="submit" class="btn-outline">{{ $thread->is_solved ? __('Reabrir tópico') : __('Marcar como resolvido') }}</button>
                </form>
            @endif
        </article>

        <h2 class="section-title">{{ trans_choice(':count resposta|:count respostas', $thread->replies->count()) }}</h2>

        @foreach ($thread->replies as $reply)
            <div class="reply card-box" id="reply-{{ $reply->id }}">
                <div class="byline">
                    <x-avatar :user="$reply->user" :size="28" />
                    <a href="{{ route('profile.show', $reply->user->username) }}"><strong>{{ $reply->user->name }}</strong></a>
                    <span class="muted">· {{ $reply->created_at->diffForHumans() }}</span>
                </div>
                <div class="prose">{!! nl2br(e($reply->body)) !!}</div>
            </div>
        @endforeach

        <form class="form card-box" method="POST" action="{{ route('threads.reply', $thread) }}">
            @csrf
            <div class="form-row">
                <label for="body">{{ __('Sua resposta') }}</label>
                <textarea id="body" name="body" class="field" rows="4" maxlength="3000" required>{{ old('body') }}</textarea>
                @error('body') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">{{ __('Responder') }}</button>
            </div>
        </form>
    </div>
@endsection
