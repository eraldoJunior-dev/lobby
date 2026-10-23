@extends('layouts.feed')

@section('title', __('Novo tópico'))

@section('content')
    <header class="page-head">
        <div>
            <a class="back" href="{{ route('forums.index') }}">{{ __('← Fóruns') }}</a>
            <h1>{{ __('Novo tópico') }}</h1>
        </div>
    </header>

    <div class="page-body">
        <form class="form card-box" method="POST" action="{{ route('threads.store') }}">
            @csrf

            <div class="form-row">
                <label for="game_id">{{ __('Fórum') }}</label>
                <select id="game_id" name="game_id" class="field" required>
                    <option value="">{{ __('Escolha um jogo') }}</option>
                    @foreach ($games as $game)
                        <option value="{{ $game->id }}" @selected((string) $selected === (string) $game->id)>{{ $game->title }}</option>
                    @endforeach
                </select>
                @error('game_id') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-row">
                <label for="title">{{ __('Título') }}</label>
                <input id="title" name="title" type="text" class="field" maxlength="140" value="{{ old('title') }}" required>
                @error('title') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-row">
                <label for="body">{{ __('Mensagem') }}</label>
                <textarea id="body" name="body" class="field" rows="7" maxlength="5000" required>{{ old('body') }}</textarea>
                @error('body') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">{{ __('Publicar tópico') }}</button>
            </div>
        </form>
    </div>
@endsection
