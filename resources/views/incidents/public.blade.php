<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signaler un incident - Smart City</title>
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
                    borderRadius: {
                        DEFAULT: '0.25rem',
                        lg: '0.5rem',
                        xl: '0.75rem',
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .location-spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(0, 52, 52, .22);
            border-top-color: #003434;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        .location-spinner[hidden] {
            display: none;
        }

        .gps-widget {
            position: relative;
            overflow: hidden;
            border: 1px solid #BCCCDC;
            border-radius: 999px;
            padding: .85rem 1rem .85rem .85rem;
            background: linear-gradient(135deg, #FFFFFF 0%, #EAF3F8 100%);
            box-shadow: 0 12px 28px rgba(16, 42, 67, .10);
        }

        .gps-target {
            position: relative;
            display: grid;
            width: 4.25rem;
            height: 4.25rem;
            min-width: 4.25rem;
            place-items: center;
            border-radius: 999px;
            color: #FFFFFF;
            background: #005F73;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .22), 0 10px 18px rgba(0, 95, 115, .22);
        }

        .gps-target::before {
            content: "";
            position: absolute;
            inset: -.45rem;
            border: 1px dashed rgba(0, 95, 115, .32);
            border-radius: inherit;
        }

        .gps-target::after {
            content: "";
            position: absolute;
            right: .6rem;
            bottom: .6rem;
            width: .72rem;
            height: .72rem;
            border: 2px solid #FFFFFF;
            border-radius: 999px;
            background: #2A9D8F;
        }

        .gps-widget[data-location-state="searching"] .gps-target::before {
            animation: gps-pulse 1s ease-in-out infinite;
        }

        .gps-widget[data-location-state="ready"] .gps-target {
            background: #067647;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .22), 0 10px 18px rgba(6, 118, 71, .20);
        }

        .gps-widget[data-location-state="error"] .gps-target {
            background: #B42318;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .22), 0 10px 18px rgba(180, 35, 24, .18);
        }

        .gps-action {
            border-radius: 999px;
            box-shadow: 0 8px 16px rgba(0, 95, 115, .18);
        }

        .location-spinner:not([hidden]) ~ .location-button-icon {
            display: none;
        }

        html[data-theme="dark"] .gps-widget {
            border-color: #365568;
            background: linear-gradient(135deg, #101f2b 0%, #152937 100%);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .26);
        }

        @media (max-width: 640px) {
            .gps-widget {
                border-radius: 1.5rem;
                padding: 1rem;
            }

            .gps-target {
                width: 3.75rem;
                height: 3.75rem;
                min-width: 3.75rem;
            }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes gps-pulse {
            0%, 100% {
                transform: scale(.96);
                opacity: .55;
            }

            50% {
                transform: scale(1.08);
                opacity: 1;
            }
        }

        .public-heading {
            position: relative;
            overflow: hidden;
            border: 1px solid #BCCCDC;
            border-radius: .75rem;
            padding: 1.25rem .75rem 1.25rem 1rem;
            background:
                linear-gradient(90deg, rgba(0, 95, 115, .10), rgba(42, 157, 143, .12)),
                #FFFFFF;
            box-shadow: 0 14px 34px rgba(16, 42, 67, .08);
        }

        .public-heading::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: .5rem;
            background: #005F73;
        }

        .public-heading-title {
            color: #005F73;
            display: block;
            font-size: 13px;
            line-height: 1.15;
            letter-spacing: 0;
            white-space: nowrap;
        }

        @media (min-width: 640px) {
            .public-heading {
                padding: 2rem 1.25rem;
            }

            .public-heading-title {
                font-size: 32px;
            }
        }

        @media (min-width: 768px) {
            .public-heading-title {
                font-size: 44px;
            }
        }

        .urgency-switch {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .35rem;
            border: 1px solid #BCCCDC;
            border-radius: .75rem;
            padding: .35rem;
            background: #EAF3F8;
        }

        .urgency-option {
            display: inline-flex;
            min-height: 3.25rem;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            border: 1px solid transparent;
            border-radius: .5rem;
            color: #334E68;
            background: transparent;
            font-size: .9rem;
            font-weight: 800;
            transition: background .16s ease, border-color .16s ease, color .16s ease, box-shadow .16s ease, transform .16s ease;
        }

        .urgency-option:hover {
            background: rgba(255, 255, 255, .72);
        }

        .urgency-option[data-active="true"] {
            background: #FFFFFF;
            box-shadow: 0 7px 16px rgba(16, 42, 67, .10);
            transform: translateY(-1px);
        }

        .urgency-option[data-active="true"][data-urgency="normal"] {
            border-color: #16A34A;
            color: #047857;
        }

        .urgency-option[data-active="true"][data-urgency="urgent"] {
            border-color: #D97706;
            color: #B45309;
        }

        .urgency-option[data-active="true"][data-urgency="critique"] {
            border-color: #B42318;
            color: #B42318;
        }

        .urgency-option .material-symbols-outlined {
            font-size: 1.25rem;
        }

        html[data-theme="dark"] .urgency-switch {
            border-color: #365568;
            background: #152937;
        }

        html[data-theme="dark"] .urgency-option {
            color: #b7cad4;
        }

        html[data-theme="dark"] .urgency-option:hover,
        html[data-theme="dark"] .urgency-option[data-active="true"] {
            background: #101f2b;
            box-shadow: 0 7px 16px rgba(0, 0, 0, .22);
        }

        @media (max-width: 640px) {
            .urgency-switch {
                gap: .25rem;
                padding: .25rem;
            }

            .urgency-option {
                min-height: 3rem;
                flex-direction: column;
                gap: .12rem;
                font-size: .75rem;
            }

            .urgency-option .material-symbols-outlined {
                font-size: 1.1rem;
            }
        }
    </style>
    @include('partials.theme-head')
</head>
<body class="min-h-screen bg-surface font-sans text-on-surface">
    <header class="fixed top-0 z-50 flex w-full items-center justify-between border-b border-outline-variant bg-surface/95 px-6 py-3 backdrop-blur md:px-8">
        <a class="flex items-center gap-3 no-underline" href="{{ route('incidents.public.home') }}">
            <span class="grid h-16 w-16 place-items-center overflow-hidden rounded-full bg-white shadow-sm ring-1 ring-outline-variant">
                <img class="h-14 w-14 object-contain" src="{{ asset('images/smart-city-incidents-logo.png') }}" alt="Smart City Incidents">
            </span>
            <span class="hidden text-xl font-semibold text-primary sm:inline">SmartCity Incident</span>
        </a>
        <div class="flex items-center gap-3">
            @include('partials.theme-toggle')
            <a class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-container" href="{{ route('login') }}">
                Connexion
            </a>
        </div>
    </header>

    <main class="mx-auto flex min-h-screen max-w-7xl flex-col px-6 pb-16 pt-28 md:px-8">
        <section class="public-heading mx-auto mb-12 w-full max-w-5xl text-center">
            <h1 class="public-heading-title font-bold">Citoyens connectés, communes réactives</h1>
        </section>

        @if (session('success'))
            <div class="mx-auto mb-6 w-full max-w-5xl rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-auto mb-6 w-full max-w-5xl rounded-lg border border-error-container bg-red-50 px-4 py-3 text-error">
                <strong>Corrigez les champs suivants :</strong>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid w-full grid-cols-1 gap-6 lg:grid-cols-12">
            <aside class="lg:col-span-5">
                <div class="flex h-full flex-col justify-between rounded-xl border border-outline-variant bg-surface-container-low p-6">
                    <div>
                        <h2 class="mb-4 text-2xl font-semibold text-primary">Pourquoi signaler ?</h2>
                        <ul class="space-y-4 text-on-surface-variant">
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary">speed</span>
                                <span>Intervention rapide des services techniques.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary">security</span>
                                <span>Amélioration de la sécurité de tous les citoyens.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary">visibility</span>
                                <span>Transmission claire du signalement vers la commune concernée.</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-10 overflow-hidden rounded-lg">
                        <img class="h-52 w-full object-cover" alt="Infrastructure urbaine Smart City" src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80">
                    </div>
                </div>
            </aside>

            <section class="lg:col-span-7">
                <form method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data" id="public-incident-form" class="space-y-6 rounded-xl border border-outline-variant bg-surface-container-lowest p-6 shadow-sm md:p-10">
                    @csrf
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                    <input type="hidden" id="geolocation_verified" name="geolocation_verified" value="{{ old('geolocation_verified') }}">
                    <input type="hidden" id="geolocation_accuracy" name="geolocation_accuracy" value="{{ old('geolocation_accuracy') }}">
                    <input type="hidden" id="location_country" name="location_country" value="{{ old('location_country') }}">
                    <input type="hidden" id="location_city" name="location_city" value="{{ old('location_city') }}">
                    <input type="hidden" id="location_zone" name="location_zone" value="{{ old('location_zone') }}">
                    <input type="hidden" id="location_address" name="location_address" value="{{ old('location_address') }}">
                    <input type="hidden" id="urgency" name="urgency" value="{{ old('urgency', 'normal') }}">

                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="title">Titre de l'incident</label>
                        <select class="h-12 w-full rounded border border-outline bg-white px-3 text-base focus:border-primary focus:ring-2 focus:ring-primary/20" id="title" name="title" required>
                            <option value="">Sélectionnez un type d'incident</option>
                            @foreach ($titleOptions as $titleOption)
                                <option value="{{ $titleOption }}" @selected(old('title') === $titleOption)>{{ $titleOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="hidden space-y-2" id="custom-title-wrap">
                        <label class="block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="custom_title">Préciser l'incident</label>
                        <input class="h-12 w-full rounded border border-outline bg-white px-3 text-base focus:border-primary focus:ring-2 focus:ring-primary/20" id="custom_title" name="custom_title" value="{{ old('custom_title') }}" placeholder="Décrivez brièvement l'incident">
                    </div>

                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-[0.12em] text-secondary">Niveau d'urgence</label>
                        <div class="urgency-switch" id="urgency-buttons" role="group" aria-label="Niveau d'urgence">
                            <button class="urgency-option" type="button" data-urgency="normal" data-active="false" aria-pressed="false">
                                <span class="material-symbols-outlined">check_circle</span>
                                <span>Normale</span>
                            </button>
                            <button class="urgency-option" type="button" data-urgency="urgent" data-active="false" aria-pressed="false">
                                <span class="material-symbols-outlined">warning</span>
                                <span>Urgente</span>
                            </button>
                            <button class="urgency-option" type="button" data-urgency="critique" data-active="false" aria-pressed="false">
                                <span class="material-symbols-outlined">report_problem</span>
                                <span>Critique</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-[0.12em] text-secondary" for="description">Description <span class="normal-case tracking-normal text-secondary">(facultatif)</span></label>
                        <textarea class="min-h-28 w-full resize-y rounded border border-outline bg-white px-3 py-3 text-base focus:border-primary focus:ring-2 focus:ring-primary/20" id="description" name="description" maxlength="1200" placeholder="Ajoutez quelques détails utiles si nécessaire">{{ old('description') }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <span class="block text-xs font-bold uppercase tracking-[0.12em] text-secondary">Photos</span>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="group flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-outline-variant bg-surface-container-low p-6 text-center transition hover:bg-surface-container">
                                <span class="material-symbols-outlined mb-3 text-4xl text-primary transition group-hover:scale-110">photo_camera</span>
                                <span class="font-semibold text-primary">Prendre une photo</span>
                                <span class="mt-1 text-sm text-secondary">Vous pouvez reprendre plusieurs photos une par une.</span>
                                <input id="photo-camera" class="hidden" type="file" accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.heic,.heif" capture="environment">
                            </label>
                            <label class="group flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-outline-variant bg-surface-container-low p-6 text-center transition hover:bg-surface-container">
                                <span class="material-symbols-outlined mb-3 text-4xl text-primary transition group-hover:scale-110">folder_open</span>
                                <span class="font-semibold text-primary">Choisir des fichiers</span>
                                <span class="mt-1 text-sm text-secondary">Sélectionnez une ou plusieurs images depuis la galerie.</span>
                                <input id="photo-gallery" class="hidden" type="file" accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.heic,.heif" multiple>
                            </label>
                        </div>
                        <input id="photo" class="hidden" type="file" name="photos[]" accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.heic,.heif" multiple>
                        <p class="text-sm font-semibold text-secondary" id="photo-label">Aucune photo ajoutée pour le moment.</p>
                        <div class="hidden grid-cols-1 gap-3 rounded-xl border border-outline-variant bg-surface-container-lowest p-3 sm:grid-cols-2" id="photo-preview-wrap"></div>
                    </div>

                    <div class="gps-widget" id="gps-widget" data-location-state="idle">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4">
                                <span class="gps-target" aria-hidden="true">
                                    <span class="material-symbols-outlined text-3xl">my_location</span>
                                </span>
                                <div class="min-w-0">
                                    <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold uppercase tracking-[0.12em] text-primary shadow-sm">
                                        <span class="material-symbols-outlined text-base">gps_fixed</span>
                                        GPS requis
                                    </div>
                                    <p class="text-base font-bold text-on-surface">Position GPS</p>
                                    <p class="mt-1 text-sm font-semibold leading-6 text-on-surface-variant" id="location-status" aria-live="polite">
                                        Cliquez sur "Activer ma localisation" pour autoriser la position.
                                    </p>
                                </div>
                            </div>
                            <button class="gps-action inline-flex min-h-12 shrink-0 items-center justify-center gap-2 bg-primary px-5 py-3 text-sm font-bold text-white transition hover:bg-primary-container disabled:cursor-not-allowed disabled:opacity-60" type="button" id="use-location">
                                <span class="location-spinner" id="location-spinner" hidden aria-hidden="true"></span>
                                <span class="material-symbols-outlined location-button-icon text-lg">near_me</span>
                                <span id="location-button-label">Activer ma localisation</span>
                            </button>
                        </div>
                    </div>

                    <button class="flex w-full items-center justify-center gap-2 rounded bg-primary px-6 py-4 text-sm font-bold uppercase tracking-wider text-white transition hover:bg-primary-container disabled:cursor-not-allowed disabled:opacity-55" type="submit" id="submit-incident" disabled>
                        Envoyer le signalement
                        <span class="material-symbols-outlined text-lg">send</span>
                    </button>
                </form>
            </section>
        </div>
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
        const gpsWidget = document.getElementById('gps-widget');
        const submitButton = document.getElementById('submit-incident');
        const urgencyInput = document.getElementById('urgency');
        const urgencyButtons = Array.from(document.querySelectorAll('.urgency-option'));
        const photoInput = document.getElementById('photo');
        const photoCameraInput = document.getElementById('photo-camera');
        const photoGalleryInput = document.getElementById('photo-gallery');
        const photoLabel = document.getElementById('photo-label');
        const photoPreviewWrap = document.getElementById('photo-preview-wrap');
        const supportedCommunes = @json($supportedCommunes);
        let locationCommuneIdentified = false;
        let selectedPhotos = [];
        let preparingPhotos = false;
        const maxPhotoDimension = 960;
        const photoCompressionQuality = 0.7;
        const acceptedPhotoExtensions = new Set(['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'heic', 'heif']);
        const heicPhotoTypes = new Set(['image/heic', 'image/heif']);

        function toggleCustomTitle() {
            const needsCustomTitle = titleSelect.value.toLowerCase().includes('autre');
            customTitleWrap.classList.toggle('hidden', !needsCustomTitle);
            customTitleInput.required = needsCustomTitle;
            if (!needsCustomTitle) customTitleInput.value = '';
            if (needsCustomTitle) customTitleInput.focus();
        }

        function updateUrgencyButtons() {
            urgencyButtons.forEach((button) => {
                const isActive = button.dataset.urgency === urgencyInput.value;
                button.dataset.active = String(isActive);
                button.setAttribute('aria-pressed', String(isActive));
            });
        }

        function isLocationReady() {
            return locationCommuneIdentified && geolocationVerifiedInput.value === '1' && latitudeInput.value && longitudeInput.value;
        }

        function normalizeLocationText(value) {
            return String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
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
            if (gpsWidget && isSearching) {
                gpsWidget.dataset.locationState = 'searching';
            }
        }

        function setCommuneStatus(commune) {
            locationCommuneIdentified = Boolean(commune);
            submitButton.disabled = !isLocationReady();
            setLocationSearching(false);
            if (gpsWidget) {
                gpsWidget.dataset.locationState = commune ? 'ready' : 'error';
            }
            locationStatus.textContent = commune
                ? `Localisation activée. La plainte sera envoyée à la commune de ${commune}.`
                : 'Zone non prise en charge. Le signalement ne peut pas être envoyé depuis cette position.';
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

                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error('Adresse introuvable');

                const data = await response.json();
                const address = data.address || {};
                locationCountryInput.value = address.country || '';
                locationCityInput.value = address.city || address.town || address.village || address.municipality || address.county || address.state || '';
                locationZoneInput.value = address.neighbourhood || address.suburb || address.quarter || address.city_district || address.district || address.road || '';
                locationAddressInput.value = data.display_name || '';
                setCommuneStatus(findSupportedCommune(locationCityInput.value, locationZoneInput.value, locationAddressInput.value));
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
            if (gpsWidget) {
                gpsWidget.dataset.locationState = 'idle';
            }
            submitButton.disabled = true;
        }

        function showLocationError(error = null) {
            resetLocationState();
            setLocationSearching(false);
            if (gpsWidget) {
                gpsWidget.dataset.locationState = 'error';
            }
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
        urgencyButtons.forEach((button) => {
            button.addEventListener('click', () => {
                urgencyInput.value = button.dataset.urgency;
                updateUrgencyButtons();
            });
        });

        function syncPhotoInputFiles() {
            const dataTransfer = new DataTransfer();
            selectedPhotos.forEach((file) => dataTransfer.items.add(file));
            photoInput.files = dataTransfer.files;
        }

        function renderPhotoPreviews() {
            if (selectedPhotos.length === 0) {
                photoLabel.textContent = 'Aucune photo ajoutée pour le moment.';
                photoPreviewWrap.classList.add('hidden');
                photoPreviewWrap.classList.remove('grid');
                photoPreviewWrap.innerHTML = '';
                return;
            }

            photoLabel.textContent = `${selectedPhotos.length} photo${selectedPhotos.length > 1 ? 's' : ''} ajoutée${selectedPhotos.length > 1 ? 's' : ''}.`;
            photoPreviewWrap.innerHTML = '';
            photoPreviewWrap.classList.remove('hidden');
            photoPreviewWrap.classList.add('grid');

            selectedPhotos.forEach((file, index) => {
                const url = URL.createObjectURL(file);
                const card = document.createElement('div');
                card.className = 'overflow-hidden rounded-lg border border-outline-variant bg-white';
                card.innerHTML = `
                    <img class="h-36 w-full object-cover" src="${url}" alt="Aperçu de la photo ${index + 1}">
                    <div class="flex items-center justify-between gap-2 p-2">
                        <p class="min-w-0 truncate text-sm font-semibold text-on-surface-variant" title="${file.name}">${file.name}</p>
                        <button class="shrink-0 rounded bg-red-50 px-2 py-1 text-xs font-bold text-error" type="button" data-remove-photo="${index}">Supprimer</button>
                    </div>
                `;
                const previewImage = card.querySelector('img');
                previewImage.addEventListener('load', () => URL.revokeObjectURL(url), { once: true });
                previewImage.addEventListener('error', () => {
                    URL.revokeObjectURL(url);
                    previewImage.replaceWith(Object.assign(document.createElement('div'), {
                        className: 'grid h-36 w-full place-items-center bg-surface-container text-sm font-semibold text-secondary',
                        textContent: 'Aperçu indisponible',
                    }));
                }, { once: true });
                photoPreviewWrap.appendChild(card);
            });
        }

        function compressedPhotoName(file) {
            const baseName = (file.name || 'photo').replace(/\.[^.]+$/, '');
            return `${baseName || 'photo'}.jpg`;
        }

        function photoFileExtension(file) {
            return String(file?.name || '').split('.').pop().toLowerCase();
        }

        function isSupportedPhotoFile(file) {
            return Boolean(file) && (
                file.type?.startsWith('image/')
                || acceptedPhotoExtensions.has(photoFileExtension(file))
            );
        }

        function isHeicPhotoFile(file) {
            return heicPhotoTypes.has(file?.type || '') || ['heic', 'heif'].includes(photoFileExtension(file));
        }

        function appendPreparedPhotoFiles(files) {
            selectedPhotos = [...selectedPhotos, ...files];
            preparingPhotos = false;
            syncPhotoInputFiles();
            renderPhotoPreviews();
            photoCameraInput.value = '';
            photoGalleryInput.value = '';
        }

        function preparePhotoFile(file) {
            if (!isSupportedPhotoFile(file)) {
                return Promise.resolve(null);
            }

            if (isHeicPhotoFile(file)) {
                return Promise.resolve(file);
            }

            return new Promise((resolve) => {
                const image = new Image();
                const objectUrl = URL.createObjectURL(file);

                image.onload = () => {
                    URL.revokeObjectURL(objectUrl);

                    const scale = Math.min(1, maxPhotoDimension / image.width, maxPhotoDimension / image.height);
                    const width = Math.max(1, Math.round(image.width * scale));
                    const height = Math.max(1, Math.round(image.height * scale));
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    canvas.getContext('2d')?.drawImage(image, 0, 0, width, height);

                    if (!canvas.toBlob) {
                        resolve(file);
                        return;
                    }

                    canvas.toBlob((blob) => {
                        if (!blob) {
                            resolve(file);
                            return;
                        }

                        const compressed = new File([blob], compressedPhotoName(file), {
                            type: 'image/jpeg',
                            lastModified: Date.now(),
                        });

                        resolve(compressed.size < file.size || file.size > 1200000 ? compressed : file);
                    }, 'image/jpeg', photoCompressionQuality);
                };

                image.onerror = () => {
                    URL.revokeObjectURL(objectUrl);
                    resolve(file.type?.startsWith('image/') ? file : null);
                };

                image.src = objectUrl;
            });
        }

        async function addSelectedPhotoFiles(files) {
            const incomingFiles = Array.from(files || []);
            if (incomingFiles.length === 0) return;

            preparingPhotos = true;
            photoLabel.classList.remove('text-error');
            photoLabel.textContent = 'Préparation des photos...';

            const preparedPhotos = [];
            let rejectedPhotos = 0;
            for (const file of incomingFiles) {
                const preparedPhoto = await preparePhotoFile(file);
                if (preparedPhoto) {
                    preparedPhotos.push(preparedPhoto);
                } else {
                    rejectedPhotos++;
                }
            }

            if (preparedPhotos.length === 0) {
                preparingPhotos = false;
                photoCameraInput.value = '';
                photoGalleryInput.value = '';
                photoLabel.textContent = rejectedPhotos > 0
                    ? 'Le fichier choisi n’est pas une image compatible.'
                    : 'Aucune photo ajoutée pour le moment.';
                photoLabel.classList.add('text-error');
                return;
            }

            appendPreparedPhotoFiles(preparedPhotos);
        }

        photoCameraInput.addEventListener('change', () => {
            addSelectedPhotoFiles(photoCameraInput.files);
        });

        photoGalleryInput.addEventListener('change', () => {
            addSelectedPhotoFiles(photoGalleryInput.files);
        });

        photoPreviewWrap.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove-photo]');
            if (!button) return;

            selectedPhotos.splice(Number(button.dataset.removePhoto), 1);
            syncPhotoInputFiles();
            renderPhotoPreviews();
        });

        form.addEventListener('submit', (event) => {
            if (preparingPhotos) {
                event.preventDefault();
                photoLabel.textContent = 'Patientez, les photos sont encore en préparation.';
                photoLabel.classList.add('text-error');
                return;
            }

            if (selectedPhotos.length === 0) {
                event.preventDefault();
                photoLabel.textContent = 'Ajoutez au moins une photo avant d’envoyer le signalement.';
                photoLabel.classList.add('text-error');
                return;
            }

            photoLabel.classList.remove('text-error');

            if (isLocationReady()) return;
            event.preventDefault();
            if (geolocationVerifiedInput.value === '1' && latitudeInput.value && longitudeInput.value) {
                locationStatus.textContent = 'Zone non prise en charge. Le signalement ne peut pas être envoyé depuis cette position.';
                return;
            }
            locationStatus.textContent = 'Localisation obligatoire : autorisez la position avant l’envoi.';
            requestLocation();
        });

        toggleCustomTitle();
        updateUrgencyButtons();

        if (!window.isSecureContext && !['localhost', '127.0.0.1'].includes(window.location.hostname)) {
            showLocationError();
        }
    </script>
</body>
</html>
