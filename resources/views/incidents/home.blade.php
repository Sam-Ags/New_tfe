<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartCity Incident</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=optional" rel="stylesheet">
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
                        'on-surface': '#102A43',
                        'on-surface-variant': '#334E68',
                        outline: '#829AB1',
                        'outline-variant': '#BCCCDC',
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

        .home-shell {
            background: linear-gradient(180deg, #F4F8FB 0%, #EAF3F8 100%);
        }

        .home-logo {
            box-shadow: 0 24px 54px rgba(16, 42, 67, .16);
        }

        .home-action {
            box-shadow: 0 14px 28px rgba(0, 95, 115, .22);
        }

        .home-title {
            font-size: 2rem;
            line-height: 1.22;
            text-wrap: balance;
        }

        html[data-theme="dark"] .home-shell {
            background: linear-gradient(180deg, #08131d 0%, #101f2b 100%);
        }

        @media (min-width: 640px) {
            .home-title {
                font-size: 3rem;
                line-height: 1.12;
            }
        }
    </style>
    @include('partials.theme-head')
</head>
<body class="home-shell min-h-screen font-sans text-on-surface">
    <header class="mx-auto flex w-full max-w-7xl items-center justify-end px-6 py-5">
        <div class="flex items-center gap-3">
            @include('partials.theme-toggle')
            <a class="rounded-full bg-primary px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-primary-container" href="{{ route('login') }}">
                Connexion
            </a>
        </div>
    </header>

    <main class="mx-auto grid min-h-[calc(100vh-5.5rem)] w-full max-w-7xl place-items-center px-6 pb-12">
        <section class="flex w-full max-w-3xl flex-col items-center text-center">
            <p class="mb-6 text-2xl font-extrabold text-primary sm:text-3xl">SmartCity</p>

            <div class="home-logo grid h-52 w-52 place-items-center overflow-hidden rounded-full bg-white ring-1 ring-outline-variant sm:h-64 sm:w-64">
                <img class="h-40 w-40 object-contain sm:h-52 sm:w-52" src="{{ asset('images/smart-city-incidents-logo-256.png') }}" alt="SmartCity Incident">
            </div>

            <h1 class="home-title mt-8 max-w-3xl font-extrabold text-on-surface">
                Ensemble, construisons une ville plus intelligente et plus sûre.
            </h1>

            <a class="home-action mt-8 inline-flex min-h-14 items-center justify-center gap-3 rounded-full bg-primary px-8 py-4 text-base font-bold text-white transition hover:bg-primary-container focus:outline-none focus:ring-4 focus:ring-primary/20 sm:text-lg" href="{{ route('incidents.public.create') }}">
                <span class="material-symbols-outlined">add_location_alt</span>
                Signaler un incident
            </a>
        </section>
    </main>
</body>
</html>
