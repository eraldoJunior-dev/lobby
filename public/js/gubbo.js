(() => {
'use strict';
const widget = document.querySelector('.gubbo-widget');
if (!widget) return;
const panel = widget.querySelector('.gubbo-panel');
const launcher = widget.querySelector('.gubbo-launcher');
const input = widget.querySelector('input');
const log = widget.querySelector('.gubbo-messages');
const faqs = [
 ['Como criar uma conta?', 'Na landing page, clique em criar conta ou cadastrar. Preencha os dados do formulário e escolha uma senha. Se já tem conta, use Entrar.', ['conta', 'cadastro', 'cadastrar', 'registrar', 'entrar', 'login']],
 ['Como publicar?', 'No Início, escreva no campo “O que você está jogando?”. Você pode escolher um jogo, marcar a nota e indicar spoilers. Depois clique em Publicar.', ['publicar', 'publicacao', 'postar', 'post']],
 ['Como dar meia estrela?', 'Na avaliação, clique na metade esquerda de uma estrela para uma nota com 0,5 ou na metade direita para a estrela inteira. As notas são 0,5 · 1 · 1,5 · 2 · 2,5 · 3 · 3,5 · 4 · 4,5 · 5. Clique novamente na nota marcada para removê-la.', ['avaliar', 'avaliacao', 'nota', 'estrela', 'estrelas', 'meia']],
 ['Como organizar meus jogos?', 'Abra Jogos, escolha um título e use as opções da sua biblioteca na página do jogo. Você pode registrar seu progresso, sua avaliação e sua resenha.', ['biblioteca', 'jogos', 'jogo', 'progresso', 'resenha']],
 ['Como evitar spoilers?', 'Ao publicar, marque “Contém spoilers” se necessário. Em Configurações, na aparência, você também pode ajustar a preferência de esconder spoilers.', ['spoiler', 'spoilers']],
 ['Como mudar meu perfil ou tema?', 'Abra Configurações. Use as opções de perfil para seus dados públicos e Aparência para tema, cor de destaque, tamanho do texto e outras preferências.', ['perfil', 'tema', 'aparencia', 'cor', 'configuracoes']],
 ['O Gubbo é uma IA?', 'Sou um assistente de perguntas frequentes com respostas prontas. Não consulto sua conta, não altero dados e não envio suas perguntas para um serviço de IA.', ['ia', 'inteligencia', 'bot', 'gubbo', 'privacidade']]
];
const normalize = text => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9 ]/g, ' ');
function append(text, cls) {
 const p = document.createElement('p'); p.className = cls; p.textContent = text; log.append(p);
 while (log.children.length > 31) log.firstElementChild.remove();
 log.scrollTop = log.scrollHeight;
}
function answer(question, known) {
 append(question, 'gubbo-user');
 const words = normalize(question).split(/\s+/);
 const ranked = faqs.map(faq => ({faq, score: faq[2].filter(key => words.includes(key)).length})).sort((a,b) => b.score-a.score);
 append(known || (ranked[0].score ? ranked[0].faq[1] : 'Ainda não tenho uma resposta pronta para essa dúvida. Tente uma das perguntas acima ou descreva usando termos como perfil, publicar, biblioteca ou estrelas.'), 'gubbo-answer');
}
faqs.forEach(faq => { const button = document.createElement('button'); button.type='button'; button.textContent=faq[0]; button.addEventListener('click', () => answer(faq[0], faq[1])); widget.querySelector('.gubbo-questions').append(button); });
function setOpen(open) { panel.hidden=!open; launcher.setAttribute('aria-expanded', String(open)); launcher.setAttribute('aria-label', open ? 'Fechar Gubbo Bot' : 'Abrir Gubbo Bot'); if(open) input.focus(); else launcher.focus(); }
launcher.addEventListener('click', () => setOpen(panel.hidden));
widget.querySelector('.gubbo-close').addEventListener('click', () => setOpen(false));
widget.addEventListener('keydown', event => { if(event.key==='Escape' && !panel.hidden) { event.preventDefault(); setOpen(false); } });
widget.querySelector('form').addEventListener('submit', event => { event.preventDefault(); const question=input.value.trim(); if(!question) return; answer(question); input.value=''; input.focus(); });
})();
