<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signaler un incident - Smart City</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <style>
        :root {
            --ink: #17212b;
            --muted: #607080;
            --line: #dfe7ec;
            --panel: #ffffff;
            --brand: #0e7c7b;
            --brand-dark: #075f61;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: "Instrument Sans", system-ui, sans-serif;
            color: var(--ink);
            background:
                linear-gradient(120deg, rgba(245, 248, 250, .94), rgba(226, 241, 239, .94)),
                url("https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1600&q=80") center/cover fixed;
        }

        .topbar {
            border-bottom: 1px solid var(--line);
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(12px);
        }

        .topbar-inner {
            max-width: 940px;
            margin: 0 auto;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
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

        .admin-link {
            min-height: 36px;
            border-radius: 7px;
            padding: 8px 11px;
            color: #334155;
            background: #f1f5f9;
            text-decoration: none;
            font-weight: 700;
        }

        .wrap {
            max-width: 940px;
            margin: 0 auto;
            padding: 44px 20px;
        }

        .panel {
            max-width: 560px;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 24px;
            background: var(--panel);
            box-shadow: 0 18px 50px rgba(16, 24, 40, .10);
        }

        h1 {
            margin: 0 0 8px;
            font-size: clamp(30px, 5vw, 44px);
            line-height: 1.05;
            letter-spacing: 0;
        }

        .welcome {
            margin: 0 0 8px;
            color: var(--brand-dark);
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
        }

        p {
            margin: 0 0 20px;
            color: var(--muted);
            line-height: 1.5;
        }

        label {
            display: block;
            margin: 15px 0 6px;
            color: #334155;
            font-size: 13px;
            font-weight: 800;
        }

        input, select {
            width: 100%;
            border: 1px solid #cfd9e0;
            border-radius: 7px;
            padding: 12px;
            background: #fff;
            color: var(--ink);
        }

        input:focus, select:focus {
            outline: 3px solid rgba(14, 124, 123, .16);
            border-color: var(--brand);
        }

        .button {
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

        .button:hover { background: var(--brand-dark); }

        .button:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .button.secondary {
            color: var(--brand-dark);
            background: #e6f3f1;
        }

        .button.secondary:hover { background: #d4ebe8; }

        .button-content {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
        }

        .spinner {
            width: 17px;
            height: 17px;
            border: 2px solid rgba(7, 95, 97, .25);
            border-top-color: var(--brand-dark);
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        .spinner[hidden] {
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .notice {
            margin-bottom: 16px;
            padding: 13px 14px;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            background: #f0fdf4;
            color: #166534;
            font-weight: 700;
        }

        .errors {
            margin-bottom: 16px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 13px 14px;
            background: #fef2f2;
            color: #991b1b;
        }

        .field-note, .location-status {
            margin-top: 6px;
            color: var(--muted);
            font-size: 13px;
        }

        .location-status {
            font-weight: 700;
        }

        @media (max-width: 620px) {
            .topbar-inner {
                align-items: flex-start;
                flex-direction: column;
            }

            .wrap {
                padding: 24px 16px;
            }

            .panel {
                padding: 20px;
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
            <a class="admin-link" href="{{ route('admin.login') }}">Connexion administration</a>
        </div>
    </header>

    <main class="wrap">
        <section class="panel">
            <p class="welcome">Bienvenue sur Smart City Incident</p>
            <h1>Signaler un incident</h1>

            @if (session('success'))
                <div class="notice">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="errors">
                    <strong>Corrigez les champs suivants :</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data" id="public-incident-form">
                @csrf
                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                <input type="hidden" id="geolocation_verified" name="geolocation_verified" value="{{ old('geolocation_verified') }}">
                <input type="hidden" id="geolocation_accuracy" name="geolocation_accuracy" value="{{ old('geolocation_accuracy') }}">
                <input type="hidden" id="location_country" name="location_country" value="{{ old('location_country') }}">
                <input type="hidden" id="location_city" name="location_city" value="{{ old('location_city') }}">
                <input type="hidden" id="location_zone" name="location_zone" value="{{ old('location_zone') }}">
                <input type="hidden" id="location_address" name="location_address" value="{{ old('location_address') }}">

                <label for="title">Titre de l’incident</label>
                <select id="title" name="title" required>
                    <option value="">Choisir un titre</option>
                    @foreach ($titleOptions as $titleOption)
                        <option value="{{ $titleOption }}" @selected(old('title') === $titleOption)>{{ $titleOption }}</option>
                    @endforeach
                </select>

                <div id="custom-title-wrap" style="display: none;">
                    <label for="custom_title">Préciser l’incident</label>
                    <input id="custom_title" name="custom_title" value="{{ old('custom_title') }}" placeholder="Exemple : Feu tricolore en panne">
                </div>

                <label for="urgency">Urgence</label>
                <select id="urgency" name="urgency" required>
                    <option value="normal" @selected(old('urgency') === 'normal')>Normale</option>
                    <option value="urgent" @selected(old('urgency') === 'urgent')>Urgente</option>
                    <option value="critique" @selected(old('urgency') === 'critique')>Critique</option>
                </select>

                <label for="photo">Photo</label>
                <input id="photo" type="file" name="photo" accept="image/*" capture="environment" required>
                <div class="field-note">Sur téléphone, l’appareil photo peut s’ouvrir directement.</div>

                <button class="button secondary" type="button" id="use-location">
                    <span class="button-content">
                        <span class="spinner" id="location-spinner" hidden aria-hidden="true"></span>
                        <span id="location-button-label">Activer ma localisation</span>
                    </span>
                </button>
                <div class="location-status" id="location-status" aria-live="polite">Cliquez sur “Activer ma localisation” pour autoriser la position.</div>
                <button class="button" type="submit" id="submit-incident" disabled>Envoyer le signalement</button>
            </form>
        </section>
    </main>

    <script>
        const titleSelect = document.getElementById('title');
        const customTitleWrap = document.getElementById('custom-title-wrap');
        const customTitleInput = document.getElementById('custom_title');
        const form = document.getElementById('public-incident-form');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const geolocationVerifiedInput = document.getElementById('geolocation_verified');
        const geolocationAccuracyInput = document.getElementById('geolocation_accuracy');
        const locationCountryInput = document.getElementById('location_country');
        const locationCityInput = document.getElementById('location_city');
        const locationZoneInput = document.getElementById('location_zone');
        const locationAddressInput = document.getElementById('location_address');
        const locationStatus = document.getElementById('location-status');
        const useLocationButton = document.getElementById('use-location');
        const locationSpinner = document.getElementById('location-spinner');
        const locationButtonLabel = document.getElementById('location-button-label');
        const submitButton = document.getElementById('submit-incident');
        const supportedCommunes = @json($supportedCommunes);
        let locationCommuneIdentified = false;

        function toggleCustomTitle() {
            const needsCustomTitle = titleSelect.value === 'Autre incident urbain';
            customTitleWrap.style.display = needsCustomTitle ? 'block' : 'none';
            customTitleInput.required = needsCustomTitle;

            if (!needsCustomTitle) {
                customTitleInput.value = '';
            }
        }

        function isLocationReady() {
            return locationCommuneIdentified && geolocationVerifiedInput.value === '1' && latitudeInput.value && longitudeInput.value;
        }

        function normalizeLocationText(value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase();
        }

        function findSupportedCommune(...values) {
            const haystack = normalizeLocationText(values.filter(Boolean).join(' '));

            return supportedCommunes.find((commune) => {
                const needle = normalizeLocationText(commune);

                return haystack === needle || haystack.includes(needle);
            });
        }

        function setLocationSearching(isSearching, label = 'Recherche en cours...') {
            useLocationButton.disabled = isSearching;
            locationSpinner.hidden = !isSearching;
            locationButtonLabel.textContent = isSearching ? label : 'Activer ma localisation';
        }

        function setCommuneStatus(commune) {
            locationCommuneIdentified = Boolean(commune);
            submitButton.disabled = !isLocationReady();
            setLocationSearching(false);

            if (commune) {
                locationStatus.textContent = `Localisation activée. La plainte sera envoyée à la commune de ${commune}.`;
                return;
            }

            locationStatus.textContent = 'Zone non prise en charge. Le signalement ne peut pas être envoyé depuis cette position.';
        }

        function setLocation(position) {
            latitudeInput.value = position.coords.latitude.toFixed(7);
            longitudeInput.value = position.coords.longitude.toFixed(7);
            geolocationVerifiedInput.value = '1';
            geolocationAccuracyInput.value = Math.round(position.coords.accuracy);
            locationCommuneIdentified = false;
            submitButton.disabled = true;
            setLocationSearching(true, 'Vérification de la zone...');
            locationStatus.textContent = 'Localisation trouvée. Vérification de la zone...';
            resolveReadableLocation(latitudeInput.value, longitudeInput.value);
        }

        async function resolveReadableLocation(latitude, longitude) {
            locationCountryInput.value = '';
            locationCityInput.value = '';
            locationZoneInput.value = '';
            locationAddressInput.value = '';

            try {
                const url = new URL('https://nominatim.openstreetmap.org/reverse');
                url.searchParams.set('format', 'jsonv2');
                url.searchParams.set('lat', latitude);
                url.searchParams.set('lon', longitude);
                url.searchParams.set('zoom', '18');
                url.searchParams.set('addressdetails', '1');
                url.searchParams.set('accept-language', 'fr');

                const response = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Adresse introuvable');
                }

                const data = await response.json();
                const address = data.address || {};

                locationCountryInput.value = address.country || '';
                locationCityInput.value = address.city
                    || address.town
                    || address.village
                    || address.municipality
                    || address.county
                    || address.state
                    || '';
                locationZoneInput.value = address.neighbourhood
                    || address.suburb
                    || address.quarter
                    || address.city_district
                    || address.district
                    || address.road
                    || '';
                locationAddressInput.value = data.display_name || '';
                setCommuneStatus(findSupportedCommune(
                    locationCityInput.value,
                    locationZoneInput.value,
                    locationAddressInput.value
                ));
            } catch (error) {
                locationCountryInput.value = '';
                locationCityInput.value = '';
                locationZoneInput.value = '';
                locationAddressInput.value = '';
                setCommuneStatus(null);
            }
        }

        function resetLocationState() {
            locationCommuneIdentified = false;
            geolocationVerifiedInput.value = '';
            geolocationAccuracyInput.value = '';
            locationCountryInput.value = '';
            locationCityInput.value = '';
            locationZoneInput.value = '';
            locationAddressInput.value = '';
            submitButton.disabled = true;
        }

        function showLocationError(error = null) {
            resetLocationState();
            setLocationSearching(false);

            if (!window.isSecureContext && !['localhost', '127.0.0.1'].includes(window.location.hostname)) {
                locationStatus.textContent = 'Localisation bloquée : sur téléphone, utilisez un lien HTTPS comme ngrok. Un lien http://adresse-ip:8000 ne peut pas demander le GPS.';
                return;
            }

            if (!navigator.geolocation) {
                locationStatus.textContent = 'Ce navigateur ne prend pas en charge la localisation.';
                return;
            }

            if (error?.code === error.PERMISSION_DENIED) {
                locationStatus.textContent = 'Localisation refusée. Autorisez la position dans le navigateur puis réessayez.';
                return;
            }

            if (error?.code === error.POSITION_UNAVAILABLE) {
                locationStatus.textContent = 'Position indisponible. Activez le GPS du téléphone puis réessayez.';
                return;
            }

            if (error?.code === error.TIMEOUT) {
                locationStatus.textContent = 'La recherche GPS prend trop de temps. Activez la localisation du téléphone puis réessayez.';
                return;
            }

            locationStatus.textContent = 'Activez la localisation pour envoyer le signalement.';
        }

        function requestApproximateLocation() {
            setLocationSearching(true, 'Recherche approximative...');
            locationStatus.textContent = 'GPS précis trop lent. Recherche d’une position approximative...';

            navigator.geolocation.getCurrentPosition(setLocation, showLocationError, {
                enableHighAccuracy: false,
                timeout: 30000,
                maximumAge: 600000
            });
        }

        function requestLocation() {
            if (!navigator.geolocation) {
                showLocationError();
                return;
            }

            resetLocationState();
            setLocationSearching(true);
            locationStatus.textContent = 'Recherche de votre position GPS...';

            navigator.geolocation.getCurrentPosition(setLocation, (error) => {
                if (error?.code === error.TIMEOUT || error?.code === error.POSITION_UNAVAILABLE) {
                    requestApproximateLocation();
                    return;
                }

                showLocationError(error);
            }, {
                enableHighAccuracy: true,
                timeout: 30000,
                maximumAge: 0
            });
        }

        titleSelect.addEventListener('change', toggleCustomTitle);
        useLocationButton.addEventListener('click', requestLocation);
        form.addEventListener('submit', (event) => {
            if (isLocationReady()) {
                return;
            }

            event.preventDefault();
            if (geolocationVerifiedInput.value === '1' && latitudeInput.value && longitudeInput.value) {
                locationStatus.textContent = 'Zone non prise en charge. Le signalement ne peut pas être envoyé depuis cette position.';
                return;
            }

            locationStatus.textContent = 'Localisation obligatoire : autorisez la position avant l’envoi.';
            requestLocation();
        });

        toggleCustomTitle();

        if (!window.isSecureContext && !['localhost', '127.0.0.1'].includes(window.location.hostname)) {
            showLocationError();
        }
    </script>
</body>
</html>
