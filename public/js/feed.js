(() => {
    'use strict';

    const T = Object.assign({ saved: 'Saved', save: 'Save', following: 'Following', follow: 'Follow', sessionExpired: 'Session expired. Reload the page.', failed: 'Something went wrong. Try again.' }, window.LOBBY_I18N || {});

    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

    /* ---------- Aviso rápido ---------- */
    const toastEl = $('#toast');
    let toastTimer;
    const toast = (msg) => {
        if (!toastEl) return;
        toastEl.textContent = msg;
        toastEl.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toastEl.classList.remove('is-visible'), 3200);
    };

    /* ---------- Aparência (tema, cor, densidade, texto, movimento, contraste, spoilers) ---------- */
    const root = document.documentElement;
    const THEMES = ['purple', 'dark', 'light'];
    const csrf = () => (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    const apUrl = () => (document.querySelector('meta[name="appearance-url"]') || {}).content || '';

    const sendAppearance = (payload) => fetch(apUrl(), {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
    }).then((res) => { if (!res.ok) throw new Error(String(res.status)); return res; });

    const applyAppearance = (p) => {
        if ('theme' in p) root.dataset.theme = p.theme;
        if ('accent' in p) root.dataset.accent = p.accent;
        if ('density' in p) root.dataset.density = p.density;
        if ('text_size' in p) root.dataset.text = p.text_size;
        if ('reduce_motion' in p) { if (p.reduce_motion) root.dataset.motion = 'reduce'; else delete root.dataset.motion; }
        if ('high_contrast' in p) { if (p.high_contrast) root.dataset.contrast = 'high'; else delete root.dataset.contrast; }
        if ('hide_spoilers' in p) root.dataset.spoilers = p.hide_spoilers ? 'hide' : 'show';
    };

    // Reflete o estado atual nos controles da aba Aparência
    const syncAppearanceUi = () => {
        const current = { theme: root.dataset.theme, accent: root.dataset.accent, density: root.dataset.density, text_size: root.dataset.text };
        $$('[data-ap-key]').forEach((btn) => {
            btn.setAttribute('aria-pressed', String(btn.dataset.apValue === current[btn.dataset.apKey]));
        });
        $$('[data-ap-toggle]').forEach((box) => {
            const key = box.dataset.apToggle;
            box.checked = key === 'reduce_motion' ? root.dataset.motion === 'reduce'
                : key === 'high_contrast' ? root.dataset.contrast === 'high'
                : root.dataset.spoilers === 'hide';
        });
    };

    // Aplica na hora e grava na conta logo em seguida
    let saveTimer;
    let pending = {};
    const saveAppearance = (payload) => {
        applyAppearance(payload);
        syncAppearanceUi();
        pending = { ...pending, ...payload };
        const note = $('#ap-saved');
        if (note) note.textContent = '';
        clearTimeout(saveTimer);
        saveTimer = setTimeout(async () => {
            const body = pending;
            pending = {};
            try {
                await sendAppearance(body);
                if (note) note.textContent = note.dataset.saved || '';
            } catch (e) {
                toast((note && note.dataset.error) || 'Error');
            }
        }, 150);
    };

    $$('[data-ap-key]').forEach((btn) => {
        btn.addEventListener('click', () => saveAppearance({ [btn.dataset.apKey]: btn.dataset.apValue }));
    });
    $$('[data-ap-toggle]').forEach((box) => {
        box.addEventListener('change', () => saveAppearance({ [box.dataset.apToggle]: box.checked }));
    });
    syncAppearanceUi();

    // Botão do menu: alterna roxo → preto → branco
    $$('[data-theme-cycle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            saveAppearance({ theme: THEMES[(THEMES.indexOf(root.dataset.theme) + 1) % THEMES.length] });
        });
    });

    /* ---------- Atalhos ---------- */
    document.addEventListener('keydown', (e) => {
        if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey) return;
        const tag = (e.target.tagName || '').toLowerCase();
        if (['input', 'textarea', 'select'].includes(tag) || e.target.isContentEditable) return;

        const search = $('[data-search]');
        if (search && search.offsetParent !== null) {
            e.preventDefault();
            search.focus();
        }
    });

    $$('[data-focus-composer]').forEach((link) => {
        link.addEventListener('click', (e) => {
            const box = $('#body');
            if (!box) return;
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
            box.focus({ preventScroll: true });
        });
    });

    /* ---------- Campo de publicação ---------- */
    const body = $('#body');
    if (body) {
        const counter = $(body.dataset.counter || '#body-count');
        const max = Number(body.getAttribute('maxlength')) || 2000;

        const refresh = () => {
            body.style.height = 'auto';
            body.style.height = body.scrollHeight + 'px';

            if (!counter) return;
            const len = body.value.length;
            counter.textContent = len ? `${len}/${max}` : '';
            counter.classList.toggle('is-near', len > max * 0.9);
        };

        body.addEventListener('input', refresh);
        refresh();
    }

    // Clicar de novo na estrela marcada remove a nota
    const stars = $$('.rating-input input');
    stars.forEach((r) => {
        r.dataset.was = r.checked ? '1' : '0';
        r.addEventListener('click', () => {
            if (r.dataset.was === '1') {
                r.checked = false;
                r.dataset.was = '0';
            } else {
                stars.forEach((x) => { x.dataset.was = '0'; });
                r.dataset.was = '1';
            }
        });
    });

    /* ---------- Spoilers e confirmações ---------- */
    document.addEventListener('click', (e) => {
        const reveal = e.target.closest('[data-reveal]');
        if (reveal) reveal.parentElement.classList.remove('hidden');
    });

    document.addEventListener('submit', (e) => {
        const msg = e.target.dataset && e.target.dataset.confirm;
        if (msg && !window.confirm(msg)) e.preventDefault();
    });

    /* ---------- Reagir, salvar e seguir sem recarregar a página ---------- */
    const handlers = {
        reaction(form, data) {
            Object.entries(data.reactions || {}).forEach(([type, state]) => {
                const btn = $(`.reaction[data-type="${type}"]`, form);
                if (!btn) return;
                btn.classList.toggle('is-active', state.active);
                btn.setAttribute('aria-pressed', state.active ? 'true' : 'false');
                $('[data-count]', btn).textContent = state.count || '';
            });
        },
        save(form, data) {
            const btn = $('.save-btn', form);
            btn.classList.toggle('is-active', data.saved);
            btn.setAttribute('aria-pressed', data.saved ? 'true' : 'false');
            $('.icon', btn).classList.toggle('icon-filled', data.saved);
            $('[data-save-label]', btn).textContent = data.saved ? T.saved : T.save;
        },
        follow(form, data) {
            const btn = $('[data-follow-btn]', form);
            btn.classList.toggle('is-active', data.following);
            btn.textContent = data.following ? T.following : T.follow;
        },
    };

    document.addEventListener('submit', async (e) => {
        const form = e.target;
        const kind = form.dataset && form.dataset.ajax;
        if (!kind || !handlers[kind] || e.defaultPrevented) return;

        e.preventDefault();
        if (form.dataset.busy) return;

        const payload = new FormData(form);
        if (e.submitter && e.submitter.name) payload.set(e.submitter.name, e.submitter.value);

        form.dataset.busy = '1';
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: payload,
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (res.status === 419 || res.status === 401) {
                toast(T.sessionExpired);
                return;
            }
            if (!res.ok) throw new Error(String(res.status));

            handlers[kind](form, await res.json());
        } catch (err) {
            toast(T.failed);
        } finally {
            delete form.dataset.busy;
        }
    });

    /* ---------- Páginas novas: filtros, tema e foto ---------- */
    $$('[data-autosubmit]').forEach((el) => el.addEventListener('change', () => el.form && el.form.submit()));

    $$('[data-autosubmit-delay]').forEach((el) => {
        let timer;
        el.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => el.form && el.form.submit(), 450);
        });
    });

    /* ---------- Foto de perfil: escolhe, reduz (512 px) e mostra a prévia ---------- */
    const shrinkImage = (file, side = 512) => new Promise((resolve) => {
        if (!/^image\/(png|jpe?g|webp)$/i.test(file.type)) return resolve(null);
        const url = URL.createObjectURL(file);
        const img = new Image();
        img.onload = () => {
            try {
                const size = Math.min(img.naturalWidth, img.naturalHeight);
                const canvas = document.createElement('canvas');
                canvas.width = canvas.height = Math.min(side, size);
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, (img.naturalWidth - size) / 2, (img.naturalHeight - size) / 2, size, size, 0, 0, canvas.width, canvas.height);
                canvas.toBlob((blob) => {
                    URL.revokeObjectURL(url);
                    resolve(blob ? new File([blob], 'avatar.jpg', { type: 'image/jpeg' }) : null);
                }, 'image/jpeg', 0.88);
            } catch (e) { URL.revokeObjectURL(url); resolve(null); }
        };
        img.onerror = () => { URL.revokeObjectURL(url); resolve(null); };
        img.src = url;
    });

    $$('[data-file-name]').forEach((input) => {
        input.addEventListener('change', async () => {
            const out = $(input.dataset.fileName);
            let file = input.files && input.files[0];
            if (file && 'DataTransfer' in window) {
                const small = await shrinkImage(file);
                if (small) {
                    const dt = new DataTransfer();
                    dt.items.add(small);
                    input.files = dt.files;
                    file = small;
                }
            }
            if (out) out.textContent = file ? file.name : '';

            const preview = $('.photo-row .avatar');
            if (preview && file) {
                preview.innerHTML = '';
                const img = document.createElement('img');
                img.alt = '';
                img.src = URL.createObjectURL(file);
                preview.appendChild(img);
            }
        });
    });
})();
