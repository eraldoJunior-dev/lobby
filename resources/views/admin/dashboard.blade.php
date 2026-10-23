@extends('layouts.feed')
@section('title', 'Lobby Admin')
@section('content')
<style>
.lobby-admin{--adm-border:rgba(169,139,220,.20);--adm-panel:#20192c;--adm-muted:#b9afc9;--adm-accent:#a66aff;max-width:1120px;width:100%;padding:28px 22px 55px;color:#f6f2fc;font-family:Inter,system-ui,sans-serif}
.lobby-admin *{box-sizing:border-box}
.lobby-admin .adm-hero{position:relative;overflow:hidden;background:linear-gradient(125deg,#302047,#1d1729 75%);border:1px solid var(--adm-border);border-radius:22px;padding:30px;margin-bottom:24px}
.lobby-admin .adm-hero:after{content:"";position:absolute;width:220px;height:220px;right:-85px;top:-100px;border-radius:50%;background:radial-gradient(circle,rgba(172,103,255,.26),transparent 70%);pointer-events:none}
.lobby-admin .adm-eyebrow{font-size:11px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:#c49aff;margin-bottom:10px}
.lobby-admin h1{font-size:clamp(27px,4vw,38px);line-height:1.15;margin:0 0 10px;font-weight:800;color:#fff}
.lobby-admin .adm-subtitle{margin:0;color:#c9bed7;font-size:14px}
.lobby-admin .adm-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px;margin:20px 0 29px}
.lobby-admin .adm-stat{background:#201a2b;border:1px solid var(--adm-border);border-radius:17px;padding:18px 20px}
.lobby-admin .adm-stat-label{display:block;font-size:12px;color:var(--adm-muted);margin-bottom:9px}
.lobby-admin .adm-stat-number{font-size:29px;line-height:1;font-weight:800;color:#fff}
.lobby-admin .adm-section{margin:0 0 30px}
.lobby-admin .adm-section-heading{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:15px}
.lobby-admin h2{font-size:20px;letter-spacing:-.03em;color:#fff;margin:0;font-weight:750}
.lobby-admin .adm-count{font-size:12px;color:#c8b6e1;border:1px solid var(--adm-border);background:#292036;border-radius:30px;padding:5px 11px}
.lobby-admin .adm-list{display:grid;gap:12px}
.lobby-admin .adm-card{background:var(--adm-panel);border:1px solid var(--adm-border);border-radius:16px;padding:18px;min-width:0;transition:border-color .2s,transform .2s}
.lobby-admin .adm-card:hover{border-color:rgba(177,117,255,.50)}
.lobby-admin .adm-card-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap}
.lobby-admin .adm-name{font-size:15px;font-weight:750;color:#fff;overflow-wrap:anywhere}
.lobby-admin .adm-meta{font-size:12px;color:var(--adm-muted);margin-top:6px;overflow-wrap:anywhere}
.lobby-admin .adm-tag{display:inline-flex;align-items:center;gap:5px;border:1px solid #5d477e;background:#342547;border-radius:50px;padding:5px 10px;color:#d8b9ff;font-size:11px;font-weight:700;white-space:nowrap}
.lobby-admin .adm-tag--open{color:#ffd5a0;border-color:#76583c;background:#3b2c25}
.lobby-admin .adm-tag--resolved{color:#9ce6b7;border-color:#365b4a;background:#20372d}
.lobby-admin .adm-tag--closed{color:#bcb6c7;border-color:#514a59;background:#2d2934}
.lobby-admin .adm-controls{display:flex;align-items:flex-end;gap:9px;flex-wrap:wrap;margin-top:16px}
.lobby-admin .adm-field{display:flex;flex-direction:column;gap:6px;min-width:0;flex:1 1 175px}
.lobby-admin .adm-field label{font-size:11px;color:#c9bed8;font-weight:700}
.lobby-admin select,.lobby-admin input{display:block;min-height:41px;width:100%;min-width:0;border:1px solid #534365;border-radius:10px;background:#171220;color:#f8f4ff;padding:9px 11px;font:inherit;font-size:13px;outline:none}
.lobby-admin input::placeholder{color:#92879e}
.lobby-admin select:focus,.lobby-admin input:focus{border-color:#b98cff;box-shadow:0 0 0 3px rgba(166,106,255,.15)}
.lobby-admin .adm-btn{min-height:41px;border:1px solid transparent;border-radius:10px;background:#9854ed;color:white;padding:9px 16px;font:inherit;font-size:12px;font-weight:800;cursor:pointer;transition:background .2s,transform .2s;white-space:nowrap}
.lobby-admin .adm-btn:hover{background:#af76fa;transform:translateY(-1px)}
.lobby-admin .adm-btn--secondary{background:#332640;border-color:#665078;color:#e9dafa}
.lobby-admin .adm-btn--secondary:hover{background:#473357}
.lobby-admin .adm-btn:focus-visible,.lobby-admin a:focus-visible{outline:3px solid #c49aff;outline-offset:3px}
.lobby-admin .adm-link{display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#c99eff;text-decoration:none;font-weight:700;margin-top:12px}
.lobby-admin .adm-link:hover{text-decoration:underline}
.lobby-admin .adm-divider{height:1px;background:var(--adm-border);margin:15px 0}
.lobby-admin .adm-flash{border:1px solid #3b775a;background:#1d382b;color:#a9ebc7;padding:12px 15px;border-radius:12px;margin:0 0 18px}
.lobby-admin .adm-empty{padding:24px;border:1px dashed #625276;border-radius:14px;color:#bcb1ca;text-align:center;font-size:13px}
.lobby-admin .adm-history{display:grid;gap:8px}
.lobby-admin .adm-history-item{display:grid;grid-template-columns:minmax(100px,1fr) minmax(100px,1fr) 2fr auto;gap:12px;align-items:center;padding:13px 15px;background:var(--adm-panel);border:1px solid var(--adm-border);border-radius:12px;font-size:12px;color:#cfc3dd}
.lobby-admin .adm-history-item strong{color:#f6f0ff;overflow-wrap:anywhere}
.lobby-admin .adm-history-item span{overflow-wrap:anywhere}
.lobby-admin .adm-history-item time{white-space:nowrap;color:#9e93ad}
.lobby-admin .adm-pagination{margin-top:16px}
@media(max-width:800px){.lobby-admin{padding:20px 13px 40px}.lobby-admin .adm-stats{grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}.lobby-admin .adm-stat{padding:14px 11px}.lobby-admin .adm-stat-number{font-size:24px}.lobby-admin .adm-history-item{grid-template-columns:1fr 1fr}.lobby-admin .adm-history-item time{white-space:normal}}
@media(max-width:520px){.lobby-admin{padding:16px 10px 35px}.lobby-admin .adm-hero{padding:23px 18px}.lobby-admin .adm-stats{grid-template-columns:1fr 1fr}.lobby-admin .adm-stat:last-child{grid-column:1/-1}.lobby-admin .adm-card{padding:15px}.lobby-admin .adm-controls{flex-direction:column;align-items:stretch}.lobby-admin .adm-field,.lobby-admin .adm-btn{width:100%;flex-basis:auto}.lobby-admin .adm-history-item{grid-template-columns:1fr}.lobby-admin h2{font-size:18px}}
@media(prefers-reduced-motion:reduce){.lobby-admin .adm-card,.lobby-admin .adm-btn{transition:none}.lobby-admin .adm-btn:hover{transform:none}}
</style>
<div class="page-body lobby-admin">
    <header class="adm-hero">
        <div class="adm-eyebrow">Painel de gerenciamento</div>
        <h1>Lobby Admin</h1>
        <p class="adm-subtitle">Gerencie denúncias, suporte e moderação em um só lugar.</p>
    </header>

    @if(session('success'))
        <div class="adm-flash" role="status">{{ session('success') }}</div>
    @endif

    <div class="adm-stats" aria-label="Resumo administrativo">
        <div class="adm-stat"><span class="adm-stat-label">Chamados nesta página</span><strong class="adm-stat-number">{{ $tickets->count() }}</strong></div>
        <div class="adm-stat"><span class="adm-stat-label">Usuários recentes</span><strong class="adm-stat-number">{{ $users->count() }}</strong></div>
        <div class="adm-stat"><span class="adm-stat-label">Ações registradas</span><strong class="adm-stat-number">{{ $actions->count() }}</strong></div>
    </div>

    <section class="adm-section" aria-labelledby="adm-tickets-title">
        <div class="adm-section-heading"><h2 id="adm-tickets-title">Chamados e denúncias</h2><span class="adm-count">{{ $tickets->total() }} no total</span></div>
        <div class="adm-list">
            @forelse($tickets as $t)
                <article class="adm-card">
                    <div class="adm-card-head">
                        <div>
                            <div class="adm-name">#{{ $t->id }} · {{ $t->subject }}</div>
                            <div class="adm-meta">Enviado por {{ '@'.$t->username }} · {{ str_replace('_',' ', $t->kind) }}</div>
                        </div>
                        <span class="adm-tag @if($t->status==='open') adm-tag--open @elseif($t->status==='resolved') adm-tag--resolved @elseif($t->status==='closed') adm-tag--closed @endif">{{ ['open'=>'Aberto','in_review'=>'Em análise','resolved'=>'Resolvido','closed'=>'Encerrado'][$t->status] ?? $t->status }}</span>
                    </div>
                    <a class="adm-link" href="{{ route('support.show',$t->id) }}">Abrir conversa →</a>
                    <form method="POST" action="{{ route('admin.status',$t->id) }}" class="adm-controls">
                        @csrf
                        <div class="adm-field">
                            <label for="ticket-status-{{ $t->id }}">Status do chamado</label>
                            <select id="ticket-status-{{ $t->id }}" name="status">
                                <option value="open" @selected($t->status==='open')>Aberto</option>
                                <option value="in_review" @selected($t->status==='in_review')>Em análise</option>
                                <option value="resolved" @selected($t->status==='resolved')>Resolvido</option>
                                <option value="closed" @selected($t->status==='closed')>Encerrado</option>
                            </select>
                        </div>
                        <button class="adm-btn" type="submit">Atualizar status</button>
                    </form>
                </article>
            @empty
                <div class="adm-empty">Nenhum chamado ou denúncia por enquanto.</div>
            @endforelse
        </div>
        <div class="adm-pagination">{{ $tickets->links() }}</div>
    </section>

    <section class="adm-section" aria-labelledby="adm-users-title">
        <div class="adm-section-heading"><h2 id="adm-users-title">Usuários recentes</h2><span class="adm-count">{{ $users->count() }} exibidos</span></div>
        <div class="adm-list">
            @forelse($users as $u)
                <article class="adm-card">
                    <div class="adm-card-head">
                        <div>
                            <div class="adm-name">{{ '@'.$u->username }}</div>
                            <div class="adm-meta">{{ $u->name }} · {{ $u->email }}</div>
                        </div>
                        <span class="adm-tag">{{ ['owner'=>'Proprietário','moderator'=>'Moderador','user'=>'Usuário'][$u->lobby_role] ?? $u->lobby_role }}</span>
                    </div>
                    @if($u->lobby_role==='user')
                        <form method="POST" action="{{ route('admin.moderate',$u->id) }}" class="adm-controls">
                            @csrf
                            <div class="adm-field">
                                <label for="action-{{ $u->id }}">Ação de moderação</label>
                                <select id="action-{{ $u->id }}" name="action">
                                    <option value="warn">Advertir</option>
                                    <option value="suspend_1d">Suspender 1 dia</option>
                                    <option value="suspend_7d">Suspender 7 dias</option>
                                    <option value="suspend_30d">Suspender 30 dias</option>
                                    <option value="ban">Banir</option>
                                    <option value="unban">Remover punição</option>
                                </select>
                            </div>
                            <div class="adm-field">
                                <label for="reason-{{ $u->id }}">Motivo</label>
                                <input id="reason-{{ $u->id }}" name="reason" required minlength="5" maxlength="1000" placeholder="Descreva o motivo da medida">
                            </div>
                            <button class="adm-btn" type="submit" onclick="return confirm('Confirmar ação de moderação?')">Aplicar</button>
                        </form>
                    @endif
                    @if(auth()->user()->lobby_role==='owner' && $u->lobby_role!=='owner')
                        <div class="adm-divider"></div>
                        <form method="POST" action="{{ route('admin.roles',$u->id) }}" class="adm-controls">
                            @csrf
                            <div class="adm-field">
                                <label for="role-{{ $u->id }}">Cargo no Lobby</label>
                                <select id="role-{{ $u->id }}" name="role">
                                    <option value="user" @selected($u->lobby_role==='user')>Usuário</option>
                                    <option value="moderator" @selected($u->lobby_role==='moderator')>Moderador</option>
                                </select>
                            </div>
                            <button class="adm-btn adm-btn--secondary" type="submit">Salvar cargo</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="adm-empty">Nenhum usuário encontrado.</div>
            @endforelse
        </div>
    </section>

    <section class="adm-section" aria-labelledby="adm-history-title">
        <div class="adm-section-heading"><h2 id="adm-history-title">Histórico de ações</h2><span class="adm-count">Últimas {{ $actions->count() }}</span></div>
        <div class="adm-history">
            @forelse($actions as $a)
                <div class="adm-history-item">
                    <strong>#{{ $a->id }} · {{ str_replace('_',' ', $a->action) }}</strong>
                    <span>Usuário #{{ $a->target_user_id }}</span>
                    <span>{{ $a->reason }}</span>
                    <time>{{ $a->created_at }}</time>
                </div>
            @empty
                <div class="adm-empty">Ainda não há ações de moderação registradas.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
