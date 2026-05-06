<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Plateforme incidents urbains</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <style>
        :root {
            --ink: #16202a;
            --muted: #64748b;
            --line: #d8e2e8;
            --surface: #ffffff;
            --brand: #0f766e;
            --brand-dark: #115e59;
            --accent: #eab308;
            --soft: #f4f8f8;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Instrument Sans", system-ui, sans-serif;
            color: var(--ink);
            background: #edf3f4;
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(420px, .9fr) minmax(420px, 1fr);
        }

        .visual {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 42px;
            color: #fff;
            background:
                linear-gradient(135deg, rgba(7, 47, 52, .90), rgba(12, 95, 83, .76)),
                url("https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1500&q=80") center/cover;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 18px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #123233;
            background: #f8fafc;
            font-weight: 800;
        }

        .visual-copy {
            max-width: 560px;
        }

        .visual-copy span {
            display: inline-flex;
            margin-bottom: 16px;
            border: 1px solid rgba(255,255,255,.32);
            border-radius: 999px;
            padding: 7px 12px;
            background: rgba(255,255,255,.12);
            font-size: 13px;
            font-weight: 700;
        }

        .visual-copy h1 {
            margin: 0;
            font-size: clamp(38px, 5vw, 64px);
            line-height: 1.02;
            letter-spacing: 0;
        }

        .visual-copy p {
            max-width: 520px;
            margin: 18px 0 0;
            color: rgba(255,255,255,.86);
            font-size: 18px;
            line-height: 1.6;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .metric {
            border: 1px solid rgba(255,255,255,.24);
            border-radius: 8px;
            padding: 14px;
            background: rgba(255,255,255,.12);
        }

        .metric strong {
            display: block;
            font-size: 24px;
        }

        .metric small {
            color: rgba(255,255,255,.78);
        }

        .auth-side {
            display: grid;
            place-items: center;
            padding: 34px;
        }

        .auth {
            width: min(460px, 100%);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 30px;
            background: var(--surface);
            box-shadow: 0 24px 60px rgba(15, 23, 42, .10);
        }

        .tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 24px;
            padding: 5px;
            border-radius: 8px;
            background: #eef4f4;
        }

        .tabs a {
            display: inline-flex;
            justify-content: center;
            border-radius: 7px;
            padding: 10px 12px;
            color: #475569;
            text-decoration: none;
            font-weight: 700;
        }

        .tabs .active {
            color: var(--brand-dark);
            background: #fff;
            box-shadow: 0 1px 5px rgba(15, 23, 42, .08);
        }

        h2 {
            margin: 0 0 8px;
            font-size: 28px;
            letter-spacing: 0;
        }

        p {
            margin: 0 0 22px;
            color: var(--muted);
            line-height: 1.5;
        }

        label {
            display: block;
            margin: 14px 0 6px;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        input {
            width: 100%;
            border: 1px solid #cbd7df;
            border-radius: 7px;
            padding: 12px;
            color: var(--ink);
            background: #fff;
        }

        input:focus {
            outline: 3px solid rgba(15, 118, 110, .15);
            border-color: var(--brand);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 14px 0 18px;
            color: #334155;
            font-weight: 600;
        }

        .remember input {
            width: auto;
        }

        button {
            width: 100%;
            border: 0;
            border-radius: 7px;
            padding: 13px 15px;
            color: #fff;
            background: var(--brand);
            font-weight: 800;
            cursor: pointer;
        }

        button:hover {
            background: var(--brand-dark);
        }

        .error {
            margin-bottom: 14px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 11px;
            color: #991b1b;
            background: #fef2f2;
            font-weight: 600;
        }

        .footnote {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }

        @media (max-width: 900px) {
            .page {
                grid-template-columns: 1fr;
            }

            .visual {
                min-height: 360px;
            }

            .metrics {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="visual" aria-label="Présentation de la plateforme">
            <div class="brand">
                <div class="brand-mark">SC</div>
                <div>Smart City Incidents</div>
            </div>

            <div class="visual-copy">
                <span>Gestion urbaine participative</span>
                <h1>Une ville plus réactive face aux incidents.</h1>
                <p>Centralisez les signalements, suivez les interventions et priorisez les incidents critiques à partir d'une plateforme unique.</p>
            </div>

            <div class="metrics" aria-label="Indicateurs de la plateforme">
                <div class="metric">
                    <strong>24h/24</strong>
                    <small>Signalements citoyens</small>
                </div>
                <div class="metric">
                    <strong>Carte</strong>
                    <small>Localisation instantanée</small>
                </div>
                <div class="metric">
                    <strong>Priorité</strong>
                    <small>Aide à la décision</small>
                </div>
            </div>
        </section>

        <section class="auth-side">
            <div class="auth">
                <div class="tabs" aria-label="Accès utilisateur">
                    <a class="active" href="{{ route('login') }}">Connexion</a>
                    <a href="{{ route('register') }}">Inscription</a>
                </div>

                <h2>Connexion</h2>
                <p>Connectez-vous pour accéder à votre espace de suivi ou au tableau de bord administratif.</p>

                @if ($errors->any())
                    <div class="error">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <label for="email">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>

                    <label for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" autocomplete="current-password" required>

                    <label class="remember">
                        <input type="checkbox" name="remember">
                        Rester connecté
                    </label>

                    <button type="submit">Se connecter</button>
                </form>

                <div class="footnote">Accès réservé aux citoyens inscrits et aux services municipaux autorisés.</div>
            </div>
        </section>
    </main>
</body>
</html>
