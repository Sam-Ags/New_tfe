<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart City - Incidents urbains</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        :root {
            --ink: #17212b;
            --muted: #607080;
            --line: #dfe7ec;
            --panel: #ffffff;
            --soft: #f5f8fa;
            --brand: #0e7c7b;
            --brand-dark: #075f61;
            --accent: #d97706;
            --danger: #c2410c;
            --ok: #15803d;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Instrument Sans", system-ui, sans-serif;
            color: var(--ink);
            background: var(--soft);
        }

        a { color: inherit; }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            border-bottom: 1px solid var(--line);
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(12px);
        }

        .topbar-inner {
            max-width: 1220px;
            margin: 0 auto;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }

        .brand-mark {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: white;
            background: var(--brand);
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
            flex-wrap: wrap;
        }

        .nav a {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            border-radius: 7px;
            padding: 8px 10px;
            color: #334155;
            background: #f1f5f9;
            text-decoration: none;
            font-weight: 700;
        }

        .nav a:hover {
            color: var(--brand-dark);
            background: #dcefed;
        }

        .user-chip {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            border: 1px solid var(--line);
            border-radius: 7px;
            padding: 8px 10px;
            color: #334155;
            background: #fff;
            font-weight: 700;
        }

        .logout-form button {
            min-height: 36px;
            border: 1px solid #fecaca;
            border-radius: 7px;
            padding: 8px 10px;
            background: #fff7f7;
            color: var(--danger);
            font-weight: 700;
            cursor: pointer;
        }

        .hero {
            background:
                linear-gradient(120deg, rgba(10, 58, 64, .88), rgba(20, 93, 89, .68)),
                url("https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1600&q=80");
            background-size: cover;
            background-position: center;
            color: white;
        }

        .hero-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: 64px 20px 42px;
            min-height: 360px;
            display: grid;
            align-content: end;
        }

        .eyebrow {
            margin: 0 0 12px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        h1 {
            max-width: 760px;
            margin: 0;
            font-size: clamp(34px, 5vw, 62px);
            line-height: 1.02;
            letter-spacing: 0;
        }

        .hero p {
            max-width: 720px;
            margin: 16px 0 0;
            font-size: 18px;
            color: rgba(255,255,255,.88);
        }

        .wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 26px 20px 56px;
        }

        .notice {
            margin-bottom: 18px;
            padding: 13px 14px;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            background: #f0fdf4;
            color: #166534;
            font-weight: 600;
        }

        .grid {
            display: grid;
            gap: 18px;
        }

        [data-view][hidden],
        [data-stats-detail][hidden],
        [data-dashboard-panel][hidden] {
            display: none !important;
        }

        .stats {
            grid-template-columns: repeat(5, 1fr);
        }

        .stat, .panel, .incident-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: 0 12px 32px rgba(16, 24, 40, .05);
        }

        .stat {
            padding: 16px;
        }

        .stat span {
            display: block;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        .stat strong {
            display: block;
            margin-top: 6px;
            font-size: 32px;
        }

        .main-grid {
            margin-top: 18px;
            grid-template-columns: minmax(330px, .85fr) minmax(0, 1.35fr);
            align-items: start;
        }

        .citizen-form-panel {
            grid-column: 1 / -1;
        }

        .panel {
            padding: 20px;
        }

        .panel h2 {
            margin: 0 0 14px;
            font-size: 22px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid #cfd9e0;
            border-radius: 7px;
            padding: 10px 11px;
            background: white;
            color: var(--ink);
        }

        textarea { min-height: 96px; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 13px;
        }

        .full { grid-column: 1 / -1; }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 0;
            border-radius: 7px;
            padding: 11px 14px;
            color: white;
            background: var(--brand);
            font-weight: 700;
            cursor: pointer;
        }

        .button:hover { background: var(--brand-dark); }

        .button:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .button.secondary {
            background: #334155;
        }

        .button.ghost {
            border: 1px solid var(--line);
            color: #334155;
            background: #fff;
        }

        .button.ghost:hover {
            color: var(--brand-dark);
            background: #eef7f6;
        }

        #map {
            width: 100%;
            height: 420px;
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
        }

        .map-note {
            margin-top: 9px;
            color: var(--muted);
            font-size: 13px;
        }

        .content-grid {
            margin-top: 18px;
            grid-template-columns: 1fr 1fr;
        }

        .notifications {
            margin-top: 18px;
            grid-template-columns: .85fr 1.15fr;
        }

        .incident-list {
            display: grid;
            gap: 12px;
            max-height: 680px;
            overflow: auto;
            padding-right: 4px;
        }

        .incident-card {
            padding: 15px;
        }

        .incident-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
        }

        .incident-head h3 {
            margin: 0 0 5px;
            font-size: 17px;
        }

        .meta {
            color: var(--muted);
            font-size: 13px;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin: 10px 0;
        }

        .badge {
            border-radius: 999px;
            padding: 5px 9px;
            background: #edf2f7;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
        }

        .priority-elevee { background: #fee2e2; color: #991b1b; }
        .priority-moyenne { background: #fef3c7; color: #92400e; }
        .priority-faible { background: #dcfce7; color: #166534; }
        .status-en_attente { background: #f1f5f9; color: #475569; }
        .status-en_cours { background: #dbeafe; color: #1d4ed8; }
        .status-resolu { background: #dcfce7; color: #166534; }

        .admin-form {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 8px;
            margin-top: 12px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 1fr auto auto;
            gap: 8px;
            margin-bottom: 14px;
        }

        .location-status {
            margin-top: 9px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        .flag-marker {
            width: 28px;
            height: 28px;
            position: relative;
        }

        .flag-marker::before {
            content: "";
            position: absolute;
            left: 8px;
            top: 2px;
            width: 13px;
            height: 10px;
            background: #dc2626;
            border-radius: 2px 2px 2px 0;
            box-shadow: 0 2px 7px rgba(127, 29, 29, .35);
        }

        .flag-marker::after {
            content: "";
            position: absolute;
            left: 7px;
            top: 2px;
            width: 2px;
            height: 23px;
            background: #7f1d1d;
        }

        .popup-photo {
            width: 180px;
            height: 95px;
            margin-top: 6px;
            object-fit: cover;
            border-radius: 6px;
        }

        .popup-dashboard {
            display: inline-flex;
            margin-top: 8px;
            border: 0;
            border-radius: 6px;
            padding: 7px 9px;
            color: #fff;
            background: var(--brand);
            font-weight: 700;
            cursor: pointer;
        }

        .photo {
            width: 100%;
            height: 150px;
            margin-top: 10px;
            object-fit: cover;
            border-radius: 7px;
            border: 1px solid var(--line);
        }

        .bars {
            display: grid;
            gap: 13px;
        }

        .bar-row {
            display: grid;
            gap: 5px;
        }

        .bar-line {
            height: 12px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: var(--accent);
        }

        .timeline {
            margin-top: 12px;
            padding-left: 14px;
            border-left: 2px solid #d8e1e8;
            color: var(--muted);
            font-size: 13px;
        }

        .timeline div + div { margin-top: 5px; }

        .empty {
            border: 1px dashed #b8c5ce;
            border-radius: 8px;
            padding: 24px;
            background: white;
            color: var(--muted);
            text-align: center;
        }

        .notification-list {
            display: grid;
            gap: 10px;
        }

        .notification-item {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px;
            background: #fff;
        }

        .notification-item strong {
            display: block;
            margin-bottom: 4px;
        }

        .errors {
            margin-bottom: 14px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px;
            background: #fef2f2;
            color: #991b1b;
        }

        @media (max-width: 940px) {
            .stats, .main-grid, .content-grid, .notifications {
                grid-template-columns: 1fr;
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .topbar-inner {
                align-items: flex-start;
                flex-direction: column;
            }

            .nav {
                width: 100%;
            }
        }

        @media (max-width: 620px) {
            .hero-inner { min-height: 320px; }
            .form-grid, .admin-form, .filter-form, .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand">
                <div class="brand-mark">SC</div>
                <span>Smart City Incidents</span>
            </div>
            <nav class="nav" aria-label="Navigation">
                <span class="user-chip">
                    {{ auth()->user()->name }} -
                    @if (auth()->user()->isSuperAdmin())
                        Super admin - {{ auth()->user()->department }}
                    @else
                        {{ ucfirst(auth()->user()->role) }}{{ auth()->user()->commune ? ' - '.auth()->user()->commune->name : '' }}
                    @endif
                </span>
                <a href="#admin" data-nav-view="admin">Tableau de bord</a>
                @if (! auth()->user()->canManageIncidents())
                    <a href="#signalement" data-nav-view="signalement">Nouveau signalement</a>
                @endif
                @if (auth()->user()->canManageIncidents())
                    <a href="#carte" data-nav-view="carte">Carte</a>
                @endif
                @if (auth()->user()->canManageIncidents())
                    <a href="#admin" data-nav-view="admin">Gestion incidents</a>
                    <a href="#stats" data-nav-view="stats">Statistiques</a>
                @endif
                @if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('authorities.create') }}">Gestion autorités</a>
                @endif
                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Déconnexion</button>
                </form>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-inner">
            <p class="eyebrow">Plateforme intelligente de gestion urbaine</p>
            <h1>Signaler, localiser et suivre les incidents urbains en temps réel.</h1>
            <p>
                @if (auth()->user()->canManageIncidents())
                    Une interface complète pour l'administration : carte, priorités, suivi des statuts et statistiques.
                @else
                    Un espace simple pour envoyer une plainte avec photo, description et localisation GPS obligatoire.
                @endif
            </p>
        </div>
    </section>

    <main class="wrap">
        @if (session('success'))
            <div class="notice">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                <strong>Corrige les champs suivants :</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (auth()->user()->canManageIncidents())
            <section class="grid stats" id="stats" data-view="stats">
                <div class="stat"><span>Total incidents</span><strong>{{ $stats['total'] }}</strong></div>
                <div class="stat"><span>En attente</span><strong>{{ $stats['pending'] }}</strong></div>
                <div class="stat"><span>En cours</span><strong>{{ $stats['progress'] }}</strong></div>
                <div class="stat"><span>Résolus</span><strong>{{ $stats['resolved'] }}</strong></div>
                <div class="stat"><span>Priorité élevée</span><strong>{{ $stats['high'] }}</strong></div>
            </section>
        @endif

        @if (auth()->user()->canManageIncidents())
            <section class="grid notifications" data-view="admin">
                <div class="panel">
                    <h2>Espace administration</h2>
                    @if (auth()->user()->isSuperAdmin())
                        <p class="meta">Vous supervisez les signalements de toutes les communes du département {{ auth()->user()->department }}.</p>
                    @elseif (auth()->user()->isAdmin())
                        <p class="meta">Vous recevez les signalements des citoyens de {{ auth()->user()->commune?->name ?? 'votre commune' }}, puis vous pouvez modifier les statuts et affecter les incidents aux agents de terrain.</p>
                    @else
                        <p class="meta">Vous consultez les incidents qui vous sont affectés. Quand l'intervention commence, passez le statut en cours, puis résolu une fois le problème traité.</p>
                    @endif
                </div>
                <div class="panel">
                    <h2>Notifications</h2>
                    <div class="notification-list">
                        @forelse ($notifications as $notification)
                            <div class="notification-item">
                                <strong>{{ $notification->title }}</strong>
                                <span class="meta">{{ $notification->message }}</span>
                            </div>
                        @empty
                            <div class="empty">Aucune notification pour le moment.</div>
                        @endforelse
                    </div>
                </div>
            </section>
        @endif

        <section class="grid main-grid" data-view="{{ auth()->user()->canManageIncidents() ? 'carte' : 'signalement' }}">
            @if (! auth()->user()->canManageIncidents())
                <div class="panel citizen-form-panel" id="signalement">
                    <h2>Nouveau signalement citoyen</h2>
                <form method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data" class="form-grid" id="incident-form">
                    @csrf
                    <input type="hidden" id="geolocation_verified" name="geolocation_verified" value="{{ old('geolocation_verified') }}">
                    <input type="hidden" id="geolocation_accuracy" name="geolocation_accuracy" value="{{ old('geolocation_accuracy') }}">
                    <div class="full">
                        <label for="title">Titre de l'incident</label>
                        <select id="title" name="title" required>
                            <option value="">Choisir un titre</option>
                            @foreach ($titleOptions as $titleOption)
                                <option value="{{ $titleOption }}" @selected(old('title') === $titleOption)>{{ $titleOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="full" id="custom-title-wrap" style="display: none;">
                        <label for="custom_title">Préciser l'incident</label>
                        <input id="custom_title" name="custom_title" value="{{ old('custom_title') }}" placeholder="Exemple : Feu tricolore en panne">
                    </div>
                    <div>
                        <label for="category">Catégorie</label>
                        <select id="category" name="category" required>
                            @foreach ($categories as $value => $label)
                                <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="urgency">Urgence</label>
                        <select id="urgency" name="urgency" required>
                            <option value="normal" @selected(old('urgency') === 'normal')>Normale</option>
                            <option value="urgent" @selected(old('urgency') === 'urgent')>Urgente</option>
                            <option value="critique" @selected(old('urgency') === 'critique')>Critique</option>
                        </select>
                    </div>
                    <div>
                        <label for="district">Quartier / zone</label>
                        <input id="district" name="district" value="{{ old('district', 'Centre-ville') }}" required>
                    </div>
                    <div>
                        <label for="photo">Photo</label>
                        <input id="photo" type="file" name="photo" accept="image/*">
                    </div>
                    <div class="full">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required>{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label for="latitude">Latitude</label>
                        <input id="latitude" name="latitude" value="{{ old('latitude') }}" readonly required>
                    </div>
                    <div>
                        <label for="longitude">Longitude</label>
                        <input id="longitude" name="longitude" value="{{ old('longitude') }}" readonly required>
                    </div>
                    <div class="full">
                        <button class="button ghost" type="button" id="use-location">Activer ma localisation GPS</button>
                        <button class="button" type="submit" id="submit-incident" disabled>Envoyer le signalement</button>
                        <div class="location-status" id="location-status" aria-live="polite">La localisation GPS doit être activée avant l’envoi.</div>
                    </div>
                </form>
                </div>
            @else
                <div class="panel">
                    <h2>{{ auth()->user()->isAdmin() ? 'Traitement administratif' : 'Interventions affectées' }}</h2>
                    <p class="meta">
                        {{ auth()->user()->isAdmin()
                            ? 'Affectez chaque signalement à un agent de terrain, puis suivez son passage de en attente à en cours et résolu.'
                            : 'Les signalements affectés à votre compte apparaissent dans la liste de gestion. Mettez le statut à jour pendant votre intervention.'
                        }}
                    </p>
                </div>
            @endif

            @if (auth()->user()->canManageIncidents())
                <div class="panel" id="carte">
                    <h2>Carte interactive des incidents</h2>
                    <div id="map"></div>
                    <p class="map-note">Chaque drapeau rouge représente une plainte localisée par GPS.</p>
                </div>
            @endif
        </section>

        @if (auth()->user()->canManageIncidents())
            <section class="grid content-grid" id="admin" data-view="admin">
                <div class="panel" data-dashboard-panel>
                    <h2>Tableau de bord administratif</h2>
                    <form method="GET" action="{{ route('incidents.index') }}#admin" class="filter-form">
                        <select name="status" aria-label="Filtrer par statut">
                            <option value="">Tous les statuts</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($activeFilters['status'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="category" aria-label="Filtrer par catégorie">
                            <option value="">Toutes les catégories</option>
                            @foreach ($categories as $value => $label)
                                <option value="{{ $value }}" @selected($activeFilters['category'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="button secondary" type="submit">Filtrer</button>
                        <a class="button ghost" href="{{ route('incidents.index') }}#admin">Réinitialiser</a>
                    </form>
                    @if ($incidents->isEmpty())
                        <div class="empty">Cliquez sur un drapeau rouge de la carte pour ajouter une plainte au dashboard.</div>
                    @else
                        <div class="incident-list">
                            @foreach ($incidents as $incident)
                                <article class="incident-card">
                                <div class="incident-head">
                                    <div>
                                        <h3>#{{ $incident->id }} - {{ $incident->title }}</h3>
                                        <div class="meta">{{ $incident->district }} - {{ $incident->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <span class="badge priority-{{ $incident->priority }}">{{ $incident->priorityLabel() }}</span>
                                </div>
                                <div class="badges">
                                    <span class="badge">{{ $incident->categoryLabel() }}</span>
                                    <span class="badge status-{{ $incident->status }}">{{ $incident->statusLabel() }}</span>
                                    <span class="badge">{{ $incident->urgency }}</span>
                                </div>
                                <p>{{ $incident->description }}</p>
                                @if ($incident->photo_path)
                                    <img class="photo" src="{{ asset($incident->photo_path) }}" alt="Photo de l'incident {{ $incident->id }}">
                                @endif
                                @if ($incident->assignedAgent || $incident->assigned_to)
                                    <div class="meta">Agent affecté : {{ $incident->assignedAgent?->name ?? $incident->assigned_to }}</div>
                                @endif
                                @if (auth()->user()->canManageIncidents())
                                    <form method="POST" action="{{ route('incidents.update', $incident) }}" class="admin-form">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" aria-label="Statut">
                                            @foreach ($statuses as $value => $label)
                                                <option value="{{ $value }}" @selected($incident->status === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @if (auth()->user()->isAdmin())
                                            <select name="assigned_agent_id" aria-label="Agent affecté">
                                                <option value="">Aucun agent</option>
                                                @foreach ($agents as $agent)
                                                    <option value="{{ $agent->id }}" @selected($incident->assigned_agent_id === $agent->id)>{{ $agent->name }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input value="{{ $incident->assignedAgent?->name ?? auth()->user()->name }}" disabled>
                                        @endif
                                        <button class="button secondary" type="submit">Mettre à jour</button>
                                    </form>
                                @endif
                                <div class="timeline">
                                    <div>Signalé : {{ $incident->created_at->format('d/m/Y H:i') }}</div>
                                    @if ($incident->taken_at)
                                        <div>Pris en charge : {{ $incident->taken_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                    @if ($incident->resolved_at)
                                        <div>Résolu : {{ $incident->resolved_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="panel" data-stats-detail>
                    <h2>Statistiques par catégorie</h2>
                    <div class="bars">
                    @forelse ($categoryStats as $row)
                        @php
                            $percent = $stats['total'] > 0 ? max(8, round(($row['count'] / $stats['total']) * 100)) : 0;
                        @endphp
                        <div class="bar-row">
                            <strong>{{ $row['label'] }} - {{ $row['count'] }}</strong>
                            <div class="bar-line">
                                <div class="bar-fill" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="empty">Les graphiques apparaissent après les premiers signalements.</div>
                    @endforelse
                    </div>
                </div>

                <div class="panel" data-stats-detail>
                    <h2>Zones les plus touchées</h2>
                    <div class="bars">
                    @forelse ($zoneStats as $row)
                        @php
                            $percent = $stats['total'] > 0 ? max(8, round(($row['count'] / $stats['total']) * 100)) : 0;
                        @endphp
                        <div class="bar-row">
                            <strong>{{ $row['label'] }} - {{ $row['count'] }}</strong>
                            <div class="bar-line">
                                <div class="bar-fill" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="empty">Les zones prioritaires apparaîtront après les premiers signalements.</div>
                    @endforelse
                    </div>
                </div>
            </section>
        @endif
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const incidents = @json($mapIncidents);
        const canAddToDashboard = @json(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin());
        const dashboardIncidentIds = new Set(@json($incidents->pluck('id')->values()));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const mapCenter = @json($mapCenter);
        const defaultCenter = [mapCenter.latitude, mapCenter.longitude];
        const mapElement = document.getElementById('map');
        let map = null;

        if (mapElement) {
            map = L.map('map').setView(defaultCenter, mapCenter.zoom);
            const redFlagIcon = L.divIcon({
                className: '',
                html: '<div class="flag-marker"></div>',
                iconSize: [28, 28],
                iconAnchor: [8, 25],
                popupAnchor: [8, -24]
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const markers = [];

            incidents.forEach((incident) => {
                const marker = L.marker([incident.latitude, incident.longitude], {
                    icon: redFlagIcon
                }).addTo(map);

                marker.bindPopup(`
                    <strong>${incident.title}</strong><br>
                    Catégorie : ${incident.category}<br>
                    Zone : ${incident.district}${incident.commune ? ' - ' + incident.commune : ''}<br>
                    Latitude : ${Number(incident.latitude).toFixed(7)}<br>
                    Longitude : ${Number(incident.longitude).toFixed(7)}<br>
                    Date : ${incident.date}<br>
                    Statut : ${incident.status}<br>
                    Priorité : ${incident.priorityLabel}<br>
                    ${incident.agent ? 'Agent : ' + incident.agent : 'Agent : non affecté'}<br>
                    Adresse : <span>${incident.description}</span>
                    ${incident.photo ? `<br><img class="popup-photo" src="${incident.photo}" alt="Photo du signalement">` : ''}
                    ${canAddToDashboard && !dashboardIncidentIds.has(incident.id) ? '<br><button class="popup-dashboard" type="button">Ajouter au dashboard</button>' : ''}
                `);

                marker.on('mouseover', () => {
                    marker.openPopup();
                });

                marker.on('popupopen', (event) => {
                    const button = event.popup.getElement()?.querySelector('.popup-dashboard');

                    button?.addEventListener('click', () => {
                        addIncidentToDashboard(incident, marker);
                    });
                });

                markers.push(marker);
            });

            if (markers.length > 0) {
                const group = L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.2));
            } else {
                map.setView(defaultCenter, mapCenter.zoom);
            }
        }

        function addIncidentToDashboard(incident, marker = null) {
            if (!canAddToDashboard || dashboardIncidentIds.has(incident.id)) {
                return;
            }

            dashboardIncidentIds.add(incident.id);

            fetch(incident.dashboardUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            }).then((response) => {
                if (response.ok) {
                    marker?.remove();
                    setTimeout(() => {
                        window.location.href = `${window.location.pathname}#admin`;
                        window.location.reload();
                    }, 400);
                }
            });
        }

        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const geolocationVerifiedInput = document.getElementById('geolocation_verified');
        const geolocationAccuracyInput = document.getElementById('geolocation_accuracy');
        const locationStatus = document.getElementById('location-status');
        const useLocationButton = document.getElementById('use-location');
        const submitIncidentButton = document.getElementById('submit-incident');
        const incidentForm = document.getElementById('incident-form');

        function isGpsReady() {
            return Boolean(
                geolocationVerifiedInput?.value === '1'
                && latitudeInput?.value
                && longitudeInput?.value
            );
        }

        function setSelectedPosition(lat, lng, accuracy = null, verified = false) {
            if (!latitudeInput || !longitudeInput) {
                return;
            }

            latitudeInput.value = lat.toFixed(7);
            longitudeInput.value = lng.toFixed(7);
            if (geolocationVerifiedInput) {
                geolocationVerifiedInput.value = verified ? '1' : '';
            }
            if (geolocationAccuracyInput) {
                geolocationAccuracyInput.value = accuracy !== null ? Math.round(accuracy) : '';
            }
            if (submitIncidentButton) {
                submitIncidentButton.disabled = !verified;
            }
            if (locationStatus) {
                const accuracyText = accuracy !== null ? ` Précision estimée : ${Math.round(accuracy)} m.` : '';
                locationStatus.textContent = verified
                    ? `Localisation GPS activée. Le signalement peut être envoyé.${accuracyText}`
                    : 'Activez la géolocalisation GPS pour envoyer le signalement.';
            }

            if (map) {
                const latLng = L.latLng(lat, lng);
                map.setView(latLng, 16);
            }
        }

        function explainGeolocationError(error) {
            if (!locationStatus) {
                return;
            }

            if (!navigator.geolocation) {
                locationStatus.textContent = 'Ce navigateur ne prend pas en charge la géolocalisation.';
                return;
            }

            if (!window.isSecureContext && !['localhost', '127.0.0.1'].includes(window.location.hostname)) {
                locationStatus.textContent = 'Le GPS du téléphone exige une page HTTPS ou localhost. Ouvrez le projet en HTTPS pour tester sur téléphone.';
                return;
            }

            if (!error) {
                locationStatus.textContent = 'Activez la localisation GPS pour envoyer le signalement.';
                return;
            }

            if (error.code === error.PERMISSION_DENIED) {
                locationStatus.textContent = 'Localisation refusée. Autorisez la position dans les paramètres du navigateur, puis réessayez.';
                return;
            }

            if (error.code === error.POSITION_UNAVAILABLE) {
                locationStatus.textContent = 'Position indisponible. Activez le GPS du téléphone ou vérifiez votre connexion.';
                return;
            }

            if (error.code === error.TIMEOUT) {
                locationStatus.textContent = 'La recherche GPS a pris trop de temps. Réessayez près d’une fenêtre ou avec le GPS activé.';
                return;
            }

            locationStatus.textContent = 'Impossible de récupérer la position. Activez la localisation et réessayez.';
        }

        function requestGpsLocation() {
            if (!latitudeInput || !longitudeInput || !geolocationVerifiedInput) {
                return;
            }

            if (!navigator.geolocation) {
                explainGeolocationError();
                return;
            }

            locationStatus.textContent = 'Recherche de votre position GPS...';
            if (submitIncidentButton) {
                submitIncidentButton.disabled = true;
            }

            navigator.geolocation.getCurrentPosition((position) => {
                setSelectedPosition(
                    position.coords.latitude,
                    position.coords.longitude,
                    position.coords.accuracy,
                    true
                );
            }, (error) => {
                if (geolocationVerifiedInput) {
                    geolocationVerifiedInput.value = '';
                }
                if (geolocationAccuracyInput) {
                    geolocationAccuracyInput.value = '';
                }
                if (submitIncidentButton) {
                    submitIncidentButton.disabled = true;
                }
                explainGeolocationError(error);
            }, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            });
        }

        useLocationButton?.addEventListener('click', () => {
            requestGpsLocation();
        });

        incidentForm?.addEventListener('submit', (event) => {
            if (isGpsReady()) {
                return;
            }

            event.preventDefault();
            if (submitIncidentButton) {
                submitIncidentButton.disabled = true;
            }
            if (locationStatus) {
                locationStatus.textContent = 'Localisation obligatoire : autorisez le GPS avant d’envoyer le signalement.';
            }
            requestGpsLocation();
        });

        if (isGpsReady()) {
            submitIncidentButton.disabled = false;
        } else if (latitudeInput && longitudeInput) {
            setTimeout(requestGpsLocation, 500);
        }

        const titleSelect = document.getElementById('title');
        const customTitleWrap = document.getElementById('custom-title-wrap');
        const customTitleInput = document.getElementById('custom_title');

        function toggleCustomTitle() {
            if (!titleSelect || !customTitleWrap || !customTitleInput) {
                return;
            }

            const needsCustomTitle = titleSelect.value === 'Autre incident urbain';
            customTitleWrap.style.display = needsCustomTitle ? 'block' : 'none';
            customTitleInput.required = needsCustomTitle;

            if (!needsCustomTitle) {
                customTitleInput.value = '';
            }
        }

        titleSelect?.addEventListener('change', toggleCustomTitle);
        toggleCustomTitle();

        const availableViews = Array.from(document.querySelectorAll('[data-view]'));
        const navLinks = Array.from(document.querySelectorAll('[data-nav-view]'));
        const statsDetails = Array.from(document.querySelectorAll('[data-stats-detail]'));
        const dashboardPanels = Array.from(document.querySelectorAll('[data-dashboard-panel]'));
        const defaultView = @json(auth()->user()->canManageIncidents() ? 'admin' : 'signalement');

        function showView(view) {
            const targetView = availableViews.some((element) => element.dataset.view === view)
                ? view
                : defaultView;

            availableViews.forEach((element) => {
                element.hidden = element.dataset.view !== targetView;
            });

            statsDetails.forEach((element) => {
                element.hidden = targetView !== 'stats';
            });

            dashboardPanels.forEach((element) => {
                element.hidden = targetView === 'stats';
            });

            navLinks.forEach((link) => {
                link.classList.toggle('active', link.dataset.navView === targetView);
            });

            if (targetView === 'carte' && map) {
                setTimeout(() => map.invalidateSize(), 80);
            }
        }

        navLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const view = link.dataset.navView;
                history.replaceState(null, '', `#${view}`);
                showView(view);
            });
        });

        showView(window.location.hash.replace('#', '') || defaultView);
    </script>
</body>
</html>
