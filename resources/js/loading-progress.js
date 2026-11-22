(() => {
    let progress = document.getElementById('global-progress');
    if (!progress) {
        progress = document.createElement('div');
        progress.id = 'global-progress';
        progress.setAttribute('role', 'progressbar');
        progress.setAttribute('aria-label', 'Chargement');
        progress.setAttribute('aria-valuemin', '0');
        progress.setAttribute('aria-valuemax', '100');
        progress.setAttribute('aria-valuenow', '0');
        progress.style.cssText = 'position:fixed;inset:0 0 auto 0;z-index:100000;height:3px;transform:scaleX(0);transform-origin:left;opacity:0;pointer-events:none;background:linear-gradient(90deg,#38bdf8,#818cf8,#fb923c);box-shadow:0 0 14px rgba(129,140,248,.7);transition:transform 180ms ease,opacity 120ms ease';
        (document.body || document.documentElement).appendChild(progress);
    }

    let active = 0;
    let value = 0.08;
    let timer = null;

    function render() {
        progress.style.opacity = active > 0 ? '1' : '0';
        progress.style.transform = `scaleX(${Math.min(value, 0.92)})`;
        progress.setAttribute('aria-valuenow', String(Math.round(Math.min(value, 1) * 100)));
    }

    function begin() {
        active += 1;
        value = Math.max(value, 0.12);
        render();
        clearInterval(timer);
        timer = setInterval(() => {
            if (active > 0) {
                value = Math.min(0.92, value + (1 - value) * 0.08);
                render();
            }
        }, 180);
    }

    function end() {
        active = Math.max(0, active - 1);
        if (active !== 0) return;
        clearInterval(timer);
        value = 1;
        render();
        setTimeout(() => {
            value = 0;
            render();
        }, 220);
    }

    begin();
    window.addEventListener('load', end, { once: true });

    const nativeFetch = window.fetch?.bind(window);
    if (nativeFetch) {
        window.fetch = (...args) => {
            begin();
            return nativeFetch(...args).finally(end);
        };
    }

    document.addEventListener('submit', (event) => {
        if (!event.defaultPrevented && event.target instanceof HTMLFormElement) begin();
    }, true);

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const url = new URL(link.href, window.location.href);
        if (url.origin === window.location.origin && !link.hasAttribute('download') && link.target !== '_blank' && url.href !== window.location.href) begin();
    }, true);

    window.addEventListener('pageshow', () => {
        end();
        value = 0;
        render();
    });
})();
