<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des autorités - Smart City</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=optional" rel="stylesheet">
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

        .admin-main {
            min-height: 100vh;
            width: calc(100% - 16rem);
            margin-left: 16rem;
            min-width: 0;
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
            color: #fff !important;
        }

        .admin-sidebar a.bg-primary {
            background: #F4A261 !important;
            color: #102A43 !important;
        }

        .admin-sidebar a.bg-primary .material-symbols-outlined {
            color: #102A43 !important;
        }

        .admin-sidebar form {
            border-color: rgba(255, 255, 255, .18);
        }

        @media (max-width: 1023px) {
            .admin-sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .admin-main {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
    @include('partials.theme-head')
</head>
<body class="min-h-screen bg-surface font-sans text-on-surface">
    <aside class="admin-sidebar fixed left-0 top-0 z-30 flex h-screen w-64 flex-col border-r border-outline-variant bg-surface-container-low p-6">
        <div class="mb-8 flex items-center gap-3">
            <span class="grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-full bg-white shadow-sm ring-1 ring-outline-variant">
                <img class="h-16 w-16 object-contain" src="{{ asset('images/smart-city-incidents-logo.png') }}" alt="Smart City Incidents">
            </span>
            <span class="text-sm font-bold leading-tight text-primary">SmartCity<br>Incident</span>
        </div>

        <div class="mb-10 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-white">
                <img class="h-full w-full object-contain p-1" src="{{ asset('images/smart-city-incidents-logo.png') }}" alt="">
            </div>
            <div>
                <p class="font-semibold text-on-surface">{{ auth()->user()->name }}</p>
                <p class="text-sm text-secondary">{{ auth()->user()->commune?->name ?: 'Admin' }}</p>
            </div>
        </div>

        <nav class="flex flex-1 flex-col gap-2">
            <a class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="{{ route('incidents.index') }}#admin">
                <span class="material-symbols-outlined">dashboard</span>
                Tableau de bord
            </a>
            <a class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="{{ route('incidents.index') }}#carte">
                <span class="material-symbols-outlined">map</span>
                Carte
            </a>
            <a class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="{{ route('incidents.index') }}#incidents">
                <span class="material-symbols-outlined">report_problem</span>
                Gestion incidents
            </a>
            <a class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-surface-container hover:text-primary" href="{{ route('incidents.index') }}#stats">
                <span class="material-symbols-outlined">bar_chart</span>
                Statistiques
            </a>
            <a class="flex items-center gap-3 rounded-lg bg-primary px-3 py-3 text-sm font-semibold text-white" href="{{ route('authorities.create') }}">
                <span class="material-symbols-outlined">account_balance</span>
                Gestion autorités
            </a>
        </nav>

        <form class="border-t border-outline-variant pt-4" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-secondary transition hover:bg-red-50 hover:text-error" type="submit">
                <span class="material-symbols-outlined">logout</span>
                Déconnexion
            </button>
        </form>
    </aside>

    <main class="admin-main px-6 py-10">
        <div class="mb-6 flex justify-end">
            @include('partials.theme-toggle')
        </div>
        <section class="mb-8">
            <span class="mb-3 block text-xs font-bold uppercase tracking-[0.2em] text-secondary">Autorités municipales</span>
            <h1 class="text-4xl font-semibold leading-tight text-primary">Gestion des comptes autorités</h1>
            <p class="mt-3 max-w-3xl text-on-surface-variant">
                Créez les comptes des administrateurs communaux et des agents techniques.
            </p>
        </section>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 font-semibold text-emerald-800">{{ session('success') }}</div>
        @endif

        @if (session('created_authority'))
            <div class="mb-6 rounded-xl border border-primary/20 bg-surface-container-lowest p-5 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-secondary">Compte créé</p>
                        <h2 class="mt-1 text-2xl font-semibold text-primary">{{ session('created_authority.name') }}</h2>
                        <p class="mt-1 text-sm text-secondary">{{ session('created_authority.email') }}{{ session('created_authority.phone') ? ' - '.session('created_authority.phone') : '' }}</p>
                    </div>
                    <div class="rounded-xl bg-primary px-5 py-4 text-white">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-white/75">
                            {{ session('created_authority.role') === 'agent' ? 'Identifiant agent' : 'Identifiant compte' }}
                        </p>
                        <p class="mt-1 text-2xl font-bold tracking-wide">{{ session('created_authority.identifier') }}</p>
                    </div>
                </div>
            </div>
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

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="xl:col-span-5">
                <form method="POST" action="{{ route('authorities.store') }}" class="rounded-xl border border-outline-variant bg-surface-container-lowest p-6 shadow-sm">
                    @csrf
                    <div class="mb-6 flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary text-white">
                            <span class="material-symbols-outlined">person_add</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-semibold text-on-surface">Nouvelle autorité</h2>
                            <p class="text-sm text-secondary">Rattachement automatique à {{ auth()->user()->commune?->name ?: 'votre commune' }}.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="name">Nom complet</label>
                            <input class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="name" name="name" value="{{ old('name') }}" required autofocus>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="email">Adresse email</label>
                            <input class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="email" type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="phone">Téléphone</label>
                            <input class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="phone" name="phone" value="{{ old('phone') }}">
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="role">Rôle</label>
                            <select class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="role" name="role" required>
                                <option value="admin" @selected(old('role') === 'admin')>Administrateur communal</option>
                                <option value="agent" @selected(old('role') === 'agent')>Agent technique</option>
                            </select>
                        </div>
                        <div class="rounded-lg border border-outline-variant bg-surface-container-low p-4 text-sm text-on-surface-variant">
                            Ce compte sera automatiquement rattaché à
                            <strong class="text-primary">{{ auth()->user()->commune?->name ?: 'votre commune' }}</strong>
                            @if (auth()->user()->department)
                                dans le département <strong class="text-primary">{{ auth()->user()->department }}</strong>.
                            @endif
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="password">Mot de passe temporaire</label>
                                <input class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="password" type="password" name="password" required>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="password_confirmation">Confirmation</label>
                                <input class="h-12 w-full rounded border-outline-variant focus:border-primary focus:ring-primary/20" id="password_confirmation" type="password" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>

                    <button class="mt-6 flex w-full items-center justify-center gap-2 rounded bg-primary px-5 py-4 text-sm font-bold uppercase tracking-wider text-white transition hover:bg-primary-container" type="submit">
                        Créer le compte
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                </form>
            </div>

            <div class="xl:col-span-7">
                <div class="rounded-xl border border-outline-variant bg-surface-container-lowest shadow-sm">
                    <div class="border-b border-outline-variant bg-surface-container-low/60 p-5">
                        <h2 class="text-2xl font-semibold text-on-surface">Services habilités</h2>
                        <p class="mt-1 text-sm text-secondary">Comptes actifs dans le périmètre autorisé.</p>
                    </div>
                    <div class="max-h-[760px] divide-y divide-outline-variant overflow-auto">
                        @forelse ($authorities as $authority)
                            <article class="grid gap-4 p-5 md:grid-cols-[1fr_auto]">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-surface-container text-primary">
                                        <span class="material-symbols-outlined">badge</span>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-on-surface">{{ $authority->name }}</h3>
                                        <p class="mt-1 inline-flex rounded-full bg-primary px-3 py-1 text-xs font-bold tracking-wide text-white">
                                            {{ $authority->accountIdentifier() }}
                                        </p>
                                        <p class="mt-1 text-sm text-secondary">{{ $authority->email }}{{ $authority->phone ? ' - '.$authority->phone : '' }}</p>
                                        <p class="mt-1 text-sm text-secondary">
                                            {{ $authority->commune?->name ?: 'Commune non définie' }}{{ $authority->commune?->department ? ' - '.$authority->commune->department : '' }}
                                        </p>
                                    </div>
                                </div>
                                <span class="h-fit rounded-full bg-surface-container px-3 py-1 text-xs font-bold text-primary">
                                    @if ($authority->role === 'admin')
                                        Administrateur communal
                                    @else
                                        Agent technique
                                    @endif
                                </span>
                            </article>
                        @empty
                            <div class="p-8 text-center text-secondary">Aucune autorité enregistrée.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
