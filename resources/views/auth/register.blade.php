@extends('layouts.app')

@section('title', __('Criar conta'))

@section('content')
    <div class="card">
        <div class="eyebrow">{{ __('Faça parte') }}</div>
        <h1>{{ __('Criar conta') }}</h1>
        <p class="subtitle">{{ __('Entre para a comunidade gamer do Lobby.') }}</p>

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="field">
                <label for="name">{{ __('Nome') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       autocomplete="name" required autofocus
                       @class(['invalid' => $errors->has('name')])>
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="email">{{ __('E-mail') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       autocomplete="email" required
                       @class(['invalid' => $errors->has('email')])>
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="cpf">{{ __('CPF') }}</label>
                <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}"
                       inputmode="numeric" autocomplete="off" maxlength="14" placeholder="000.000.000-00" required
                       @class(['invalid' => $errors->has('cpf')])>
                @error('cpf') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="birth_date">{{ __('Data de nascimento') }}</label>
                <input type="text" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                       inputmode="numeric" autocomplete="bday" maxlength="10" placeholder="DD/MM/AAAA" required
                       @class(['invalid' => $errors->has('birth_date')])>
                @error('birth_date') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="password">{{ __('Senha') }}</label>
                <input type="password" id="password" name="password"
                       autocomplete="new-password" required minlength="8"
                       @class(['invalid' => $errors->has('password')])>
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">{{ __('Confirmar senha') }}</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       autocomplete="new-password" required minlength="8">
            </div>

            <button type="submit" class="button-primary">{{ __('Criar conta ↗') }}</button>
        </form>
        <script>
            (() => {
                const cpf = document.getElementById('cpf');
                const birth = document.getElementById('birth_date');
                cpf?.addEventListener('input', () => {
                    const digits = cpf.value.replace(/\D/g, '').slice(0, 11);
                    cpf.value = digits.replace(/^(\d{3})(\d)/, '$1.$2').replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1-$2');
                });
                birth?.addEventListener('input', () => {
                    const digits = birth.value.replace(/\D/g, '').slice(0, 8);
                    birth.value = digits.replace(/^(\d{2})(\d)/, '$1/$2').replace(/^(\d{2})\/(\d{2})(\d)/, '$1/$2/$3');
                });
            })();
        </script>

        <p class="alt">{{ __('Já tem conta?') }} <a href="{{ route('login') }}">{{ __('Entrar') }}</a></p>
    </div>
@endsection
