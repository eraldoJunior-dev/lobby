<h2 class="panel-title">{{ __('Privacidade') }}</h2>

<form class="form" method="POST" action="{{ route('settings.privacy') }}">
    @csrf
    @method('PUT')

    <label class="switch-row">
        <span><strong>{{ __('Perfil público') }}</strong><small class="muted">{{ __('Outras pessoas podem ver sua bio e suas informações. Desligado, só você vê o perfil completo.') }}</small></span>
        <input type="checkbox" class="switch" name="public_profile" value="1" @checked($user->setting('privacy.public_profile', true))>
    </label>

    <label class="switch-row">
        <span><strong>{{ __('Mostrar minha biblioteca') }}</strong><small class="muted">{{ __('Estatísticas, gêneros favoritos e jogos favoritos aparecem no seu perfil.') }}</small></span>
        <input type="checkbox" class="switch" name="show_library" value="1" @checked($user->setting('privacy.show_library', true))>
    </label>

    <div class="form-actions">
        <button type="submit" class="btn-primary">{{ __('Salvar') }}</button>
    </div>
</form>
