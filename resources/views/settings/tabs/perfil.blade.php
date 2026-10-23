<h2 class="panel-title">{{ __('Editar perfil') }}</h2>

<form class="form" method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="photo-row">
        <x-avatar :user="$user" :size="72" />
        <div>
            <label class="btn-outline file-btn">
                {{ __('Alterar foto') }}
                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" data-file-name="#avatar-name">
            </label>
            <span id="avatar-name" class="muted file-name"></span>
            @if ($user->avatar_path)
                <label class="check"><input type="checkbox" name="remove_avatar" value="1"> {{ __('Remover foto atual') }}</label>
            @endif
            @error('avatar') <p class="error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="form-row">
        <label for="name">{{ __('Nome de exibição') }}</label>
        <input id="name" name="name" type="text" class="field" maxlength="60" value="{{ old('name', $user->name) }}" required>
        @error('name') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-row">
        <label for="username">{{ __('Nome de usuário') }}</label>
        <input id="username" name="username" type="text" class="field" maxlength="30" value="{{ old('username', $user->username) }}" autocapitalize="none" spellcheck="false" required>
        @error('username') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-row"><label>Em qual plataforma você joga?</label><p class="muted">Edite os ícones que aparecem ao lado do seu nome.</p>
        <div class="lobby-platform-grid">@foreach (\App\Support\Platforms::OPTIONS as $key => [$label, $symbol])
        <label class="lobby-platform-choice"><input type="checkbox" name="platforms[]" value="{{ $key }}" @checked(in_array($key, old('platforms', \App\Support\Platforms::selected($user))))><span class="lobby-platform-symbol"><img src="{{ asset('assets/platforms/'.$key.'.svg') }}" alt="" class="lobby-platform-logo"></span><strong>{{ $label }}</strong><span class="lobby-platform-check">✓</span></label>
        @endforeach</div>@error('platforms')<p class="error">{{ $message }}</p>@enderror
    </div>
    <div class="form-row">
        <label for="pronouns_choice">{{ __('Pronomes') }}</label>
        <select id="pronouns_choice" name="pronouns_choice" class="field">
            <option value="">{{ __('Prefiro não informar') }}</option>
            @foreach ($pronounOptions as $option)
                <option value="{{ $option }}" @selected(old('pronouns_choice', $pronounChoice) === $option)>{{ $option }}</option>
            @endforeach
        </select>
        <label class="sub-label" for="pronouns_custom">{{ __('Ou escreva os seus:') }}</label>
        <input id="pronouns_custom" name="pronouns_custom" type="text" class="field" maxlength="40" placeholder="{{ __('Pronomes personalizados') }}" value="{{ old('pronouns_custom', $pronounCustom) }}">
        @error('pronouns_choice') <p class="error">{{ $message }}</p> @enderror
        @error('pronouns_custom') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-row">
        <label for="status">{{ __('Status') }}</label>
        <input id="status" name="status" type="text" class="field" maxlength="80" placeholder="{{ __('O que você está jogando?') }}" value="{{ old('status', $user->status) }}">
        @error('status') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-row">
        <label for="bio">{{ __('Bio') }}</label>
        <textarea id="bio" name="bio" class="field" rows="4" maxlength="300" placeholder="{{ __('Conte o que você joga') }}">{{ old('bio', $user->bio) }}</textarea>
        @error('bio') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-row">
        <label for="location">{{ __('Localização') }}</label>
        <input id="location" name="location" type="text" class="field" maxlength="80" value="{{ old('location', $user->location) }}">
        @error('location') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-row">
        <label for="favorite_genre">{{ __('Gênero favorito') }}</label>
        <select id="favorite_genre" name="favorite_genre" class="field">
            <option value="">{{ __('Nenhum') }}</option>
            @foreach ($genres as $genre)
                <option value="{{ $genre }}" @selected(old('favorite_genre', $user->favorite_genre) === $genre)>{{ __($genre) }}</option>
            @endforeach
        </select>
        @error('favorite_genre') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-row">
        <label for="website">{{ __('Site') }}</label>
        <input id="website" name="website" type="url" class="field" maxlength="200" placeholder="{{ __('https://') }}" value="{{ old('website', $user->website) }}">
        @error('website') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">{{ __('Salvar alterações') }}</button>
    </div>
</form>
