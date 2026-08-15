@once
<div id="bmb-toasts" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2 items-end pointer-events-none"></div>
<script>
(function () {
    if (window.BMB && window.BMB.__loaded) return;
    const container = document.getElementById('bmb-toasts');
    const ICONS = { success: 'check_circle', error: 'error', warning: 'warning', info: 'info' };
    const TONES = {
        success: 'bg-primary/10 border-primary/20 text-primary',
        error:   'bg-error-container/40 border-error/30 text-error',
        warning: 'bg-tertiary/10 border-tertiary/30 text-tertiary',
        info:    'bg-surface-container-high border-outline-variant/40 text-on-surface'
    };
    function toast(message, type, timeout) {
        type = type || 'success';
        timeout = (timeout === undefined) ? 4500 : timeout;
        if (!container) return null;
        const el = document.createElement('div');
        el.className = 'pointer-events-auto flex items-center gap-2 rounded-xl border px-4 py-3 text-sm font-bold shadow-lg max-w-sm transition-all duration-300 translate-x-6 opacity-0 ' + (TONES[type] || TONES.info);
        el.innerHTML = '<span class="material-symbols-outlined text-base">' + (ICONS[type] || ICONS.info) + '</span><span class="bmb-toast-msg"></span>';
        el.querySelector('.bmb-toast-msg').textContent = message;
        container.appendChild(el);
        requestAnimationFrame(function () { el.classList.remove('translate-x-6', 'opacity-0'); });
        const dismiss = function () {
            el.classList.add('translate-x-6', 'opacity-0');
            setTimeout(function () { el.remove(); }, 300);
        };
        el.addEventListener('click', dismiss);
        if (timeout) setTimeout(dismiss, timeout);
        return el;
    }
    async function jsonFetch(url, options) {
        options = options || {};
        options.headers = Object.assign({ 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, options.headers || {});
        const res = await fetch(url, options);
        const ct = res.headers.get('content-type') || '';
        const data = ct.indexOf('application/json') !== -1 ? await res.json() : null;
        if (!res.ok) {
            const message = (data && (data.message || data.__error)) || ('Request failed (' + res.status + ')');
            const err = new Error(message); err.payload = data; err.status = res.status; throw err;
        }
        return data;
    }
    window.BMB = { __loaded: true, toast: toast, jsonFetch: jsonFetch };
})();
</script>
@endonce
