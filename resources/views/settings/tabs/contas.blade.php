<h2 class="panel-title">{{ __('Contas vinculadas') }}</h2>
<p class="muted panel-text">Conecte sua conta Steam ao Lobby com autenticação oficial. Sua senha nunca é compartilhada com o Lobby.</p>

@if (session('steam_error'))
    <div class="flash" role="alert">{{ session('steam_error') }}</div>
@endif

<ul class="linked-list">
    <li>
        <div>
            <strong>Steam</strong>
            @if ($steamAccount)
                <p class="muted" style="margin: 6px 0 0">Conectada · SteamID: {{ $steamAccount->steam_id }}</p>
                <a href="https://steamcommunity.com/profiles/{{ $steamAccount->steam_id }}" target="_blank" rel="noopener noreferrer">Ver perfil Steam</a>
            @else
                <p class="muted" style="margin: 6px 0 0">Não conectada</p>
            @endif
        </div>
        @if ($steamAccount)
            <form action="{{ route('steam.disconnect') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-outline">Desconectar</button>
            </form>
        @else
            <a class="btn-outline" href="{{ route('steam.connect') }}">Conectar Steam</a>
        @endif
    </li>
    <li><strong>Xbox</strong><button type="button" class="btn-outline" disabled>Em breve</button></li>
</ul>
<p class="muted panel-text">A conexão confirma a identidade da conta Steam. Importação de biblioteca e conquistas exige integração adicional com a Steam Web API e depende da privacidade do perfil.</p>
