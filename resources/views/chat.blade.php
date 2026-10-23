@extends('layouts.feed')

@section('title', __('Chat'))

@section('content')
    <header class="page-head">
        <h1>{{ __('Chat') }} <span class="tag beta">{{ __('Beta') }}</span></h1>
    </header>

    <div class="page-body">
        <section class="beta-card card-box" aria-labelledby="beta-title">
            <x-icon name="chat" class="beta-icon" />
            <h2 id="beta-title">{{ __('O chat está em beta e ainda não foi lançado') }}</h2>
            <p>{{ __('Por enquanto não é possível conversar com outros perfis. Estamos preparando tudo e avisaremos quando a novidade estiver disponível.') }}</p>
        </section>

        <div class="chat-mock" aria-hidden="true">
            <input type="text" class="field" placeholder="{{ __('Enviar mensagem (em breve)') }}" disabled>
            <button type="button" class="btn-primary" disabled>{{ __('Enviar') }}</button>
        </div>
    </div>
@endsection
