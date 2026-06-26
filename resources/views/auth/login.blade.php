<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Smart City Incidents</title>
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
    </style>
    @include('partials.theme-head')
</head>
<body class="flex min-h-screen flex-col bg-surface font-sans text-on-surface">
    <header class="flex w-full items-center justify-between border-b border-outline-variant bg-surface px-6 py-3 md:px-8">
        <a class="flex items-center gap-3 no-underline" href="{{ route('incidents.public.home') }}">
            <span class="grid h-16 w-16 place-items-center overflow-hidden rounded-full bg-white shadow-sm ring-1 ring-outline-variant">
                <img class="h-14 w-14 object-contain" src="{{ asset('images/smart-city-incidents-logo-256.png') }}" alt="Smart City Incidents">
            </span>
            <span class="hidden text-xl font-semibold text-primary sm:inline">SmartCity Incident</span>
        </a>
        <div class="flex items-center gap-3">
            @include('partials.theme-toggle')
            <a class="inline-flex items-center gap-2 rounded-full border border-outline-variant px-4 py-2 text-sm font-semibold text-primary hover:bg-surface-container-low" href="{{ route('incidents.public.home') }}">
                <span class="material-symbols-outlined text-lg">home</span>
                Accueil
            </a>
        </div>
    </header>

    <main class="flex flex-1 items-center justify-center p-6">
        <div class="grid w-full max-w-6xl overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest shadow-sm md:grid-cols-2">
            <section class="relative hidden min-h-[660px] overflow-hidden bg-primary-container md:block">
                <div class="absolute inset-0 opacity-25">
                    <img class="h-full w-full object-cover" alt="Ville connectée" src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1500&q=80">
                </div>
                <div class="relative flex h-full flex-col justify-end p-12 text-white">
                    <span class="mb-4 text-xs font-bold uppercase tracking-[0.2em] opacity-80">Smart City Incidents</span>
                    <h1 class="text-4xl font-semibold leading-tight">Connectez-vous à votre espace.</h1>
                    <p class="mt-5 max-w-md text-base leading-7 text-white/85">
                        Retrouvez les outils et informations liés à votre compte en toute simplicité.
                    </p>
                    <div class="mt-10 flex gap-6 text-sm font-semibold">
                        <span class="inline-flex items-center gap-2"><span class="material-symbols-outlined">verified_user</span> Accès sécurisé</span>
                        <span class="inline-flex items-center gap-2"><span class="material-symbols-outlined">person</span> Espace personnalisé</span>
                    </div>
                </div>
            </section>

            <section class="flex flex-col justify-center p-6 md:p-12">
                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-error-container bg-red-50 px-4 py-3 font-semibold text-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-[0.12em] text-on-surface-variant" for="email">Adresse email</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-secondary">mail</span>
                            <input class="h-12 w-full rounded border border-outline-variant bg-white pl-11 pr-4 text-base focus:border-primary focus:ring-2 focus:ring-primary/20" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="adresse@email.com">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-[0.12em] text-on-surface-variant" for="password">Mot de passe</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-secondary">lock</span>
                            <input class="h-12 w-full rounded border border-outline-variant bg-white pl-11 pr-12 text-base focus:border-primary focus:ring-2 focus:ring-primary/20" id="password" type="password" name="password" autocomplete="current-password" required placeholder="Mot de passe">
                            <button class="absolute right-3 top-1/2 inline-flex -translate-y-1/2 items-center justify-center text-secondary transition hover:text-primary" type="button" data-password-toggle="password" aria-label="Afficher le mot de passe">
                                <span class="material-symbols-outlined text-xl" data-password-toggle-icon>visibility</span>
                            </button>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm font-medium text-secondary">
                        <input class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary" type="checkbox" name="remember">
                        Rester connecté
                    </label>

                    <button class="flex w-full items-center justify-center gap-2 rounded bg-primary px-6 py-4 text-sm font-bold uppercase tracking-wider text-white transition hover:bg-primary-container" type="submit">
                        Continuer
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                </form>

                <div class="mt-10 border-t border-outline-variant pt-6 text-center text-sm leading-6 text-secondary">
                    Après connexion, l’espace adapté à votre profil s’ouvrira automatiquement.
                </div>
            </section>
        </div>
    </main>
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            const input = document.getElementById(button.dataset.passwordToggle);
            const icon = button.querySelector('[data-password-toggle-icon]');

            button.addEventListener('click', () => {
                if (!input) return;

                const shouldShow = input.type === 'password';
                input.type = shouldShow ? 'text' : 'password';
                button.setAttribute('aria-label', shouldShow ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
                if (icon) {
                    icon.textContent = shouldShow ? 'visibility_off' : 'visibility';
                }
            });
        });
    </script>
</body>
</html>
