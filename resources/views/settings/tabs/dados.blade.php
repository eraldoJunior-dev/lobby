<h2 class="panel-title">{{ __('Dados') }}</h2>

<div class="data-block">
    <h3>{{ __('Baixar meus dados') }}</h3>
    <p class="muted panel-text">{{ __('Receba um arquivo JSON com sua conta, publicações, tópicos, respostas e biblioteca de jogos.') }}</p>
    <a class="btn-outline" href="{{ route('settings.export') }}"><x-icon name="download" /> {{ __('Baixar arquivo') }}</a>
</div>

<div class="data-block danger-zone">
    <h3>{{ __('Excluir minha conta') }}</h3>
    <p class="muted panel-text">{{ __('Isso apaga sua conta e tudo o que você publicou. Não dá para desfazer.') }}</p>

    <form class="form" method="POST" action="{{ route('settings.destroy') }}" data-confirm="{{ __('Excluir a conta definitivamente?') }}">
        @csrf
        @method('DELETE')
        <div class="form-row">
            <label for="password">{{ __('Digite sua senha para confirmar') }}</label>
            <input id="password" name="password" type="password" class="field" autocomplete="current-password" required>
            @error('password') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-danger">{{ __('Excluir conta') }}</button>
        </div>
    </form>
</div>
