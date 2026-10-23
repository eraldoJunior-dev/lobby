@extends('layouts.feed')

@section('title', __('Configurações'))

@section('content')
    <header class="page-head"><h1>{{ __('Configurações') }}</h1></header>

    <div class="settings">
        <nav class="settings-nav" aria-label="{{ __('Seções das configurações') }}">
            @foreach ([
                'perfil' => 'user', 'aparencia' => 'palette', 'privacidade' => 'shield',
                'notificacoes' => 'bell', 'contas' => 'link', 'dados' => 'download',
            ] as $key => $icon)
                <a href="{{ route('settings.edit', ['aba' => $key]) }}" @class(['settings-link', 'is-active' => $tab === $key]) @if ($tab === $key) aria-current="page" @endif>
                    <x-icon :name="$icon" /><span>{{ $tabs[$key] }}</span>
                </a>
            @endforeach
        </nav>

        <section class="settings-panel card-box">
            @if (session('status'))
                <div class="flash" role="status">{{ session('status') }}</div>
            @endif

            @include('settings.tabs.'.$tab)
        </section>
    </div>
@endsection
