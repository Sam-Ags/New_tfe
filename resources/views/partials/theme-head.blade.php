<script>
    (() => {
        const savedTheme = localStorage.getItem('smartcity-theme');
        const theme = savedTheme === 'dark' ? 'dark' : 'light';
        document.documentElement.classList.add('smartcity-loading');
        document.documentElement.dataset.theme = theme;
        document.documentElement.classList.toggle('dark', theme === 'dark');
        document.documentElement.classList.toggle('light', theme !== 'dark');
    })();
</script>
<link rel="preload" as="image" href="{{ asset('images/smart-city-incidents-logo.png') }}">
<style>
    :root {
        color-scheme: light;
    }

    html.smartcity-loading body {
        opacity: 0;
    }

    html.smartcity-loading::before {
        content: "";
        position: fixed;
        inset: 0;
        z-index: 2147483647;
        background: #F4F8FB url("{{ asset('images/smart-city-incidents-logo.png') }}") center / 6rem auto no-repeat;
    }

    html[data-theme="dark"].smartcity-loading::before {
        background-color: #08131d;
    }

    html[data-theme="dark"] {
        --ink: #eaf4f8;
        --muted: #9db4c2;
        --line: #365568;
        --surface: #101f2b;
        --brand: #5fd4df;
        --brand-dark: #8eeaf0;
        color-scheme: dark;
    }

    .theme-toggle {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.75rem;
        height: 2.75rem;
        margin-top: 0;
        min-width: 2.75rem;
        min-height: 2.75rem;
        border: 1px solid #BCCCDC;
        border-radius: 999px;
        padding: 0;
        color: #005F73;
        background: #EAF3F8;
        font: inherit;
        cursor: pointer;
        transition: background .16s ease, color .16s ease, box-shadow .16s ease;
    }

    .theme-toggle:hover {
        color: #005F73;
        background: #fff;
        box-shadow: 0 1px 4px rgba(16, 42, 67, .12);
    }

    .theme-toggle .material-symbols-outlined {
        font-size: 1.35rem;
    }

    .theme-toggle [data-theme-toggle-label] {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
    }

    .material-symbols-outlined {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 1em;
        min-width: 1em;
        max-width: 1em;
        overflow: hidden;
        white-space: nowrap;
        direction: ltr;
        font-family: 'Material Symbols Outlined' !important;
        font-style: normal;
        font-weight: normal;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        word-wrap: normal;
        font-feature-settings: 'liga';
        -webkit-font-feature-settings: 'liga';
        -webkit-font-smoothing: antialiased;
    }

    .material-symbols-outlined[data-icon-svg="1"] {
        font-family: inherit !important;
        font-feature-settings: normal;
        -webkit-font-feature-settings: normal;
    }

    .material-symbols-outlined[data-icon-svg="1"] svg {
        width: 1em;
        height: 1em;
        display: block;
        fill: none;
        stroke: currentColor;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-width: 2;
    }

    html[data-theme="dark"] body,
    html[data-theme="dark"] .bg-surface {
        background-color: #08131d !important;
        color: #eaf4f8 !important;
    }

    html[data-theme="dark"] body {
        background-image: linear-gradient(135deg, rgba(8, 19, 29, .96), rgba(13, 35, 48, .94)) !important;
    }

    html[data-theme="dark"] .bg-surface-container-lowest,
    html[data-theme="dark"] .bg-white {
        background-color: #101f2b !important;
    }

    html[data-theme="dark"] .bg-surface-container-low {
        background-color: #152937 !important;
    }

    html[data-theme="dark"] .bg-surface\/95 {
        background-color: rgba(8, 19, 29, .95) !important;
    }

    html[data-theme="dark"] .bg-surface-container-low\/60,
    html[data-theme="dark"] .bg-surface-container-low\/80 {
        background-color: rgba(21, 41, 55, .86) !important;
    }

    html[data-theme="dark"] .bg-surface-container {
        background-color: #203443 !important;
    }

    html[data-theme="dark"] .bg-surface-variant {
        background-color: #294456 !important;
    }

    html[data-theme="dark"] .text-on-surface {
        color: #eaf4f8 !important;
    }

    html[data-theme="dark"] .text-on-surface-variant,
    html[data-theme="dark"] .text-secondary {
        color: #b7cad4 !important;
    }

    html[data-theme="dark"] .text-primary {
        color: #5fd4df !important;
    }

    html[data-theme="dark"] .border-outline,
    html[data-theme="dark"] .border-outline-variant {
        border-color: #365568 !important;
    }

    html[data-theme="dark"] input,
    html[data-theme="dark"] select,
    html[data-theme="dark"] textarea {
        border-color: #365568 !important;
        color: #eaf4f8 !important;
        background-color: #0d1b26 !important;
    }

    html[data-theme="dark"] input::placeholder,
    html[data-theme="dark"] textarea::placeholder {
        color: #7f98a8 !important;
    }

    html[data-theme="dark"] .bg-red-50 {
        background-color: #3a1719 !important;
    }

    html[data-theme="dark"] .bg-emerald-50 {
        background-color: #0d3025 !important;
    }

    html[data-theme="dark"] .bg-amber-50 {
        background-color: #352714 !important;
    }

    html[data-theme="dark"] .text-emerald-700,
    html[data-theme="dark"] .text-emerald-800 {
        color: #93e6c0 !important;
    }

    html[data-theme="dark"] .text-amber-800,
    html[data-theme="dark"] .text-amber-900 {
        color: #f4cf86 !important;
    }

    html[data-theme="dark"] .text-error {
        color: #ffb4a9 !important;
    }

    html[data-theme="dark"] .theme-toggle {
        border-color: #365568;
        color: #5fd4df;
        background: #152937;
    }

    html[data-theme="dark"] .theme-toggle:hover {
        color: #5fd4df;
        background: #0d1b26;
        box-shadow: 0 1px 5px rgba(0, 0, 0, .32);
    }

    @media (max-width: 640px) {
        .theme-toggle {
            width: 2.5rem;
            height: 2.5rem;
            min-width: 2.5rem;
            min-height: 2.5rem;
        }
    }

    html:not(.material-icons-ready) .material-symbols-outlined {
        color: transparent !important;
        text-shadow: none !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const key = 'smartcity-theme';
        const root = document.documentElement;
        const choiceButtons = Array.from(document.querySelectorAll('[data-theme-choice]'));
        const toggleButtons = Array.from(document.querySelectorAll('[data-theme-toggle]'));
        const makeIcon = (body) => `<svg aria-hidden="true" viewBox="0 0 24 24">${body}</svg>`;
        const iconMap = {
            account_balance: makeIcon('<path d="M3 21h18"/><path d="M5 21V10"/><path d="M19 21V10"/><path d="M12 3 3 8h18Z"/><path d="M9 21v-8"/><path d="M15 21v-8"/>'),
            account_circle: makeIcon('<circle cx="12" cy="12" r="9"/><circle cx="12" cy="10" r="3"/><path d="M6.8 18a6 6 0 0 1 10.4 0"/>'),
            add_a_photo: makeIcon('<path d="M4 8h3l2-3h6l2 3h3v11H4Z"/><circle cx="12" cy="13" r="3"/><path d="M6 4v4"/><path d="M4 6h4"/>'),
            add_location_alt: makeIcon('<path d="M12 21s7-5.2 7-11a7 7 0 0 0-14 0c0 5.8 7 11 7 11Z"/><path d="M12 7v6"/><path d="M9 10h6"/>'),
            arrow_forward: makeIcon('<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>'),
            badge: makeIcon('<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 7h6"/><circle cx="12" cy="12" r="2"/><path d="M8.5 17a4 4 0 0 1 7 0"/>'),
            bar_chart: makeIcon('<path d="M4 20h16"/><path d="M7 16V8"/><path d="M12 16V4"/><path d="M17 16v-6"/>'),
            calendar_month: makeIcon('<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4"/><path d="M16 3v4"/><path d="M4 10h16"/>'),
            call: makeIcon('<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.7.6 2.5a2 2 0 0 1-.5 2.1L8 9.5a16 16 0 0 0 6.5 6.5l1.2-1.2a2 2 0 0 1 2.1-.5c.8.3 1.6.5 2.5.6a2 2 0 0 1 1.7 2Z"/>'),
            category: makeIcon('<rect x="4" y="4" width="7" height="7" rx="1"/><rect x="13" y="4" width="7" height="7" rx="1"/><rect x="4" y="13" width="7" height="7" rx="1"/><rect x="13" y="13" width="7" height="7" rx="1"/>'),
            check_circle: makeIcon('<circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>'),
            close: makeIcon('<path d="M18 6 6 18"/><path d="m6 6 12 12"/>'),
            dashboard: makeIcon('<rect x="4" y="4" width="7" height="7" rx="1"/><rect x="13" y="4" width="7" height="7" rx="1"/><rect x="4" y="13" width="7" height="7" rx="1"/><rect x="13" y="13" width="7" height="7" rx="1"/>'),
            dark_mode: makeIcon('<path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>'),
            edit: makeIcon('<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>'),
            engineering: makeIcon('<path d="M10 3h4l1 3 3 1v4l-3 1-1 3h-4l-1-3-3-1V7l3-1Z"/><circle cx="12" cy="9" r="2"/><path d="M6 21a6 6 0 0 1 12 0"/>'),
            folder_open: makeIcon('<path d="M3 7h6l2 2h10v3"/><path d="M3 7v12h16l2-8H7l-2 8"/>'),
            gps_fixed: makeIcon('<circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="8"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M2 12h2"/><path d="M20 12h2"/>'),
            history: makeIcon('<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/><path d="M12 7v5l3 2"/>'),
            home: makeIcon('<path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>'),
            image: makeIcon('<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8" cy="10" r="2"/><path d="m21 15-5-5L5 19"/>'),
            light_mode: makeIcon('<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.9 4.9 1.4 1.4"/><path d="m17.7 17.7 1.4 1.4"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m4.9 19.1 1.4-1.4"/><path d="m17.7 6.3 1.4-1.4"/>'),
            location_on: makeIcon('<path d="M12 21s7-5.2 7-11a7 7 0 0 0-14 0c0 5.8 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>'),
            lock: makeIcon('<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>'),
            logout: makeIcon('<path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 3v18"/>'),
            mail: makeIcon('<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>'),
            map: makeIcon('<path d="M9 18 3 21V6l6-3 6 3 6-3v15l-6 3Z"/><path d="M9 3v15"/><path d="M15 6v15"/>'),
            menu: makeIcon('<path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/>'),
            my_location: makeIcon('<circle cx="12" cy="12" r="3"/><path d="M12 2v3"/><path d="M12 19v3"/><path d="M2 12h3"/><path d="M19 12h3"/><circle cx="12" cy="12" r="8"/>'),
            near_me: makeIcon('<path d="M12 2 4 21l8-4 8 4Z"/>'),
            notifications: makeIcon('<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>'),
            notifications_active: makeIcon('<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/><path d="M4 4 2.5 2.5"/><path d="M20 4l1.5-1.5"/>'),
            open_in_new: makeIcon('<path d="M14 3h7v7"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/>'),
            person: makeIcon('<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>'),
            person_add: makeIcon('<circle cx="10" cy="8" r="4"/><path d="M3 21a7 7 0 0 1 14 0"/><path d="M19 8v6"/><path d="M16 11h6"/>'),
            photo_camera: makeIcon('<path d="M4 8h3l2-3h6l2 3h3v11H4Z"/><circle cx="12" cy="13" r="3"/>'),
            photo_library: makeIcon('<rect x="5" y="5" width="14" height="14" rx="2"/><path d="M3 7v14h14"/><circle cx="10" cy="10" r="1.5"/><path d="m19 15-4-4-7 8"/>'),
            picture_as_pdf: makeIcon('<path d="M6 2h9l5 5v15H6Z"/><path d="M14 2v6h6"/><path d="M8 16h1.5a1.5 1.5 0 0 0 0-3H8v5"/><path d="M13 13v5"/><path d="M16 13h3"/><path d="M16 16h2"/>'),
            play_arrow: makeIcon('<path d="M8 5v14l11-7Z"/>'),
            report_problem: makeIcon('<path d="M12 3 2 21h20Z"/><path d="M12 9v5"/><path d="M12 17h.01"/>'),
            save: makeIcon('<path d="M5 3h14l2 2v16H3V5Z"/><path d="M7 3v6h10V3"/><path d="M7 21v-8h10v8"/>'),
            schedule: makeIcon('<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'),
            security: makeIcon('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/>'),
            send: makeIcon('<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>'),
            speed: makeIcon('<path d="M21 14a9 9 0 1 0-18 0"/><path d="M12 14l5-5"/><path d="M12 14h.01"/>'),
            update: makeIcon('<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/><path d="M12 7v5l4 2"/>'),
            verified_user: makeIcon('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/>'),
            visibility: makeIcon('<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>'),
            visibility_off: makeIcon('<path d="m3 3 18 18"/><path d="M10.6 10.6A3 3 0 0 0 13.4 13.4"/><path d="M9.9 5.3A10.7 10.7 0 0 1 12 5c6 0 10 7 10 7a17.3 17.3 0 0 1-3.2 4.2"/><path d="M6.1 6.1A17.3 17.3 0 0 0 2 12s4 7 10 7a10.7 10.7 0 0 0 4.4-1"/>'),
            warning: makeIcon('<path d="M12 3 2 21h20Z"/><path d="M12 9v5"/><path d="M12 17h.01"/>'),
            wc: makeIcon('<circle cx="7" cy="5" r="2"/><circle cx="17" cy="5" r="2"/><path d="M5 21v-7H3l2-6h4l2 6H9v7"/><path d="M15 21v-6h-2l2-7h4l2 7h-2v6"/>'),
        };

        function iconNameFrom(element) {
            const directText = Array.from(element.childNodes)
                .filter((node) => node.nodeType === Node.TEXT_NODE)
                .map((node) => node.textContent)
                .join('')
                .trim();
            return directText || element.dataset.iconName || element.textContent.trim();
        }

        function replaceMaterialIcon(element) {
            if (!element) return;
            const name = iconNameFrom(element);
            const markup = iconMap[name] || iconMap.report_problem;
            if (!markup) return;
            if (element.dataset.iconSvg === '1' && element.dataset.iconName === name) return;
            element.dataset.iconName = name;
            element.dataset.iconSvg = '1';
            element.innerHTML = markup;
        }

        function replaceMaterialIcons(scope = document) {
            scope.querySelectorAll?.('.material-symbols-outlined').forEach(replaceMaterialIcon);
        }

        function applyTheme(theme) {
            const nextTheme = theme === 'dark' ? 'dark' : 'light';
            localStorage.setItem(key, nextTheme);
            root.dataset.theme = nextTheme;
            root.classList.toggle('dark', nextTheme === 'dark');
            root.classList.toggle('light', nextTheme !== 'dark');
            choiceButtons.forEach((button) => {
                button.setAttribute('aria-pressed', String(button.dataset.themeChoice === nextTheme));
            });
            toggleButtons.forEach((button) => {
                const nextMode = nextTheme === 'dark' ? 'light' : 'dark';
                const label = button.querySelector('[data-theme-toggle-label]');
                const icon = button.querySelector('[data-theme-toggle-icon]');

                button.setAttribute('aria-pressed', String(nextTheme === 'dark'));
                button.setAttribute('aria-label', nextMode === 'dark' ? 'Activer le mode sombre' : 'Activer le mode clair');
                if (label) {
                    label.textContent = nextMode === 'dark' ? 'Sombre' : 'Clair';
                }
                if (icon) {
                    icon.textContent = nextMode === 'dark' ? 'dark_mode' : 'light_mode';
                    replaceMaterialIcon(icon);
                }
            });
        }

        choiceButtons.forEach((button) => {
            button.addEventListener('click', () => applyTheme(button.dataset.themeChoice));
        });
        toggleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark');
            });
        });

        applyTheme(localStorage.getItem(key) || root.dataset.theme || 'light');
        replaceMaterialIcons();
        root.classList.add('material-icons-ready');

        const iconObserver = new MutationObserver((records) => {
            records.forEach((record) => {
                const target = record.target.nodeType === Node.ELEMENT_NODE ? record.target : record.target.parentElement;
                if (!target) return;
                if (target.matches?.('.material-symbols-outlined')) {
                    replaceMaterialIcon(target);
                    return;
                }
                replaceMaterialIcons(target);
            });
        });
        iconObserver.observe(document.body, { childList: true, characterData: true, subtree: true });

        requestAnimationFrame(() => {
            root.classList.remove('smartcity-loading');
        });

        function markMaterialIconsReady() {
            root.classList.add('material-icons-ready');
        }

        function materialIconsAreReady() {
            return document.fonts?.check?.('24px "Material Symbols Outlined"', 'dark_mode') ?? false;
        }

        if (!document.fonts?.load) {
            root.classList.add('material-icons-ready');
            return;
        }

        if (materialIconsAreReady()) {
            markMaterialIconsReady();
            return;
        }

        document.fonts.load('24px "Material Symbols Outlined"', 'dark_mode')
            .then(() => {
                if (materialIconsAreReady()) {
                    markMaterialIconsReady();
                }
            })
            .catch(() => {});

        document.fonts.ready.then(() => {
            if (materialIconsAreReady()) {
                markMaterialIconsReady();
            }
        });

        document.fonts.addEventListener?.('loadingdone', () => {
            if (materialIconsAreReady()) {
                markMaterialIconsReady();
            }
        });
    });
</script>
