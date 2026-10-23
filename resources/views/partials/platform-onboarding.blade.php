@if (auth()->check() && ! auth()->user()->setting('platforms_onboarded', false))
<div class="lobby-modal-backdrop" id="platform-modal" role="dialog" aria-modal="true" aria-labelledby="platform-modal-title">
  <form class="lobby-modal-card" method="POST" action="{{ route('platforms.update') }}">@csrf
    <div class="lobby-modal-symbol"><x-icon name="gamepad" /></div><h2 id="platform-modal-title">Em qual plataforma você joga?</h2>
    <p>Escolha uma ou mais opções. Elas aparecerão no seu perfil e você poderá mudar depois.</p>
    <div class="lobby-platform-grid">@foreach (\App\Support\Platforms::OPTIONS as $key => [$label, $symbol])
      <label class="lobby-platform-choice"><input type="checkbox" name="platforms[]" value="{{ $key }}" @checked(in_array($key, \App\Support\Platforms::selected(auth()->user())))><span class="lobby-platform-symbol"><img src="{{ asset('assets/platforms/'.$key.'.svg') }}" alt="" class="lobby-platform-logo"></span><strong>{{ $label }}</strong><span class="lobby-platform-check">✓</span></label>
    @endforeach</div>
    @error('platforms')<p class="error">{{ $message }}</p>@enderror
    <button type="submit" class="btn-primary" style="width:100%;margin-top:18px">Continuar para o Lobby →</button>
  </form>
</div>
@endif
