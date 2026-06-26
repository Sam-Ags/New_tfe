<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ auth()->user()->isAgent() ? 'Smart City Agent - Missions' : 'Smart City Admin - Incidents' }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=optional" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#005F73',
                        'primary-container': '#0A9396',
                        surface: '#F4F8FB',
                        'surface-container-lowest': '#FFFFFF',
                        'surface-container-low': '#EAF3F8',
                        'surface-container': '#DDEAF2',
                        'surface-variant': '#CCE3EC',
                        'on-surface': '#102A43',
                        'on-surface-variant': '#334E68',
                        secondary: '#486581',
                        outline: '#829AB1',
                        'outline-variant': '#BCCCDC',
                        error: '#B42318',
                        'error-container': '#FEE4E2',
                    },
                    fontFamily: {
                        sans: ['Public Sans', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        [data-view][hidden],
        [data-stats-detail][hidden] {
            display: none !important;
        }

        #map {
            width: 100%;
            height: clamp(380px, calc(100vh - 19rem), 680px);
            min-height: 380px;
            border-radius: .75rem;
            border: 1px solid #bfc8c8;
            overflow: hidden;
        }

        .leaflet-popup-content {
            min-width: 260px;
            font-family: "Public Sans", system-ui, sans-serif;
        }

        .popup-dashboard {
            margin-top: 10px;
            border: 0;
            border-radius: 4px;
            padding: 8px 10px;
            color: #fff;
            background: #005F73;
            font-weight: 700;
            cursor: pointer;
        }

        .popup-dashboard:disabled {
            opacity: .65;
            cursor: wait;
        }

        .flag-marker {
            position: relative;
            width: 24px;
            height: 30px;
        }

        .flag-marker::before {
            content: "";
            position: absolute;
            left: 3px;
            top: 3px;
            width: 4px;
            height: 26px;
            background: #7f1d1d;
            border-radius: 2px;
        }

        .flag-marker::after {
            content: "";
            position: absolute;
            left: 7px;
            top: 2px;
            width: 17px;
            height: 14px;
            background: #e11d48;
            clip-path: polygon(0 0, 100% 0, 76% 50%, 100% 100%, 0 100%);
            box-shadow: 0 2px 5px rgba(0, 0, 0, .25);
        }

        .nav-link.active {
            background: #F4A261;
            color: #102A43;
        }

        .admin-sidebar {
            background: linear-gradient(180deg, #073B4C 0%, #005F73 100%);
            border-color: rgba(255, 255, 255, .18);
            color: #fff;
        }

        .admin-sidebar .text-on-surface,
        .admin-sidebar .text-secondary {
            color: rgba(255, 255, 255, .82) !important;
        }

        .admin-sidebar a,
        .admin-sidebar button {
            color: rgba(255, 255, 255, .84) !important;
        }

        .admin-sidebar a:hover,
        .admin-sidebar button:hover {
            background: rgba(255, 255, 255, .14) !important;
            color: #fff;
        }

        .admin-sidebar .nav-link.active {
            background: #F4A261 !important;
            color: #102A43 !important;
        }

        .admin-sidebar .nav-link.active .material-symbols-outlined {
            color: #102A43 !important;
        }

        .admin-sidebar form {
            border-color: rgba(255, 255, 255, .18);
        }

        body.agent-page {
            background: #EEF7F2;
        }

        .agent-shell {
            background:
                linear-gradient(180deg, rgba(238, 247, 242, .96), rgba(244, 248, 251, 1));
        }

        .agent-shell .admin-sidebar {
            background: linear-gradient(180deg, #12372E 0%, #0F766E 100%);
        }

        .agent-shell .admin-main {
            background: #EEF7F2;
        }

        .agent-shell .admin-main header {
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(14px);
        }

        .agent-shell .nav-link.active {
            background: #F6BD60 !important;
            color: #102A43 !important;
        }

        .agent-shell .nav-link.active .material-symbols-outlined {
            color: #102A43 !important;
        }

        .agent-shell [data-view="admin"] > .mb-6 {
            display: none;
        }

        .agent-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr);
            gap: 1.25rem;
            overflow: hidden;
            border-radius: 1rem;
            padding: 1.5rem;
            color: #FFFFFF;
            background: linear-gradient(135deg, #12372E 0%, #0F766E 58%, #F4A261 100%);
            box-shadow: 0 18px 42px rgba(15, 118, 110, .22);
        }

        .agent-hero-panel {
            border: 1px solid rgba(255, 255, 255, .20);
            border-radius: .875rem;
            background: rgba(255, 255, 255, .12);
            padding: 1rem;
            backdrop-filter: blur(10px);
        }

        .agent-hero-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }

        .agent-hero-stat {
            border: 1px solid rgba(255, 255, 255, .20);
            border-radius: .875rem;
            background: rgba(255, 255, 255, .13);
            padding: 1rem;
        }

        .agent-shell #admin > .rounded-xl {
            border: 0;
            background: transparent;
            box-shadow: none;
        }

        .agent-shell #admin > .rounded-xl > .flex {
            margin-bottom: 1rem;
            border: 1px solid #B8D8CF;
            border-radius: 1rem;
            background: #FFFFFF;
        }

        .agent-shell #admin > .rounded-xl > .grid {
            padding: 0;
        }

        .agent-mission-card {
            border-color: #B8D8CF !important;
            border-left: 6px solid #0F766E;
            box-shadow: 0 12px 28px rgba(18, 55, 46, .10);
        }

        .agent-mission-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 34px rgba(18, 55, 46, .14);
        }

        .agent-proof-form {
            border-color: #7DD3C7 !important;
            background: #ECFDF5 !important;
        }

        .sidebar-backdrop {
            display: none;
        }

        .profile-lightbox[hidden] {
            display: none;
        }

        .admin-shell {
            min-height: 100vh;
        }

        .admin-main {
            min-height: 100vh;
            width: calc(100% - 16rem);
            margin-left: 16rem;
            min-width: 0;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 1.5rem;
        }

        .summary-grid.view-summary {
            margin-top: 2rem;
            margin-bottom: 0;
        }

        .admin-two-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 1023px) {
            .admin-shell {
                display: block;
                overflow-x: hidden;
            }

            .admin-sidebar {
                position: relative !important;
                left: auto !important;
                top: auto !important;
                width: 100% !important;
                height: auto !important;
                min-height: auto !important;
                padding: 1rem !important;
                border-right: 0;
                border-bottom: 1px solid rgba(255, 255, 255, .18);
            }

            .admin-sidebar .mb-8 {
                margin-bottom: 1rem !important;
            }

            .admin-sidebar .mb-10 {
                margin-bottom: 1rem !important;
            }

            .admin-sidebar nav {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .5rem;
            }

            .admin-sidebar form {
                margin-top: 1rem;
                padding-top: 1rem;
            }

            .admin-shell .admin-sidebar {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                z-index: 50 !important;
                width: min(82vw, 20rem) !important;
                height: 100vh !important;
                min-height: 100vh !important;
                padding: 1.25rem !important;
                border-right: 1px solid rgba(255, 255, 255, .18);
                border-bottom: 0;
                transform: translateX(-105%);
                transition: transform .22s ease;
            }

            .admin-shell.sidebar-open .admin-sidebar {
                transform: translateX(0);
            }

            .admin-shell .admin-sidebar nav {
                display: flex !important;
                flex-direction: column;
            }

            .admin-shell .admin-sidebar nav form {
                margin-top: 0;
                padding-top: 0;
            }

            .admin-shell .sidebar-backdrop {
                position: fixed;
                inset: 0;
                z-index: 40;
                display: none;
                background: rgba(16, 42, 67, .48);
                backdrop-filter: blur(2px);
            }

            .admin-shell.sidebar-open .sidebar-backdrop {
                display: block;
            }

            .admin-main {
                width: 100% !important;
                margin-left: 0 !important;
            }

            .admin-main header {
                position: static !important;
                padding: 1rem !important;
                align-items: flex-start;
                gap: 1rem;
            }

            .admin-main > div {
                padding: 1rem !important;
            }

            .summary-grid,
            .admin-two-grid,
            .agent-hero {
                grid-template-columns: 1fr;
            }

            .agent-hero-stats {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 768px) and (max-width: 1279px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
    @include('partials.theme-head')
</head>
<body class="min-h-screen bg-surface font-sans text-on-surface {{ auth()->user()->isAgent() ? 'agent-page' : '' }}">
    <div class="admin-shell {{ auth()->user()->isAgent() ? 'agent-shell' : '' }}" id="app-shell">
        <aside class="admin-sidebar fixed left-0 top-0 z-30 flex h-screen w-64 flex-col border-r border-outline-variant bg-surface-container-low p-6">
            <div class="mb-8 flex items-center gap-3">
                <span class="grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-full bg-white shadow-sm ring-1 ring-outline-variant">
                    <img class="h-16 w-16 object-contain" src="{{ asset('images/smart-city-incidents-logo-256.png') }}" alt="Smart City Incidents">
                </span>
                <span class="text-sm font-bold leading-tight text-primary">SmartCity<br>Incident</span>
            </div>

            <div class="mb-10 flex items-center gap-3">
                <div class="flex h-11 w-11 overflow-hidden rounded-lg bg-primary text-lg font-bold text-white">
                    @if (auth()->user()->isAgent() && auth()->user()->profile_photo_path)
                        <img class="h-full w-full object-cover" src="{{ asset(auth()->user()->profile_photo_path) }}" alt="Photo de {{ auth()->user()->name }}">
                    @else
                        <img class="h-full w-full rounded-full bg-white object-contain p-1" src="{{ asset('images/smart-city-incidents-logo-256.png') }}" alt="">
                    @endif
                </div>
                <div>
                    <p class="font-semibold text-on-surface">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-secondary">{{ auth()->user()->commune?->name ?: ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>

            <nav class="flex flex-1 flex-col gap-2">
                <a class="nav-link flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="#admin" data-nav-view="admin">
                    <span class="material-symbols-outlined">{{ auth()->user()->isAgent() ? 'engineering' : 'dashboard' }}</span>
                    {{ auth()->user()->isAgent() ? 'Mes missions' : 'Tableau de bord' }}
                </a>
                @unless (auth()->user()->isAgent())
                    <a class="nav-link flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="#carte" data-nav-view="carte">
                        <span class="material-symbols-outlined">map</span>
                        Carte
                    </a>
                    <a class="nav-link flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="#incidents" data-nav-view="incidents">
                        <span class="material-symbols-outlined">report_problem</span>
                        Gestion incidents
                    </a>
                @endunless
                <a class="nav-link flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="#historique" data-nav-view="historique">
                    <span class="material-symbols-outlined">history</span>
                    Historique
                </a>
                @if (auth()->user()->isAgent())
                    <a class="nav-link flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="#profil" data-nav-view="profil">
                        <span class="material-symbols-outlined">badge</span>
                        Mon profil
                    </a>
                @endif
                @if (auth()->user()->isAdmin())
                    <a class="nav-link flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="#stats" data-nav-view="stats">
                        <span class="material-symbols-outlined">bar_chart</span>
                        Statistiques
                    </a>
                @endif
                @if (auth()->user()->isAdmin())
                    <a class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="{{ route('authorities.create') }}">
                        <span class="material-symbols-outlined">account_balance</span>
                        Gestion autorités
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-red-50 hover:text-error" type="submit">
                        <span class="material-symbols-outlined">logout</span>
                        Déconnexion
                    </button>
                </form>
            </nav>
        </aside>
        <button class="sidebar-backdrop" id="sidebar-backdrop" type="button" aria-label="Fermer le menu"></button>

        <main class="admin-main">
            <header class="sticky top-0 z-20 flex items-center justify-between border-b border-outline-variant bg-surface px-8 py-4">
                <div class="flex items-start gap-3">
                    <button class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary text-white lg:hidden" id="sidebar-toggle" type="button" aria-controls="app-shell" aria-expanded="false">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-primary">{{ auth()->user()->isAgent() ? 'Espace agent terrain' : 'Smart City Admin' }}</h1>
                        <p class="text-sm text-secondary">{{ auth()->user()->isAgent() ? 'Suivi des missions affectées et preuves d’intervention' : 'Gestion opérationnelle des plaintes urbaines' }}</p>
                    </div>
                </div>
                <div class="relative flex items-center gap-3">
                    @include('partials.theme-toggle')
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-outline-variant bg-surface-container-low text-primary shadow-sm transition hover:bg-red-50 hover:text-error" type="submit" aria-label="Déconnexion">
                            <span class="material-symbols-outlined">logout</span>
                        </button>
                    </form>
                    <button class="relative inline-flex h-12 w-12 items-center justify-center rounded-full border border-outline-variant bg-surface-container-low text-primary shadow-sm transition hover:bg-surface-container" id="notifications-toggle" type="button" aria-label="Afficher les notifications" aria-expanded="false">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="{{ $unreadNotificationsCount > 0 ? 'grid' : 'hidden' }} absolute -right-1 -top-1 h-6 min-w-6 place-items-center rounded-full bg-error px-1.5 text-xs font-bold text-white" id="notifications-badge">{{ $unreadNotificationsCount }}</span>
                    </button>
                    <div class="absolute right-0 top-14 z-40 w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-xl border border-outline-variant bg-white shadow-2xl" id="notifications-panel" hidden>
                        <div class="flex items-center justify-between gap-3 border-b border-outline-variant bg-surface-container-low px-4 py-3">
                            <div>
                                <h2 class="font-bold text-on-surface">Notifications</h2>
                                <p class="text-xs text-secondary"><span id="notifications-unread-text">{{ $unreadNotificationsCount }}</span> non lue<span id="notifications-unread-plural">{{ $unreadNotificationsCount > 1 ? 's' : '' }}</span></p>
                            </div>
                            <span class="material-symbols-outlined text-primary">notifications_active</span>
                        </div>
                        <div class="max-h-96 overflow-auto p-2">
                            @forelse ($notifications as $notification)
                                <button class="w-full rounded-lg p-3 text-left transition hover:bg-surface-container-low {{ $notification->read_at ? 'text-secondary' : 'bg-surface-container-low text-on-surface' }}" type="button" data-notification-item data-notification-id="{{ $notification->id }}" data-read-url="{{ route('notifications.read', $notification, false) }}" data-read="{{ $notification->read_at ? '1' : '0' }}">
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined mt-0.5 text-lg {{ $notification->read_at ? 'text-secondary' : 'text-primary' }}">notifications</span>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <strong class="text-sm">{{ $notification->title }}</strong>
                                                @unless ($notification->read_at)
                                                    <span class="h-2 w-2 rounded-full bg-error" data-unread-dot></span>
                                                @endunless
                                            </div>
                                            <p class="mt-1 text-sm leading-relaxed text-secondary">{{ $notification->message }}</p>
                                            <p class="mt-2 text-xs text-secondary">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </button>
                            @empty
                                <div class="p-5 text-center text-sm text-secondary">Aucune notification pour le moment.</div>
                            @endforelse
                        </div>
                    </div>
                    @if (auth()->user()->isAgent())
                        <button class="h-12 w-12 overflow-hidden rounded-full border-2 border-primary bg-surface-container shadow-sm" id="profile-photo-open" type="button" aria-label="Afficher la photo de profil">
                            @if (auth()->user()->profile_photo_path)
                                <img class="h-full w-full object-cover" src="{{ asset(auth()->user()->profile_photo_path) }}" alt="Photo de {{ auth()->user()->name }}">
                            @else
                                <span class="material-symbols-outlined grid h-full w-full place-items-center text-primary">account_circle</span>
                            @endif
                        </button>
                    @else
                        <span class="material-symbols-outlined text-secondary">account_circle</span>
                    @endif
                </div>
            </header>

            <div class="p-8">
                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 font-semibold text-emerald-800">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-error-container bg-red-50 px-4 py-3 text-error">
                        <strong>Corrige les champs suivants :</strong>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (auth()->user()->isAgent())
                    @php
                        $agentFirstName = explode(' ', trim(auth()->user()->name))[0] ?: auth()->user()->name;
                        $nextMission = $incidents->first();
                    @endphp
                    <section class="agent-hero mb-8">
                        <div class="flex flex-col justify-between gap-8">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-white/75">Interface agent terrain</p>
                                <h2 class="mt-3 text-3xl font-bold leading-tight md:text-4xl">Bonjour {{ $agentFirstName }}</h2>
                                <p class="mt-3 max-w-2xl text-base font-medium leading-7 text-white/80">
                                    Retrouvez vos missions affectées, démarrez les interventions et envoyez les preuves directement depuis le terrain.
                                </p>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div class="agent-hero-panel">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-white/70">Commune</p>
                                    <p class="mt-2 text-lg font-bold">{{ auth()->user()->commune?->name ?: 'Non définie' }}</p>
                                </div>
                                <div class="agent-hero-panel">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-white/70">Missions actives</p>
                                    <p class="mt-2 text-lg font-bold">{{ $incidents->count() }}</p>
                                </div>
                                <div class="agent-hero-panel">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-white/70">Historique</p>
                                    <p class="mt-2 text-lg font-bold">{{ $historyIncidents->count() }} traitée{{ $historyIncidents->count() > 1 ? 's' : '' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="agent-hero-panel">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-white/70">Priorité du moment</p>
                                    <h3 class="mt-1 text-xl font-bold">{{ $nextMission ? $nextMission->title : 'Aucune mission urgente' }}</h3>
                                </div>
                                <span class="material-symbols-outlined text-4xl text-white/80">engineering</span>
                            </div>
                            <div class="agent-hero-stats">
                                <div class="agent-hero-stat">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-white/70">À démarrer</p>
                                    <strong class="mt-2 block text-3xl">{{ $stats['pending'] }}</strong>
                                </div>
                                <div class="agent-hero-stat">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-white/70">En cours</p>
                                    <strong class="mt-2 block text-3xl">{{ $stats['progress'] }}</strong>
                                </div>
                                <div class="agent-hero-stat">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-white/70">En validation</p>
                                    <strong class="mt-2 block text-3xl">{{ $stats['validation'] }}</strong>
                                </div>
                                <div class="agent-hero-stat">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-white/70">Priorité élevée</p>
                                    <strong class="mt-2 block text-3xl">{{ $stats['high'] }}</strong>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                @if (auth()->user()->isAdmin())
                    <section class="summary-grid mb-8" id="summary-panel">
                        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Total incidents</p>
                            <strong class="mt-2 block text-3xl text-primary">{{ $stats['total'] }}</strong>
                        </div>
                        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">En attente</p>
                            <strong class="mt-2 block text-3xl text-on-surface">{{ $stats['pending'] }}</strong>
                        </div>
                        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">En cours</p>
                            <strong class="mt-2 block text-3xl text-on-surface">{{ $stats['progress'] }}</strong>
                        </div>
                        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Résolus</p>
                            <strong class="mt-2 block text-3xl text-on-surface">{{ $stats['resolved'] }}</strong>
                        </div>
                        <div class="rounded-xl bg-primary-container p-5 text-white shadow-sm">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-white/75">Priorité élevée</p>
                            <strong class="mt-2 block text-3xl">{{ $stats['high'] }}</strong>
                        </div>
                        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Taux résolution</p>
                            <strong class="mt-2 block text-3xl text-primary">{{ $performanceStats['resolutionRate'] }}%</strong>
                        </div>
                    </section>
                @endif

                <section data-view="admin" id="admin">
                    <div class="mb-6">
                        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-6 shadow-sm">
                            <h2 class="text-2xl font-semibold text-primary">{{ auth()->user()->isAgent() ? 'Mes missions terrain' : 'Espace administration' }}</h2>
                            <p class="mt-2 text-secondary">
                                @if (auth()->user()->isAdmin())
                                    Vous recevez les signalements de {{ auth()->user()->commune?->name ?: 'votre commune' }} et pouvez les affecter aux agents.
                                @else
                                    Vous consultez uniquement les plaintes qui vous sont affectées. Après intervention, envoyez une photo de preuve pour validation par l’administration.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-outline-variant bg-surface-container-lowest shadow-sm">
                        <div class="flex flex-col gap-4 border-b border-outline-variant bg-surface-container-low/60 p-5 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-2xl font-semibold text-on-surface">{{ auth()->user()->isAgent() ? 'Interventions à réaliser' : 'Tableau de bord administratif' }}</h2>
                                <p class="mt-1 text-sm text-secondary">
                                    {{ auth()->user()->isAgent() ? 'Interventions à traiter sur le terrain.' : 'Plaintes ajoutées depuis la carte par cet administrateur.' }}
                                </p>
                            </div>
                            <form method="GET" action="{{ route('incidents.index') }}#admin" class="grid grid-cols-1 gap-3 md:grid-cols-[180px_180px_auto_auto]">
                                <select class="rounded border-outline-variant text-sm" name="status" aria-label="Filtrer par statut">
                                    <option value="">Tous les statuts actifs</option>
                                    @foreach ($activeStatuses as $value => $label)
                                        <option value="{{ $value }}" @selected($activeFilters['status'] === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <select class="rounded border-outline-variant text-sm" name="category" aria-label="Filtrer par catégorie">
                                    <option value="">Toutes catégories</option>
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}" @selected($activeFilters['category'] === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="rounded bg-primary px-4 py-2 text-sm font-semibold text-white" type="submit">Filtrer</button>
                                <a class="rounded border border-primary px-4 py-2 text-center text-sm font-semibold text-primary" href="{{ route('incidents.index') }}#admin">Réinitialiser</a>
                            </form>
                        </div>

                        @if ($incidents->isEmpty())
                            <div class="p-8 text-center text-secondary">
                                {{ auth()->user()->isAgent() ? 'Aucune mission ne vous est affectée pour le moment.' : "Aucune plainte active dans le tableau de bord. Les plaintes résolues sont visibles dans l'historique." }}
                            </div>
                        @elseif (auth()->user()->isAgent())
                            <div class="grid gap-5 p-5">
                                @foreach ($incidents as $incident)
                                    @php
                                        $incidentPhotoPaths = $incident->photos
                                            ->pluck('path')
                                            ->prepend($incident->photo_path)
                                            ->filter()
                                            ->unique()
                                            ->values();
                                    @endphp
                                    <article class="agent-mission-card rounded-xl border border-outline-variant bg-white p-4 shadow-sm transition">
                                        <div class="grid gap-4 md:grid-cols-[140px_1fr]">
                                            <div class="h-36 overflow-hidden rounded-xl bg-surface-variant">
                                                @if ($incidentPhotoPaths->isNotEmpty())
                                                    <img class="h-full w-full object-cover" src="{{ asset($incidentPhotoPaths->first()) }}" alt="Photo de l'incident {{ $incident->id }}" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('grid');">
                                                    <div class="hidden h-full w-full place-items-center p-3 text-center text-sm font-semibold text-secondary">
                                                        Photo indisponible
                                                    </div>
                                                @else
                                                    <span class="material-symbols-outlined grid h-full w-full place-items-center text-5xl text-secondary">image</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="flex flex-wrap items-start justify-between gap-3">
                                                    <div>
                                                        <h3 class="text-xl font-bold text-on-surface">{{ $incident->title }}</h3>
                                                        <p class="mt-1 text-sm text-secondary">{{ $incident->categoryLabel() }} - {{ $incident->created_at->format('d/m/Y H:i') }}</p>
                                                    </div>
                                                    <span class="rounded-full {{ $incident->priority === 'elevee' ? 'bg-error-container text-error' : 'bg-surface-container text-primary' }} px-3 py-1 text-xs font-bold">{{ $incident->priorityLabel() }}</span>
                                                </div>
                                                <div class="mt-4 grid gap-2 text-sm text-secondary md:grid-cols-2">
                                                    <p><span class="font-semibold text-on-surface">Commune :</span> {{ $incident->commune?->name ?: 'Commune inconnue' }}</p>
                                                    <p><span class="font-semibold text-on-surface">Zone :</span> {{ $incident->district ?: 'Zone non précisée' }}</p>
                                                    <p><span class="font-semibold text-on-surface">Statut :</span> {{ $incident->statusLabel() }}</p>
                                                    <p><span class="font-semibold text-on-surface">Priorité :</span> {{ $incident->priorityLabel() }}</p>
                                                </div>
                                                @if ($incident->description)
                                                    <p class="mt-4 rounded-lg bg-surface-container-low p-3 text-sm text-on-surface-variant">{{ $incident->description }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-5 rounded-xl border border-outline-variant bg-surface-container-low p-4">
                                            <div class="mb-3 flex items-center justify-between gap-3">
                                                <h4 class="font-bold text-on-surface">Photos de l'incident</h4>
                                                <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-primary">{{ $incidentPhotoPaths->count() }} photo{{ $incidentPhotoPaths->count() > 1 ? 's' : '' }}</span>
                                            </div>
                                            @if ($incidentPhotoPaths->isNotEmpty())
                                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                                    @foreach ($incidentPhotoPaths as $photoPath)
                                                        <a class="group overflow-hidden rounded-xl border border-outline-variant bg-white shadow-sm" href="{{ asset($photoPath) }}" target="_blank">
                                                            <div class="relative aspect-square bg-surface-variant" data-incident-photo-card>
                                                                <img class="h-full w-full object-cover transition group-hover:scale-105" src="{{ asset($photoPath) }}" alt="Photo de l'incident {{ $incident->id }}" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('grid');">
                                                                <div class="hidden h-full w-full place-items-center p-2 text-center text-xs font-semibold text-secondary" data-photo-fallback>
                                                                    Photo indisponible
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center justify-center gap-1 px-2 py-2 text-xs font-bold text-primary">
                                                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                                                                Ouvrir
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="rounded-lg bg-white p-4 text-sm text-secondary">Aucune photo n'a été jointe à cet incident.</div>
                                            @endif
                                        </div>

                                        @if ($incident->status === 'en_attente')
                                            <form method="POST" action="{{ route('incidents.update', $incident) }}" class="mt-5">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="en_cours">
                                                <input type="hidden" name="assigned_agent_id" value="{{ $incident->assigned_agent_id }}">
                                                <button class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-5 py-4 text-base font-bold text-white shadow-sm md:w-auto" type="submit">
                                                    <span class="material-symbols-outlined">play_arrow</span>
                                                    Démarrer l'intervention
                                                </button>
                                            </form>
                                        @elseif ($incident->status === 'en_cours')
                                            <form method="POST" action="{{ route('incidents.completion.store', $incident) }}" enctype="multipart/form-data" class="agent-proof-form mt-5 rounded-xl border border-primary/30 bg-surface-container-low p-4">
                                                @csrf
                                                <h4 class="text-lg font-bold text-primary">Preuve après intervention</h4>
                                                <p class="mt-1 text-sm text-secondary">Après avoir réglé le problème sur le terrain, prenez une photo claire puis envoyez-la à l'administration pour validation.</p>
                                                <div class="mt-4 grid gap-3 md:grid-cols-2">
                                                    <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-primary bg-white px-4 py-6 text-center font-bold text-primary transition hover:bg-primary/5" for="completion_camera_{{ $incident->id }}">
                                                        <span class="material-symbols-outlined text-4xl">add_a_photo</span>
                                                        Prendre des photos
                                                        <span class="text-xs font-semibold text-secondary">Idéal sur le terrain.</span>
                                                    </label>
                                                    <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-primary bg-white px-4 py-6 text-center font-bold text-primary transition hover:bg-primary/5" for="completion_gallery_{{ $incident->id }}">
                                                        <span class="material-symbols-outlined text-4xl">photo_library</span>
                                                        Choisir dans la galerie
                                                        <span class="text-xs font-semibold text-secondary">Une ou plusieurs photos.</span>
                                                    </label>
                                                </div>
                                                <input id="completion_camera_{{ $incident->id }}" class="sr-only" type="file" accept="image/*" capture="environment" data-completion-source="{{ $incident->id }}">
                                                <input id="completion_gallery_{{ $incident->id }}" class="sr-only" type="file" accept="image/*" multiple data-completion-source="{{ $incident->id }}">
                                                <input id="completion_payload_{{ $incident->id }}" class="sr-only" type="file" name="completion_photos[]" accept="image/*" multiple required data-completion-payload="{{ $incident->id }}">
                                                <div class="mt-4 hidden rounded-lg border border-outline-variant bg-white p-3" data-completion-preview="{{ $incident->id }}">
                                                    <div class="mb-3 flex items-center justify-between gap-3">
                                                        <p class="font-bold text-on-surface">Photos prêtes à envoyer</p>
                                                        <span class="rounded-full bg-surface-container px-3 py-1 text-xs font-bold text-primary" data-completion-count="{{ $incident->id }}">0 photo</span>
                                                    </div>
                                                    <div class="grid grid-cols-3 gap-2" data-completion-list="{{ $incident->id }}"></div>
                                                    <p class="mt-3 text-xs font-semibold text-secondary">Vous pouvez reprendre une photo ou en ajouter depuis la galerie avant d'envoyer.</p>
                                                </div>
                                                <textarea class="mt-4 w-full rounded-lg border-outline-variant text-sm" name="completion_note" rows="3" placeholder="Note facultative pour l'administrateur"></textarea>
                                                <button class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-5 py-4 text-base font-bold text-white shadow-sm md:w-auto" type="submit">
                                                    <span class="material-symbols-outlined">send</span>
                                                    Envoyer la preuve à l'admin
                                                </button>
                                            </form>
                                        @elseif ($incident->status === 'en_validation')
                                            <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                                <div class="flex flex-col gap-3 md:flex-row md:items-center">
                                                    @php
                                                        $completionPhotoPaths = $incident->completionPhotos
                                                            ->pluck('path')
                                                            ->prepend($incident->completion_photo_path)
                                                            ->filter()
                                                            ->unique()
                                                            ->values();
                                                    @endphp
                                                    @if ($completionPhotoPaths->isNotEmpty())
                                                        <div class="grid grid-cols-3 gap-2">
                                                            @foreach ($completionPhotoPaths as $photoPath)
                                                                <a href="{{ asset($photoPath) }}" target="_blank">
                                                                    <img class="h-20 w-20 rounded-lg object-cover" src="{{ asset($photoPath) }}" alt="Preuve envoyée">
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="font-bold text-amber-900">Preuve envoyée à l'administration</p>
                                                        <p class="mt-1 text-sm text-amber-800">La mission attend maintenant la confirmation de l'administrateur.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="border-b border-outline-variant bg-surface-container-low text-xs uppercase tracking-[0.12em] text-secondary">
                                        <tr>
                                            <th class="px-5 py-3">Incident</th>
                                            <th class="px-5 py-3">Localisation</th>
                                            <th class="px-5 py-3">Priorité</th>
                                            <th class="px-5 py-3">Statut</th>
                                            <th class="px-5 py-3">Agent</th>
                                            <th class="px-5 py-3 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-outline-variant">
                                        @foreach ($dashboardCategoryGroups as $categoryGroup)
                                            <tr class="bg-surface-container-low/80">
                                                <td class="px-5 py-3" colspan="6">
                                                    <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                                                        <div class="flex items-center gap-2">
                                                            <span class="material-symbols-outlined text-primary">category</span>
                                                            <strong class="text-sm uppercase tracking-[0.12em] text-primary">{{ $categoryGroup['label'] }}</strong>
                                                            <span class="rounded-full bg-surface-container px-3 py-1 text-xs font-bold text-primary">{{ $categoryGroup['count'] }} plainte{{ $categoryGroup['count'] > 1 ? 's' : '' }}</span>
                                                        </div>
                                                        @if ($categoryGroup['districts'])
                                                            <span class="text-xs font-semibold text-secondary">Zones : {{ $categoryGroup['districts'] }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @foreach ($categoryGroup['incidents'] as $incident)
                                            <tr class="hover:bg-surface-container-low">
                                                <td class="px-5 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="h-12 w-12 overflow-hidden rounded-lg bg-surface-variant">
                                                            @if ($incident->photo_path)
                                                                <img class="h-full w-full object-cover" src="{{ asset($incident->photo_path) }}" alt="Photo de l'incident {{ $incident->id }}">
                                                            @else
                                                                <span class="material-symbols-outlined grid h-full w-full place-items-center text-secondary">image</span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <p class="font-semibold">{{ $incident->title }}</p>
                                                            <p class="text-sm text-secondary">{{ $incident->categoryLabel() }} - {{ $incident->created_at->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <p>{{ $incident->commune?->name ?: 'Commune inconnue' }}</p>
                                                    <p class="text-sm text-secondary">{{ $incident->district }}</p>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <span class="rounded-full {{ $incident->priority === 'elevee' ? 'bg-error-container text-error' : 'bg-surface-container text-primary' }} px-3 py-1 text-xs font-bold">{{ $incident->priorityLabel() }}</span>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <div>{{ $incident->statusLabel() }}</div>
                                                    @if ($incident->status === 'en_validation' && $incident->completion_photo_path)
                                                        <a class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-primary" href="{{ asset($incident->completion_photo_path) }}" target="_blank">
                                                            <span class="material-symbols-outlined text-sm">photo_camera</span>
                                                            Preuve envoyée
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="px-5 py-4 text-sm">
                                                    @if ($incident->assignedAgent)
                                                        <p class="font-semibold text-on-surface">{{ $incident->assignedAgent->name }}</p>
                                                        <p class="mt-1 text-xs text-secondary">
                                                            {{ $incident->assignedAgent->phone ? 'Tel: '.$incident->assignedAgent->phone : $incident->assignedAgent->email }}
                                                        </p>
                                                        @if ($incident->assignedAgent->npi)
                                                            <p class="mt-1 text-xs text-secondary">NPI: {{ $incident->assignedAgent->npi }}</p>
                                                        @endif
                                                    @else
                                                        <span class="text-secondary">Agent non affecté</span>
                                                    @endif
                                                </td>
                                                <td class="px-5 py-4">
                                                    @if (auth()->user()->isAgent())
                                                        @if ($incident->status === 'en_cours')
                                                            <form method="POST" action="{{ route('incidents.completion.store', $incident) }}" enctype="multipart/form-data" class="grid min-w-72 gap-2">
                                                                @csrf
                                                                <input class="rounded border-outline-variant text-sm" type="file" name="completion_photo" accept="image/*" capture="environment" required>
                                                                <textarea class="rounded border-outline-variant text-sm" name="completion_note" rows="2" placeholder="Note courte facultative"></textarea>
                                                                <button class="rounded bg-primary px-3 py-2 text-sm font-semibold text-white" type="submit">Envoyer la preuve</button>
                                                            </form>
                                                        @elseif ($incident->status === 'en_validation')
                                                            <div class="rounded-lg bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-900">En attente de validation admin</div>
                                                        @else
                                                            <form method="POST" action="{{ route('incidents.update', $incident) }}" class="flex justify-end gap-2">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="en_cours">
                                                                <input type="hidden" name="assigned_agent_id" value="{{ $incident->assigned_agent_id }}">
                                                                <button class="rounded bg-primary px-3 py-2 text-sm font-semibold text-white" type="submit">Démarrer</button>
                                                            </form>
                                                        @endif
                                                    @else
                                                        @if ($incident->completion_photo_path)
                                                            @php
                                                                $completionPhotoPaths = $incident->completionPhotos
                                                                    ->pluck('path')
                                                                    ->prepend($incident->completion_photo_path)
                                                                    ->filter()
                                                                    ->unique()
                                                                    ->values();
                                                            @endphp
                                                            <div class="mb-3 flex items-center gap-3 rounded-lg bg-surface-container-low p-2">
                                                                <img class="h-14 w-14 rounded object-cover" src="{{ asset($completionPhotoPaths->first()) }}" alt="Preuve agent">
                                                                <div class="text-sm">
                                                                    <p class="font-semibold text-on-surface">Preuves de l'agent</p>
                                                                    <div class="mt-1 flex flex-wrap gap-2">
                                                                        @foreach ($completionPhotoPaths as $index => $photoPath)
                                                                            <a class="text-primary" href="{{ asset($photoPath) }}" target="_blank">Photo {{ $index + 1 }}</a>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <form method="POST" action="{{ route('incidents.update', $incident) }}" class="flex justify-end gap-2">
                                                            @csrf
                                                            @method('PATCH')
                                                            @if ($incident->status === 'en_validation' && $incident->completion_photo_path)
                                                                <input type="hidden" name="status" value="resolu">
                                                                <span class="inline-flex items-center rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800">
                                                                    Mission traitée
                                                                </span>
                                                            @else
                                                                <select class="w-36 rounded border-outline-variant text-sm" name="status" aria-label="Statut">
                                                                    @foreach ($statuses as $value => $label)
                                                                        <option value="{{ $value }}" @selected($incident->status === $value)>{{ $label }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                            @if (auth()->user()->isAdmin())
                                                                <select class="w-72 rounded border-outline-variant text-sm" name="assigned_agent_id" aria-label="Agent affecté">
                                                                    <option value="">Aucun agent</option>
                                                                    @foreach ($agents as $agent)
                                                                        <option value="{{ $agent->id }}" @selected($incident->assigned_agent_id === $agent->id)>{{ $agent->assignmentLabel() }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <input type="hidden" name="assigned_agent_id" value="{{ $incident->assigned_agent_id }}">
                                                            @endif
                                                            <button class="rounded bg-primary px-3 py-2 text-sm font-semibold text-white" type="submit">{{ $incident->status === 'en_validation' ? 'Confirmer' : 'Mettre à jour' }}</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                </section>

                @unless (auth()->user()->isAgent())
                    <section data-view="carte" id="carte">
                        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-6 shadow-sm">
                            <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                                <div>
                                    <h2 class="text-3xl font-semibold text-on-surface">Carte interactive</h2>
                                    <p class="mt-1 text-secondary">Chaque drapeau rouge représente une plainte réelle localisée par GPS dans votre zone.</p>
                                </div>
                                <span class="rounded-full bg-error-container px-3 py-1 text-sm font-bold text-error">{{ $mapIncidents->count() }} drapeau{{ $mapIncidents->count() > 1 ? 'x' : '' }}</span>
                            </div>
                            <div id="map"></div>
                        </div>
                    </section>

                    <section data-view="incidents" id="incidents">
                        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest shadow-sm">
                            <div class="border-b border-outline-variant bg-surface-container-low/60 p-5">
                                <h2 class="text-2xl font-semibold text-on-surface">Gestion des incidents</h2>
                                <p class="mt-1 text-sm text-secondary">Tous les incidents visibles dans votre zone administrative.</p>
                            </div>
                            <div class="grid gap-5 p-5">
                                @forelse ($allIncidentCategoryGroups as $categoryGroup)
                                    <section class="overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest">
                                        <div class="flex flex-col gap-2 border-b border-outline-variant bg-surface-container-low px-5 py-4 md:flex-row md:items-center md:justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-primary">category</span>
                                                <h3 class="text-lg font-bold text-primary">{{ $categoryGroup['label'] }}</h3>
                                                <span class="rounded-full bg-surface-container px-3 py-1 text-xs font-bold text-primary">{{ $categoryGroup['count'] }} incident{{ $categoryGroup['count'] > 1 ? 's' : '' }}</span>
                                            </div>
                                            @if ($categoryGroup['districts'])
                                                <p class="text-sm font-semibold text-secondary">Zones proches : {{ $categoryGroup['districts'] }}</p>
                                            @endif
                                        </div>
                                        <div class="divide-y divide-outline-variant">
                                            @foreach ($categoryGroup['incidents'] as $incident)
                                                <article class="grid gap-4 p-5 md:grid-cols-[1fr_auto]">
                                                    <div>
                                                        <h4 class="text-lg font-semibold">{{ $incident->title }}</h4>
                                                        <p class="mt-1 text-sm text-secondary">{{ $incident->commune?->name }} - {{ $incident->district }} - {{ $incident->created_at->format('d/m/Y H:i') }}</p>
                                                        <p class="mt-3 text-sm text-on-surface-variant">{{ $incident->description }}</p>
                                                    </div>
                                                    <div class="flex flex-wrap items-start gap-2 md:justify-end">
                                                        <span class="rounded-full bg-surface-container px-3 py-1 text-xs font-bold text-primary">{{ $incident->statusLabel() }}</span>
                                                        <span class="rounded-full {{ $incident->priority === 'elevee' ? 'bg-error-container text-error' : 'bg-surface-container text-primary' }} px-3 py-1 text-xs font-bold">{{ $incident->priorityLabel() }}</span>
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    </section>
                                @empty
                                    <div class="p-8 text-center text-secondary">Aucun incident réel dans votre zone pour le moment.</div>
                                @endforelse
                            </div>
                        </div>
                    </section>
                @endunless

                <section data-view="historique" id="historique">
                    <div class="rounded-xl border border-outline-variant bg-surface-container-lowest shadow-sm">
                        <div class="border-b border-outline-variant bg-surface-container-low/60 p-5">
                            <h2 class="text-2xl font-semibold text-on-surface">Historique des plaintes traitées</h2>
                            <p class="mt-1 text-sm text-secondary">Plaintes résolues retirées du tableau de bord actif.</p>
                        </div>
                        <div class="grid gap-4 p-5 xl:grid-cols-2">
                            @forelse ($historyIncidents as $incident)
                                <article class="overflow-hidden rounded-xl border border-outline-variant bg-white shadow-sm">
                                    <div class="h-1.5 bg-emerald-500"></div>
                                    <div class="grid gap-5 p-5 md:grid-cols-[96px_1fr]">
                                        <div class="h-24 w-24 overflow-hidden rounded-lg bg-surface-variant">
                                            @if ($incident->photo_path)
                                                <img class="h-full w-full object-cover" src="{{ asset($incident->photo_path) }}" alt="Photo de l'incident {{ $incident->id }}">
                                            @else
                                                <span class="material-symbols-outlined grid h-full w-full place-items-center text-secondary">image</span>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                                <div>
                                                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">Intervention terminée</p>
                                                    <h3 class="mt-1 text-lg font-semibold text-on-surface">{{ $incident->title }}</h3>
                                                </div>
                                                <div class="rounded-lg bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-800">
                                                    Résolu
                                                </div>
                                            </div>

                                            <div class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
                                                <div class="rounded-lg bg-surface-container-low px-3 py-2">
                                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Zone</p>
                                                    <p class="mt-1 font-semibold text-on-surface">{{ $incident->commune?->name ?: 'Commune inconnue' }}</p>
                                                </div>
                                                <div class="rounded-lg bg-surface-container-low px-3 py-2">
                                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Priorité</p>
                                                    <p class="mt-1 font-semibold text-on-surface">{{ $incident->priorityLabel() }}</p>
                                                </div>
                                                <div class="rounded-lg bg-surface-container-low px-3 py-2">
                                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Agent</p>
                                                    <p class="mt-1 truncate font-semibold text-on-surface">{{ $incident->assignedAgent?->name ?: 'Non affecté' }}</p>
                                                </div>
                                            </div>

                                            <p class="mt-2 text-sm text-on-surface-variant">{{ $incident->description }}</p>

                                            <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-outline-variant pt-3 text-sm text-secondary">
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-base text-primary">category</span>
                                                    {{ $incident->categoryLabel() }}
                                                </span>
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-base text-primary">schedule</span>
                                                    {{ ($incident->resolved_at ?? $incident->updated_at)->format('d/m/Y H:i') }}
                                                </span>
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-base text-primary">location_on</span>
                                                    {{ $incident->district }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-xl border border-dashed border-outline-variant bg-surface-container-lowest p-8 text-center text-secondary xl:col-span-2">Aucune plainte traitée pour le moment.</div>
                            @endforelse
                        </div>
                    </div>
                </section>

                @if (auth()->user()->isAgent())
                    <section data-view="profil" id="profil">
                        @php
                            $profileUser = auth()->user();
                            $sexLabels = [
                                'homme' => 'Homme',
                                'femme' => 'Femme',
                                'autre' => 'Autre',
                            ];
                            $profileAddress = $profileUser->address ?: ($profileUser->commune?->name ?: 'Adresse non renseignée');
                        @endphp
                        <div class="grid gap-6 xl:grid-cols-[0.8fr_1.2fr]">
                            <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-6 shadow-sm">
                                <h2 class="text-2xl font-semibold text-primary">Aperçu du profil</h2>
                                <div class="mt-7 flex flex-col items-center text-center">
                                    <div class="relative">
                                        <div class="h-36 w-36 overflow-hidden rounded-full bg-surface-container ring-2 ring-emerald-500 ring-offset-4 ring-offset-white">
                                            @if ($profileUser->profile_photo_path)
                                                <img class="h-full w-full object-cover" id="profile-photo-preview" src="{{ asset($profileUser->profile_photo_path) }}" alt="Photo de {{ $profileUser->name }}">
                                            @else
                                                <div class="grid h-full w-full place-items-center text-primary" id="profile-photo-placeholder">
                                                    <span class="material-symbols-outlined text-6xl">person</span>
                                                </div>
                                                <img class="hidden h-full w-full object-cover" id="profile-photo-preview" alt="Aperçu photo de profil">
                                            @endif
                                        </div>
                                        <label class="absolute bottom-1 right-1 grid h-11 w-11 cursor-pointer place-items-center rounded-full border-4 border-white bg-emerald-600 text-white shadow-lg transition hover:bg-emerald-700" for="profile_photo" title="Modifier la photo">
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </label>
                                    </div>
                                    <h3 class="mt-7 text-2xl font-semibold text-primary">{{ $profileUser->name }}</h3>
                                    <p class="mt-1 text-sm font-semibold text-secondary">{{ $profileUser->role === 'agent' ? 'Agent terrain' : ucfirst($profileUser->role) }}</p>
                                </div>
                                <div class="mt-7 divide-y divide-outline-variant text-left">
                                    <div class="flex items-start gap-3 py-3">
                                        <span class="material-symbols-outlined mt-0.5 text-primary">location_on</span>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Adresse</p>
                                            <p class="mt-1 font-semibold">{{ $profileAddress }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 py-3">
                                        <span class="material-symbols-outlined mt-0.5 text-primary">call</span>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Téléphone</p>
                                            <p class="mt-1 font-semibold">{{ $profileUser->phone ?: 'Non renseigné' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 py-3">
                                        <span class="material-symbols-outlined mt-0.5 text-primary">badge</span>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">NPI</p>
                                            <p class="mt-1 font-semibold">{{ $profileUser->npi ?: 'Non renseigné' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 py-3">
                                        <span class="material-symbols-outlined mt-0.5 text-primary">mail</span>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Adresse e-mail</p>
                                            <p class="mt-1 break-all font-semibold">{{ $profileUser->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 py-3">
                                        <span class="material-symbols-outlined mt-0.5 text-primary">wc</span>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Sexe</p>
                                            <p class="mt-1 font-semibold">{{ $sexLabels[$profileUser->sex] ?? 'Non renseigné' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('agents.profile.update') }}" enctype="multipart/form-data" class="rounded-xl border border-outline-variant bg-surface-container-lowest p-6 shadow-sm" id="agent-profile-form">
                                @csrf
                                @method('PATCH')
                                <div class="mb-6">
                                    <h2 class="text-2xl font-semibold text-primary">Modifier vos informations personnelles</h2>
                                </div>
                                <div class="grid gap-4">
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="profile_name">Nom complet</label>
                                            <input class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="profile_name" name="name" value="{{ old('name', $profileUser->name) }}" required>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="profile_email">Adresse e-mail</label>
                                            <input class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="profile_email" type="email" name="email" value="{{ old('email', $profileUser->email) }}" required>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="profile_phone">Numéro de téléphone</label>
                                            <input class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="profile_phone" name="phone" value="{{ old('phone', $profileUser->phone) }}" required>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="profile_npi">NPI</label>
                                            <input class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="profile_npi" name="npi" value="{{ old('npi', $profileUser->npi) }}" placeholder="Numéro personnel d'identification">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="profile_sex">Sexe</label>
                                            <select class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="profile_sex" name="sex">
                                                <option value="">Non renseigné</option>
                                                <option value="homme" @selected(old('sex', $profileUser->sex) === 'homme')>Homme</option>
                                                <option value="femme" @selected(old('sex', $profileUser->sex) === 'femme')>Femme</option>
                                                <option value="autre" @selected(old('sex', $profileUser->sex) === 'autre')>Autre</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="profile_address">Adresse</label>
                                        <textarea class="min-h-24 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="profile_address" name="address" rows="3">{{ old('address', $profileUser->address) }}</textarea>
                                    </div>
                                </div>
                                <input class="hidden" id="profile_photo" type="file" name="profile_photo" accept="image/*">
                                <span class="sr-only" id="profile-photo-name"></span>
                                <button class="mt-6 flex w-full items-center justify-center gap-2 rounded bg-primary px-5 py-4 text-sm font-bold uppercase tracking-wider text-white transition hover:bg-primary-container" type="submit">
                                    Enregistrer mon profil
                                    <span class="material-symbols-outlined text-lg">save</span>
                                </button>
                            </form>
                        </div>
                    </section>
                @endif

                @if (auth()->user()->isAdmin())
                    <section data-view="stats" id="stats">
                        <div class="mb-6 rounded-xl border border-outline-variant bg-surface-container-lowest p-6 shadow-sm">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <h2 class="text-2xl font-semibold text-primary">Performance communale</h2>
                                    <p class="mt-1 text-sm text-secondary">Indicateurs clés pour évaluer le traitement des incidents de {{ auth()->user()->commune?->name ?: 'la commune' }}.</p>
                                </div>
                                <a class="inline-flex items-center justify-center gap-2 rounded bg-primary px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-primary-container" href="{{ route('incidents.performance.report') }}" target="_blank">
                                    <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
                                    Rapport PDF
                                </a>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-4">
                                <div class="rounded-lg bg-surface-container-low p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Taux de résolution</p>
                                    <p class="mt-2 text-3xl font-bold text-primary">{{ $performanceStats['resolutionRate'] }}%</p>
                                </div>
                                <div class="rounded-lg bg-surface-container-low p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Temps moyen total</p>
                                    <p class="mt-2 text-3xl font-bold text-primary">
                                        {{ $performanceStats['averageResolutionHours'] !== null ? $performanceStats['averageResolutionHours'].' h' : 'N/A' }}
                                    </p>
                                </div>
                                <div class="rounded-lg bg-surface-container-low p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Temps intervention</p>
                                    <p class="mt-2 text-3xl font-bold text-primary">
                                        {{ $performanceStats['averageInterventionHours'] !== null ? $performanceStats['averageInterventionHours'].' h' : 'N/A' }}
                                    </p>
                                </div>
                                <div class="rounded-lg bg-surface-container-low p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Dossiers > 48h</p>
                                    <p class="mt-2 text-3xl font-bold {{ $performanceStats['late'] > 0 ? 'text-error' : 'text-primary' }}">{{ $performanceStats['late'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                            <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-6 shadow-sm" data-stats-detail>
                                <h2 class="text-2xl font-semibold text-primary">Statistiques par catégorie</h2>
                                <div class="mt-5 space-y-4">
                                    @forelse ($categoryStats as $row)
                                        @php $percent = $stats['total'] > 0 ? max(8, round(($row['count'] / $stats['total']) * 100)) : 0; @endphp
                                        <div>
                                            <div class="mb-2 flex justify-between text-sm font-semibold">
                                                <span>{{ $row['label'] }}</span>
                                                <span>{{ $row['count'] }}</span>
                                            </div>
                                            <div class="h-3 overflow-hidden rounded-full bg-surface-container">
                                                <div class="h-full rounded-full bg-primary" style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-secondary">Les graphiques apparaissent après les premiers signalements.</p>
                                    @endforelse
                                </div>
                            </div>
                            <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-6 shadow-sm" data-stats-detail>
                                <h2 class="text-2xl font-semibold text-primary">Activité des agents</h2>
                                <div class="mt-5 space-y-4">
                                    @forelse ($agentStats as $row)
                                        <article class="rounded-lg border border-outline-variant bg-white p-4">
                                            <div class="mb-4 flex items-start justify-between gap-3">
                                                <div>
                                                    <h3 class="font-semibold text-on-surface">{{ $row['name'] }}</h3>
                                                    <p class="mt-1 text-sm text-secondary">{{ $row['commune'] ?: 'Commune non définie' }}</p>
                                                </div>
                                                <div class="rounded-lg bg-surface-container-low px-3 py-2 text-right">
                                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Affectées</p>
                                                    <p class="text-xl font-semibold text-primary">{{ $row['assigned'] }}</p>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3 text-sm">
                                                <div class="rounded-lg bg-amber-50 px-3 py-2">
                                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-amber-800">En cours</p>
                                                    <p class="mt-1 text-lg font-semibold text-amber-900">{{ $row['progress'] }}</p>
                                                </div>
                                                <div class="rounded-lg bg-emerald-50 px-3 py-2">
                                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-emerald-800">Résolues</p>
                                                    <p class="mt-1 text-lg font-semibold text-emerald-900">{{ $row['resolved'] }}</p>
                                                </div>
                                            </div>

                                            <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                                                <div class="rounded-lg bg-surface-container-low px-3 py-2">
                                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Temps moyen</p>
                                                    <p class="mt-1 text-lg font-semibold text-primary">{{ $row['averageResolutionHours'] !== null ? $row['averageResolutionHours'].' h' : 'N/A' }}</p>
                                                </div>
                                                <div class="rounded-lg bg-surface-container-low px-3 py-2">
                                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-secondary">Dossiers > 48h</p>
                                                    <p class="mt-1 text-lg font-semibold {{ $row['late'] > 0 ? 'text-error' : 'text-primary' }}">{{ $row['late'] }}</p>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <div class="mb-2 flex justify-between text-xs font-bold uppercase tracking-[0.12em] text-secondary">
                                                    <span>Taux de résolution</span>
                                                    <span>{{ $row['resolutionRate'] }}%</span>
                                                </div>
                                                <div class="h-3 overflow-hidden rounded-full bg-surface-container">
                                                    <div class="h-full rounded-full bg-primary-container" style="width: {{ max(4, $row['resolutionRate']) }}%"></div>
                                                </div>
                                            </div>

                                            <div class="mt-5 border-t border-outline-variant pt-4">
                                                <div class="mb-3 flex items-center justify-between gap-3">
                                                    <h4 class="text-sm font-bold uppercase tracking-[0.12em] text-secondary">Missions</h4>
                                                    <span class="rounded-full bg-surface-container px-3 py-1 text-xs font-bold text-primary">{{ $row['missions']->count() }} récente{{ $row['missions']->count() > 1 ? 's' : '' }}</span>
                                                </div>

                                                <div class="space-y-3">
                                                    @forelse ($row['missions'] as $mission)
                                                        <div class="rounded-lg border border-outline-variant bg-surface-container-low p-3">
                                                            <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                                                <div class="min-w-0">
                                                                    <p class="truncate font-semibold text-on-surface">{{ $mission['title'] }}</p>
                                                                    <p class="mt-1 text-xs text-secondary">
                                                                        {{ $mission['categoryLabel'] }} - {{ $mission['commune'] ?: 'Commune inconnue' }}{{ $mission['district'] ? ' / '.$mission['district'] : '' }}
                                                                    </p>
                                                                </div>
                                                                <div class="flex flex-wrap gap-2 md:justify-end">
                                                                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-primary">{{ $mission['statusLabel'] }}</span>
                                                                    <span class="rounded-full {{ $mission['priority'] === 'elevee' ? 'bg-error-container text-error' : 'bg-white text-primary' }} px-2.5 py-1 text-xs font-bold">{{ $mission['priorityLabel'] }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-secondary">
                                                                <span class="inline-flex items-center gap-1">
                                                                    <span class="material-symbols-outlined text-sm text-primary">calendar_month</span>
                                                                    Signalée le {{ $mission['date'] }}
                                                                </span>
                                                                <span class="inline-flex items-center gap-1">
                                                                    <span class="material-symbols-outlined text-sm text-primary">update</span>
                                                                    Dernière action {{ $mission['updated'] }}
                                                                </span>
                                                                @if ($mission['completionPhotos']->isNotEmpty())
                                                                    <span class="inline-flex items-center gap-2">
                                                                        <span class="material-symbols-outlined text-sm text-primary">photo_camera</span>
                                                                        @foreach ($mission['completionPhotos'] as $index => $photoUrl)
                                                                            <a class="font-bold text-primary" href="{{ $photoUrl }}" target="_blank">Preuve {{ $index + 1 }}</a>
                                                                        @endforeach
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="rounded-lg bg-surface-container-low p-3 text-sm text-secondary">Aucune mission affectée à cet agent pour le moment.</p>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </article>
                                    @empty
                                        <p class="text-secondary">Aucun agent technique enregistré pour le moment.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </main>
    </div>

    @if (auth()->user()->isAgent())
        <div class="profile-lightbox fixed inset-0 z-[70] grid place-items-center bg-slate-950/85 p-6 backdrop-blur-sm" id="profile-lightbox" hidden>
            <button class="absolute right-5 top-5 rounded-full bg-white/12 p-3 text-white transition hover:bg-white/20" id="profile-photo-close" type="button" aria-label="Fermer la photo">
                <span class="material-symbols-outlined">close</span>
            </button>
            <div class="max-h-[86vh] max-w-[92vw] overflow-hidden rounded-2xl bg-white p-2 shadow-2xl">
                @if (auth()->user()->profile_photo_path)
                    <img class="max-h-[82vh] w-auto object-contain" src="{{ asset(auth()->user()->profile_photo_path) }}" alt="Photo de profil de {{ auth()->user()->name }}">
                @else
                    <div class="grid h-80 w-80 place-items-center rounded-xl bg-surface-container text-primary">
                        <span class="material-symbols-outlined text-8xl">account_circle</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const incidents = @json($mapIncidents);
        const canAddToDashboard = @json(auth()->user()->isAdmin());
        const dashboardIncidentIds = new Set(@json($incidents->pluck('id')->values()));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const mapCenter = @json($mapCenter);
        const defaultCenter = [mapCenter.latitude, mapCenter.longitude];
        const mapElement = document.getElementById('map');
        let map = null;
        const markerByIncidentId = new Map();
        const incidentById = new Map(incidents.map((incident) => [String(incident.id), incident]));

        if (mapElement) {
            map = L.map('map').setView(defaultCenter, mapCenter.zoom);
            const redFlagIcon = L.divIcon({
                className: '',
                html: '<div class="flag-marker"></div>',
                iconSize: [24, 30],
                iconAnchor: [4, 30],
                popupAnchor: [8, -26]
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const markers = [];
            incidents.forEach((incident) => {
                const marker = L.marker([incident.latitude, incident.longitude], { icon: redFlagIcon }).addTo(map);
                marker.bindPopup(`
                    <strong>${incident.title}</strong><br>
                    Catégorie : ${incident.category}<br>
                    Commune : ${incident.commune || 'Non définie'}<br>
                    Zone : ${incident.district}<br>
                    Latitude : ${incident.latitude}<br>
                    Longitude : ${incident.longitude}<br>
                    Date : ${incident.date}<br>
                    Statut : ${incident.status}<br>
                    Priorité : ${incident.priorityLabel}<br>
                    Agent : ${incident.agent || 'Agent non affecté'}<br>
                    ${incident.description ? '<br>' + incident.description : ''}
                    ${incident.photos?.length ? `<br><div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px;margin-top:8px;">${incident.photos.map((photo) => `<img src="${photo}" alt="Photo" style="width:100%;height:86px;object-fit:cover;border-radius:6px;">`).join('')}</div>` : ''}
                    ${canAddToDashboard && !dashboardIncidentIds.has(incident.id) ? `<br><button class="popup-dashboard" type="button" data-incident-id="${incident.id}">Ajouter au dashboard</button>` : ''}
                `);
                marker.on('mouseover', () => marker.openPopup());
                marker.on('click', () => marker.openPopup());
                markerByIncidentId.set(String(incident.id), marker);
                markers.push(marker);
            });

            if (markers.length) {
                map.fitBounds(L.featureGroup(markers).getBounds().pad(0.2));
            }
        }

        document.addEventListener('click', (event) => {
            const button = event.target.closest('.popup-dashboard');
            if (!button) return;
            const incident = incidentById.get(String(button.dataset.incidentId));
            const marker = markerByIncidentId.get(String(button.dataset.incidentId));
            if (incident) addToDashboard(incident, marker, button);
        });

        function addToDashboard(incident, marker, button) {
            if (!canAddToDashboard || dashboardIncidentIds.has(incident.id)) return;
            dashboardIncidentIds.add(incident.id);
            button.disabled = true;
            button.textContent = 'Ajout...';

            fetch(incident.dashboardUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
                .then((response) => {
                    if (!response.ok) throw new Error('Ajout impossible');
                    if (map && marker) {
                        map.closePopup();
                        map.removeLayer(marker);
                    }
                    button.textContent = 'Ajouté';
                    setTimeout(() => {
                        window.location.href = `${window.location.pathname}#admin`;
                        window.location.reload();
                    }, 250);
                })
                .catch(() => {
                    dashboardIncidentIds.delete(incident.id);
                    button.disabled = false;
                    button.textContent = 'Ajouter au dashboard';
                    alert("Impossible d'ajouter cette plainte au dashboard.");
                });
        }

        const availableViews = Array.from(document.querySelectorAll('[data-view]'));
        const navLinks = Array.from(document.querySelectorAll('[data-nav-view]'));
        const summaryPanel = document.getElementById('summary-panel');
        const adminView = document.getElementById('admin');
        const defaultView = 'admin';

        function placeSummaryPanel(targetView) {
            if (!summaryPanel || !adminView?.parentElement) return;

            const targetElement = availableViews.find((element) => element.dataset.view === targetView) || adminView;
            summaryPanel.classList.add('view-summary');
            targetElement.insertAdjacentElement('afterend', summaryPanel);
        }

        function showView(view) {
            const targetView = availableViews.some((element) => element.dataset.view === view) ? view : defaultView;
            placeSummaryPanel(targetView);
            availableViews.forEach((element) => {
                element.hidden = element.dataset.view !== targetView;
            });
            if (summaryPanel && document.getElementById('app-shell')?.classList.contains('agent-shell')) {
                summaryPanel.hidden = targetView !== defaultView;
            }
            navLinks.forEach((link) => {
                link.classList.toggle('active', link.dataset.navView === targetView);
            });
            if (targetView === 'carte' && map) {
                setTimeout(() => map.invalidateSize(), 120);
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        navLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const view = link.dataset.navView;
                history.replaceState(null, '', `#${view}`);
                showView(view);
                closeAgentSidebar();
            });
        });

        const appShell = document.getElementById('app-shell');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');
        const profilePhotoOpen = document.getElementById('profile-photo-open');
        const profileLightbox = document.getElementById('profile-lightbox');
        const profilePhotoClose = document.getElementById('profile-photo-close');
        const profilePhotoInput = document.getElementById('profile_photo');
        const profilePhotoPreview = document.getElementById('profile-photo-preview');
        const profilePhotoPlaceholder = document.getElementById('profile-photo-placeholder');
        const profilePhotoName = document.getElementById('profile-photo-name');
        const notificationsToggle = document.getElementById('notifications-toggle');
        const notificationsPanel = document.getElementById('notifications-panel');
        const notificationsBadge = document.getElementById('notifications-badge');
        const notificationsUnreadText = document.getElementById('notifications-unread-text');
        const notificationsUnreadPlural = document.getElementById('notifications-unread-plural');

        function setUnreadNotificationCount(count) {
            const safeCount = Math.max(0, Number(count) || 0);
            if (notificationsBadge) {
                notificationsBadge.textContent = safeCount;
                notificationsBadge.classList.toggle('hidden', safeCount === 0);
                notificationsBadge.classList.toggle('grid', safeCount > 0);
            }
            if (notificationsUnreadText) {
                notificationsUnreadText.textContent = safeCount;
            }
            if (notificationsUnreadPlural) {
                notificationsUnreadPlural.textContent = safeCount > 1 ? 's' : '';
            }
        }

        function closeNotificationsPanel() {
            if (!notificationsPanel || !notificationsToggle) return;
            notificationsPanel.hidden = true;
            notificationsToggle.setAttribute('aria-expanded', 'false');
        }

        notificationsToggle?.addEventListener('click', (event) => {
            event.stopPropagation();
            if (!notificationsPanel) return;
            notificationsPanel.hidden = !notificationsPanel.hidden;
            notificationsToggle.setAttribute('aria-expanded', notificationsPanel.hidden ? 'false' : 'true');
        });

        notificationsPanel?.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        document.addEventListener('click', closeNotificationsPanel);

        document.querySelectorAll('[data-notification-item]').forEach((item) => {
            item.addEventListener('click', async () => {
                if (item.dataset.read === '1') return;

                try {
                    const response = await fetch(item.dataset.readUrl, {
                        method: 'PATCH',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({}),
                    });

                    if (!response.ok) throw new Error('Lecture impossible');

                    const data = await response.json();
                    item.dataset.read = '1';
                    item.classList.remove('bg-surface-container-low', 'text-on-surface');
                    item.classList.add('text-secondary');
                    item.querySelector('[data-unread-dot]')?.remove();
                    setUnreadNotificationCount(data.unread_count);
                } catch (error) {
                    alert("Impossible de marquer cette notification comme lue.");
                }
            });
        });

        function closeAgentSidebar() {
            if (!appShell || !sidebarToggle) return;
            appShell.classList.remove('sidebar-open');
            sidebarToggle.setAttribute('aria-expanded', 'false');
        }

        function toggleAgentSidebar() {
            if (!appShell || !sidebarToggle) return;
            const isOpen = appShell.classList.toggle('sidebar-open');
            sidebarToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        sidebarToggle?.addEventListener('click', toggleAgentSidebar);
        sidebarBackdrop?.addEventListener('click', closeAgentSidebar);

        function closeProfilePhoto() {
            if (!profileLightbox) return;
            profileLightbox.hidden = true;
            document.body.classList.remove('overflow-hidden');
        }

        profilePhotoOpen?.addEventListener('click', () => {
            if (!profileLightbox) return;
            profileLightbox.hidden = false;
            document.body.classList.add('overflow-hidden');
        });

        profilePhotoClose?.addEventListener('click', closeProfilePhoto);
        profileLightbox?.addEventListener('click', (event) => {
            if (event.target === profileLightbox) closeProfilePhoto();
        });

        profilePhotoInput?.addEventListener('change', () => {
            const file = profilePhotoInput.files?.[0];
            if (!file || !profilePhotoPreview) return;

            const url = URL.createObjectURL(file);
            profilePhotoPreview.src = url;
            profilePhotoPreview.classList.remove('hidden');
            profilePhotoPlaceholder?.classList.add('hidden');
            if (profilePhotoName) {
                profilePhotoName.textContent = file.name;
            }
            profilePhotoPreview.addEventListener('load', () => URL.revokeObjectURL(url), { once: true });
            document.getElementById('agent-profile-form')?.requestSubmit();
        });

        const completionFilesByIncident = new Map();

        function syncCompletionPayload(id) {
            const payload = document.querySelector(`[data-completion-payload="${id}"]`);
            const files = completionFilesByIncident.get(id) || [];
            if (!payload || typeof DataTransfer === 'undefined') return;

            const transfer = new DataTransfer();
            files.forEach((file) => transfer.items.add(file));
            payload.files = transfer.files;
        }

        function renderCompletionPreview(id) {
            const files = completionFilesByIncident.get(id) || [];
            const preview = document.querySelector(`[data-completion-preview="${id}"]`);
            const list = document.querySelector(`[data-completion-list="${id}"]`);
            const count = document.querySelector(`[data-completion-count="${id}"]`);
            if (!preview || !list) return;

            list.innerHTML = '';
            if (count) {
                count.textContent = `${files.length} photo${files.length > 1 ? 's' : ''}`;
            }

            files.forEach((file, index) => {
                const url = URL.createObjectURL(file);
                const item = document.createElement('div');
                item.className = 'overflow-hidden rounded-lg border border-outline-variant bg-surface-container-low';
                item.innerHTML = `
                    <img class="h-20 w-full object-cover" src="${url}" alt="Preuve ${index + 1}">
                    <div class="flex items-center justify-between gap-1 px-2 py-1">
                        <p class="min-w-0 truncate text-xs font-semibold text-secondary">${file.name}</p>
                        <button class="shrink-0 rounded bg-error-container px-2 py-1 text-xs font-bold text-error" type="button" data-remove-completion-photo="${id}" data-photo-index="${index}">Supprimer</button>
                    </div>
                `;
                item.querySelector('img')?.addEventListener('load', () => URL.revokeObjectURL(url), { once: true });
                list.appendChild(item);
            });

            preview.classList.toggle('hidden', files.length === 0);
            syncCompletionPayload(id);
        }

        document.querySelectorAll('[data-completion-source]').forEach((input) => {
            input.addEventListener('change', () => {
                const files = Array.from(input.files || []);
                if (!files.length) return;

                const id = input.dataset.completionSource;
                const currentFiles = completionFilesByIncident.get(id) || [];
                completionFilesByIncident.set(id, [...currentFiles, ...files]);
                input.value = '';
                renderCompletionPreview(id);
            });
        });

        document.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-remove-completion-photo]');
            if (!removeButton) return;

            const id = removeButton.dataset.removeCompletionPhoto;
            const index = Number(removeButton.dataset.photoIndex);
            const files = completionFilesByIncident.get(id) || [];
            files.splice(index, 1);
            completionFilesByIncident.set(id, files);
            renderCompletionPreview(id);
        });

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeNotificationsPanel();
                closeAgentSidebar();
                closeProfilePhoto();
            }
        });

        showView(window.location.hash.replace('#', '') || defaultView);
    </script>
</body>
</html>
