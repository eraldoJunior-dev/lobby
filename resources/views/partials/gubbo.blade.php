<link rel="stylesheet" href="{{ asset('css/gubbo.css') }}?v={{ filemtime(public_path('css/gubbo.css')) }}">
<aside class="gubbo-widget" aria-label="Gubbo Bot">
    <section class="gubbo-panel" id="gubbo-panel" aria-label="Gubbo Bot — perguntas frequentes" hidden>
        <header class="gubbo-header"><span class="gubbo-face" aria-hidden="true"><img src="{{ asset('images/lobiz-acenando-logo.png') }}" alt="" width="48" height="48"></span><div><strong>Gubbo Bot</strong><small>Ajuda rápida do Lobby</small></div><button type="button" class="gubbo-close" aria-label="Fechar ajuda">×</button></header>
        <div class="gubbo-messages" role="log" aria-live="polite" aria-relevant="additions"><p class="gubbo-answer">Oi! Eu sou o Gubbo. Escolha uma pergunta ou escreva sua dúvida sobre o Lobby. Respondo com orientações prontas.</p></div>
        <div class="gubbo-questions" aria-label="Perguntas frequentes"></div>
        <form class="gubbo-form"><label class="gubbo-sr" for="gubbo-input">Sua pergunta</label><input id="gubbo-input" maxlength="300" placeholder="Como avaliar um jogo?" autocomplete="off"><button type="submit" aria-label="Enviar pergunta">Enviar</button></form>

        @auth
        <details class="gubbo-support"><summary>Suporte humano / Fazer denúncia</summary>
        <form action="{{ route('support.create') }}" method="POST" class="gubbo-ticket-form">@csrf
        <label>Tipo<select name="kind" required><option value="support">Preciso de ajuda</option><option value="report_user">Denunciar usuário</option><option value="report_post">Denunciar publicação</option><option value="report_review">Denunciar avaliação</option><option value="report_other">Outra denúncia</option></select></label>
        <label>Assunto<input name="subject" required minlength="5" maxlength="120" placeholder="Resuma o problema"></label>
        <label>Descreva o ocorrido<textarea name="description" required minlength="15" maxlength="4000" rows="4" placeholder="Explique o problema e informe o usuário ou conteúdo envolvido"></textarea></label>
        <label>Link do conteúdo (opcional)<input name="target_url" maxlength="500" placeholder="Cole o link do perfil ou publicação"></label>
        <button type="submit">Enviar ao suporte</button>
        </form><a href="{{ route('support.mine') }}">Acompanhar meus chamados</a></details>
        @else
        <p class="gubbo-note">Entre na sua conta para abrir um chamado de suporte.</p>
        @endauth
        <small class="gubbo-note">Ajuda automática · sem acesso à sua conta</small>
    </section>
    <button type="button" class="gubbo-launcher" aria-controls="gubbo-panel" aria-expanded="false" aria-label="Abrir Gubbo Bot"><span class="gubbo-face" aria-hidden="true"><img src="{{ asset('images/lobiz-acenando-logo.png') }}" alt="" width="48" height="48"></span></button>
</aside>
<script src="{{ asset('js/gubbo.js') }}?v={{ filemtime(public_path('js/gubbo.js')) }}" defer></script>
