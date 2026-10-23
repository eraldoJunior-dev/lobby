@extends('layouts.app')

@section('title', __('Entrar'))

@section('content')
    <div class="card">
        <div class="eyebrow">{{ __('Bem-vindo de volta') }}</div>
        <h1>{{ __('Entrar no Lobby') }}</h1>
        <p class="subtitle">{{ __('Acesse sua conta para continuar.') }}</p>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="field">
                <label for="email">{{ __('E-mail') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       autocomplete="email" required autofocus
                       @class(['invalid' => $errors->has('email')])>
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="password">{{ __('Senha') }}</label>
                <input type="password" id="password" name="password"
                       autocomplete="current-password" required
                       @class(['invalid' => $errors->has('password')])>
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>

            <label class="check">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                {{ __('Manter conectado') }}
            </label>

            <button type="submit" class="button-primary">{{ __('Entrar ↗') }}</button>
        </form>

        <p class="alt">{{ __('Ainda não tem conta?') }} <a href="{{ route('register') }}">{{ __('Criar conta') }}</a></p>
    </div>
@endsection
