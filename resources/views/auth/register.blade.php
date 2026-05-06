<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription - Plateforme incidents urbains</title>
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
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Instrument Sans", system-ui, sans-serif;
            color: var(--ink);
            background:
                linear-gradient(135deg, rgba(237, 243, 244, .96), rgba(222, 236, 235, .94)),
                url("https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1500&q=80") center/cover;
        }

        .page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px;
        }

        .auth {
            width: min(520px, 100%);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 30px;
            background: var(--surface);
            box-shadow: 0 24px 60px rgba(15, 23, 42, .10);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            font-weight: 800;
            color: var(--brand-dark);
        }

        .brand-mark {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #fff;
            background: var(--brand);
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

        h1 {
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

        select {
            width: 100%;
            border: 1px solid #cbd7df;
            border-radius: 7px;
            padding: 12px;
            color: var(--ink);
            background: #fff;
        }

        input:focus, select:focus {
            outline: 3px solid rgba(15, 118, 110, .15);
            border-color: var(--brand);
        }

        button {
            width: 100%;
            margin-top: 18px;
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

        @media (max-width: 620px) {
            .page {
                padding: 18px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="auth">
            <div class="brand">
                <div class="brand-mark">SC</div>
                <div>Smart City Incidents</div>
            </div>

            <div class="tabs" aria-label="Accès utilisateur">
                <a href="{{ route('login') }}">Connexion</a>
                <a class="active" href="{{ route('register') }}">Inscription</a>
            </div>

            <h1>Inscription citoyenne</h1>
            <p>Créez votre compte pour signaler un incident, suivre son évolution et recevoir les mises à jour.</p>

            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('register.store') }}">
                @csrf
                <label for="name">Nom complet</label>
                <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>

                <label for="email">Adresse email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>

                <label for="phone">Téléphone</label>
                <input id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel">

                <label for="department">Département</label>
                <select id="department" required>
                    <option value="">Sélectionnez votre département</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>

                <label for="commune_id">Commune</label>
                <select id="commune_id" name="commune_id" required disabled>
                    <option value="">Choisissez d'abord un département</option>
                </select>

                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" autocomplete="new-password" required>

                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>

                <button type="submit">Créer mon compte</button>
            </form>
        </section>
    </main>
    <script>
        const communes = @json($communesForSelect);
        const oldCommuneId = @json(old('commune_id'));
        const departmentSelect = document.getElementById('department');
        const communeSelect = document.getElementById('commune_id');

        function fillCommunes(department, selectedId = null) {
            const matchingCommunes = communes.filter((commune) => commune.department === department);
            communeSelect.innerHTML = '';

            if (matchingCommunes.length === 0) {
                communeSelect.disabled = true;
                communeSelect.add(new Option("Choisissez d'abord un département", ''));
                return;
            }

            communeSelect.disabled = false;
            communeSelect.add(new Option('Sélectionnez votre commune', ''));

            matchingCommunes.forEach((commune) => {
                const option = new Option(commune.name, commune.id);
                option.selected = String(commune.id) === String(selectedId);
                communeSelect.add(option);
            });
        }

        departmentSelect.addEventListener('change', () => {
            fillCommunes(departmentSelect.value);
        });

        if (oldCommuneId) {
            const selectedCommune = communes.find((commune) => String(commune.id) === String(oldCommuneId));

            if (selectedCommune) {
                departmentSelect.value = selectedCommune.department;
                fillCommunes(selectedCommune.department, oldCommuneId);
            }
        }
    </script>
</body>
</html>
