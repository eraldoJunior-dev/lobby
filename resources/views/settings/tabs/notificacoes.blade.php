<h2 class="panel-title">{{ __('Notificações') }}</h2>
<p class="muted panel-text">{{ __('A central de notificações ainda não foi lançada. Suas preferências já ficam salvas para quando ela chegar.') }}</p>

<form class="form" method="POST" action="{{ route('settings.notifications') }}">
    @csrf
    @method('PUT')

    @foreach ([
        'reactions' => [__('Reações'), __('Quando alguém reagir às suas publicações.')],
        'replies' => [__('Respostas'), __('Quando alguém responder aos seus tópicos.')],
        'follows' => [__('Novos seguidores'), __('Quando alguém começar a seguir você.')],
        'news' => [__('Novidades do Lobby'), __('Notícias e lançamentos da plataforma.')],
    ] as $key => [$label, $hint])
        <label class="switch-row">
            <span><strong>{{ $label }}</strong><small class="muted">{{ $hint }}</small></span>
            <input type="checkbox" class="switch" name="{{ $key }}" value="1" @checked($user->setting('notifications.'.$key, true))>
        </label>
    @endforeach

    <div class="form-actions">
        <button type="submit" class="btn-primary">{{ __('Salvar') }}</button>
    </div>
</form>
