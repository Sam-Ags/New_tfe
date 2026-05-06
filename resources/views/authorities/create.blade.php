<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des autorités - Smart City</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <style>
        :root {
            --ink: #17212b;
            --muted: #607080;
            --line: #dfe7ec;
            --panel: #ffffff;
            --soft: #f5f8fa;
            --brand: #0e7c7b;
            --brand-dark: #075f61;
            --danger: #c2410c;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Instrument Sans", system-ui, sans-serif;
            color: var(--ink);
            background: var(--soft);
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            border-bottom: 1px solid var(--line);
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(12px);
        }

        .topbar-inner {
            max-width: 1120px;
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
            font-weight: 800;
        }

        .brand-mark {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #fff;
            background: var(--brand);
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 14px;
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
            color: #fff;
            background:
                linear-gradient(120deg, rgba(10, 58, 64, .92), rgba(20, 93, 89, .72)),
                url("https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1600&q=80") center/cover;
        }

        .hero-inner {
            max-width: 1120px;
            margin: 0 auto;
            padding: 58px 20px 42px;
        }

        .hero p {
            max-width: 700px;
            margin: 12px 0 0;
            color: rgba(255,255,255,.86);
            font-size: 17px;
            line-height: 1.55;
        }

        h1 {
            max-width: 760px;
            margin: 0;
            font-size: clamp(34px, 5vw, 56px);
            line-height: 1.02;
            letter-spacing: 0;
        }

        .wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: 26px 20px 56px;
        }

        .grid {
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            gap: 18px;
            align-items: start;
        }

        .panel, .authority-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: 0 12px 32px rgba(16, 24, 40, .05);
        }

        .panel {
            padding: 20px;
        }

        h2 {
            margin: 0 0 14px;
            font-size: 22px;
        }

        label {
            display: block;
            margin: 13px 0 6px;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        input, select {
            width: 100%;
            border: 1px solid #cfd9e0;
            border-radius: 7px;
            padding: 11px;
            background: white;
            color: var(--ink);
        }

        input:focus, select:focus {
            outline: 3px solid rgba(14, 124, 123, .14);
            border-color: var(--brand);
        }

        .button {
            width: 100%;
            margin-top: 18px;
            border: 0;
            border-radius: 7px;
            padding: 12px 14px;
            color: #fff;
            background: var(--brand);
            font-weight: 800;
            cursor: pointer;
        }

        .button:hover { background: var(--brand-dark); }

        .notice {
            margin-bottom: 18px;
            padding: 13px 14px;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            background: #f0fdf4;
            color: #166534;
            font-weight: 700;
        }

        .errors {
            margin-bottom: 14px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px;
            background: #fef2f2;
            color: #991b1b;
        }

        .authority-list {
            display: grid;
            gap: 10px;
            max-height: 680px;
            overflow: auto;
        }

        .authority-card {
            padding: 14px;
        }

        .authority-card strong {
            display: block;
            margin-bottom: 4px;
        }

        .meta {
            color: var(--muted);
            font-size: 13px;
        }

        .badge {
            display: inline-flex;
            margin-top: 10px;
            border-radius: 999px;
            padding: 5px 9px;
            background: #edf2f7;
            color: #334155;
            font-size: 12px;
            font-weight: 800;
        }

        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
            .topbar-inner {
                align-items: flex-start;
                flex-direction: column;
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
                <span class="user-chip">{{ auth()->user()->name }} - {{ auth()->user()->isSuperAdmin() ? 'Super admin' : 'Admin' }}</span>
                <a href="{{ route('incidents.index') }}">Tableau de bord</a>
                <a href="{{ route('incidents.index') }}#admin">Gestion incidents</a>
                <a href="{{ route('incidents.index') }}#stats">Statistiques</a>
                <a href="{{ route('authorities.create') }}">Gestion autorités</a>
                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Déconnexion</button>
                </form>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-inner">
            <h1>Création des comptes autorités</h1>
            <p>Cette page est réservée aux administrateurs. Elle permet de rattacher une autorité municipale à une commune ou un département.</p>
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

        <section class="grid">
            <div class="panel">
                <h2>Nouvelle autorité</h2>
                <form method="POST" action="{{ route('authorities.store') }}">
                    @csrf
                    <label for="name">Nom complet</label>
                    <input id="name" name="name" value="{{ old('name') }}" required autofocus>

                    <label for="email">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>

                    <label for="phone">Téléphone</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}">

                    <label for="role">Rôle</label>
                    <select id="role" name="role" required>
                        <option value="admin" @selected(old('role') === 'admin')>Administrateur communal</option>
                        <option value="agent" @selected(old('role') === 'agent')>Agent technique</option>
                        <option value="super_admin" @selected(old('role') === 'super_admin')>Super administrateur départemental</option>
                    </select>

                    <label for="department">Département</label>
                    <select id="department" name="department" required>
                        <option value="">Sélectionnez le département</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department }}" @selected(old('department') === $department)>{{ $department }}</option>
                        @endforeach
                    </select>

                    <label for="commune_id">Commune</label>
                    <select id="commune_id" name="commune_id" required disabled>
                        <option value="">Choisissez d'abord un département</option>
                    </select>

                    <label for="password">Mot de passe temporaire</label>
                    <input id="password" type="password" name="password" required>

                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>

                    <button class="button" type="submit">Créer le compte autorité</button>
                </form>
            </div>

            <div class="panel">
                <h2>Services habilités</h2>
                <div class="authority-list">
                    @foreach ($authorities as $authority)
                        <article class="authority-card">
                            <strong>{{ $authority->name }}</strong>
                            <div class="meta">Contact : {{ $authority->email }}{{ $authority->phone ? ' - '.$authority->phone : '' }}</div>
                            <div class="meta">
                                @if ($authority->role === 'super_admin')
                                    Département : {{ $authority->department ?? 'Non défini' }}
                                @else
                                    {{ $authority->commune?->name ?? 'Commune non définie' }}{{ $authority->commune?->department ? ' - '.$authority->commune->department : '' }}
                                @endif
                            </div>
                            <span class="badge">
                                @if ($authority->role === 'super_admin')
                                    Super administrateur départemental
                                @elseif ($authority->role === 'admin')
                                    Administrateur communal
                                @else
                                    Agent technique
                                @endif
                            </span>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <script>
        const communes = @json($communesForSelect);
        const oldCommuneId = @json(old('commune_id'));
        const departmentSelect = document.getElementById('department');
        const communeSelect = document.getElementById('commune_id');
        const roleSelect = document.getElementById('role');

        function fillCommunes(department, selectedId = null) {
            const matchingCommunes = communes.filter((commune) => commune.department === department);
            communeSelect.innerHTML = '';

            if (matchingCommunes.length === 0) {
                communeSelect.disabled = true;
                communeSelect.add(new Option("Choisissez d'abord un département", ''));
                return;
            }

            communeSelect.disabled = false;
            communeSelect.add(new Option('Sélectionnez la commune', ''));

            matchingCommunes.forEach((commune) => {
                const option = new Option(commune.name, commune.id);
                option.selected = String(commune.id) === String(selectedId);
                communeSelect.add(option);
            });
        }

        departmentSelect.addEventListener('change', () => {
            fillCommunes(departmentSelect.value);
        });

        function toggleCommuneRequirement() {
            const isSuperAdmin = roleSelect.value === 'super_admin';
            communeSelect.required = !isSuperAdmin;

            if (isSuperAdmin) {
                communeSelect.disabled = true;
                communeSelect.innerHTML = '';
                communeSelect.add(new Option('Aucune commune pour un super administrateur', ''));
            } else if (departmentSelect.value) {
                fillCommunes(departmentSelect.value, oldCommuneId);
            }
        }

        roleSelect.addEventListener('change', toggleCommuneRequirement);

        if (oldCommuneId) {
            const selectedCommune = communes.find((commune) => String(commune.id) === String(oldCommuneId));

            if (selectedCommune) {
                departmentSelect.value = selectedCommune.department;
                fillCommunes(selectedCommune.department, oldCommuneId);
            }
        }

        toggleCommuneRequirement();
    </script>
</body>
</html>
