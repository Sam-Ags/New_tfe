<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Incident;
use App\Models\UrbanNotification;
use App\Models\User;
use App\Services\CloudinaryImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IncidentController extends Controller
{
    private const UPLOADED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'jpe', 'png', 'gif', 'webp', 'bmp', 'heic', 'heif'];

    private const UPLOADED_IMAGE_MIME_TYPES = [
        'image/jpg',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        'image/x-ms-bmp',
        'image/heic',
        'image/heif',
    ];

    public function publicHome(): View
    {
        return view('incidents.home');
    }

    public function publicCreate(): View
    {
        return view('incidents.public', [
            'titleOptions' => Incident::TITLE_OPTIONS,
            'supportedCommunes' => Commune::orderBy('name')->pluck('name')->values(),
        ]);
    }

    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user->canManageIncidents()) {
            return redirect()->route('incidents.public.create');
        }

        $zoneScope = Incident::with(['user', 'commune', 'assignedAgent', 'photos', 'completionPhotos'])
            ->when($user->commune_id, fn ($query) => $query->where('commune_id', $user->commune_id));

        $filteredScope = (clone $zoneScope)
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(request('category'), fn ($query, $category) => $query->where('category', $category));

        $dashboardScope = $user->dashboardIncidents()
            ->with(['user', 'commune', 'assignedAgent', 'photos', 'completionPhotos'])
            ->when($user->commune_id, fn ($query) => $query->where('commune_id', $user->commune_id));

        $dashboardHistoryScope = (clone $dashboardScope)
            ->where('status', 'resolu');

        $dashboardActiveScope = (clone $dashboardScope)
            ->where('status', '!=', 'resolu')
            ->when(request('status') && request('status') !== 'resolu', fn ($query) => $query->where('status', request('status')))
            ->when(request('category'), fn ($query, $category) => $query->where('category', $category));

        $incidents = match (true) {
            $user->isAdmin() => $dashboardActiveScope->latest('incidents.created_at')->get(),
            $user->isAgent() => (clone $filteredScope)->where('assigned_agent_id', $user->id)->where('status', '!=', 'resolu')->latest()->get(),
            default => $user->incidents()->with(['commune', 'assignedAgent'])->latest()->get(),
        };

        $historyIncidents = match (true) {
            $user->isAdmin() => $dashboardHistoryScope->latest('incidents.resolved_at')->latest('incidents.updated_at')->get(),
            $user->isAgent() => (clone $zoneScope)->where('assigned_agent_id', $user->id)->where('status', 'resolu')->latest('resolved_at')->latest('updated_at')->get(),
            default => collect(),
        };

        $dashboardIncidentIds = $user->dashboardIncidents()->pluck('incidents.id');

        $allIncidents = $user->canManageIncidents()
            ? (clone $zoneScope)->latest()->get()
            : Incident::where('commune_id', $user->commune_id)->latest()->get();
        $statsIncidents = $user->isAgent()
            ? $allIncidents->where('assigned_agent_id', $user->id)
            : $allIncidents;

        $agents = User::where('role', 'agent')
            ->when($user->commune_id, fn ($query) => $query->where('commune_id', $user->commune_id))
            ->orderBy('name')
            ->get();

        $performanceStats = $this->performanceStats($statsIncidents);

        $notifications = $user->urbanNotifications()
            ->with('incident')
            ->latest()
            ->limit(6)
            ->get();

        $unreadNotificationsCount = $user->urbanNotifications()
            ->whereNull('read_at')
            ->count();

        $groupIncidentsByCategory = fn ($collection) => $collection
            ->groupBy('category')
            ->map(fn ($items, $category) => [
                'category' => $category,
                'label' => Incident::CATEGORIES[$category] ?? $category,
                'count' => $items->count(),
                'districts' => $items
                    ->pluck('district')
                    ->filter()
                    ->unique()
                    ->take(4)
                    ->implode(', '),
                'incidents' => $items
                    ->sortBy(fn (Incident $incident) => ($incident->commune?->name ?? '').'|'.($incident->district ?? '').'|'.$incident->created_at->timestamp)
                    ->values(),
            ])
            ->sortBy('label')
            ->values();

        return view('incidents.index', [
            'incidents' => $incidents,
            'historyIncidents' => $historyIncidents,
            'allIncidents' => $allIncidents,
            'dashboardCategoryGroups' => $groupIncidentsByCategory($incidents),
            'allIncidentCategoryGroups' => $groupIncidentsByCategory($allIncidents),
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'categories' => Incident::CATEGORIES,
            'statuses' => Incident::STATUSES,
            'activeStatuses' => collect(Incident::STATUSES)->except('resolu'),
            'titleOptions' => Incident::TITLE_OPTIONS,
            'agents' => $agents,
            'performanceStats' => $performanceStats,
            'activeFilters' => [
                'status' => request('status'),
                'category' => request('category'),
            ],
            'stats' => [
                'total' => $statsIncidents->count(),
                'pending' => $statsIncidents->where('status', 'en_attente')->count(),
                'progress' => $statsIncidents->where('status', 'en_cours')->count(),
                'validation' => $statsIncidents->where('status', 'en_validation')->count(),
                'resolved' => $statsIncidents->where('status', 'resolu')->count(),
                'high' => $statsIncidents->where('priority', 'elevee')->count(),
            ],
            'categoryStats' => $allIncidents
                ->groupBy('category')
                ->map(fn ($items, $category) => [
                    'label' => Incident::CATEGORIES[$category] ?? $category,
                    'count' => $items->count(),
                ])
                ->values(),
            'agentStats' => $agents
                ->map(function (User $agent) use ($allIncidents) {
                    $assignedIncidents = $allIncidents->where('assigned_agent_id', $agent->id);

                    return [
                        'name' => $agent->name,
                        'commune' => $agent->commune?->name,
                        'assigned' => $assignedIncidents->count(),
                        'progress' => $assignedIncidents->whereIn('status', ['en_cours', 'en_validation'])->count(),
                        'resolved' => $assignedIncidents->where('status', 'resolu')->count(),
                        'resolutionRate' => $this->resolutionRate($assignedIncidents),
                        'averageResolutionHours' => $this->averageResolutionHours($assignedIncidents),
                        'late' => $this->lateIncidentsCount($assignedIncidents),
                        'missions' => $assignedIncidents
                            ->sortByDesc('updated_at')
                            ->take(10)
                            ->map(fn (Incident $incident) => [
                                'title' => $incident->title,
                                'commune' => $incident->commune?->name,
                                'district' => $incident->district,
                                'status' => $incident->status,
                                'statusLabel' => $incident->statusLabel(),
                                'priority' => $incident->priority,
                                'priorityLabel' => $incident->priorityLabel(),
                                'categoryLabel' => $incident->categoryLabel(),
                                'date' => $incident->created_at->format('d/m/Y H:i'),
                                'updated' => $incident->updated_at->format('d/m/Y H:i'),
                                'completionPhoto' => $incident->completion_photo_path ? asset($incident->completion_photo_path) : null,
                                'completionPhotos' => $incident->completionPhotos
                                    ->pluck('path')
                                    ->prepend($incident->completion_photo_path)
                                    ->filter()
                                    ->unique()
                                    ->map(fn (string $path) => asset($path))
                                    ->values(),
                            ])
                            ->values(),
                    ];
                })
                ->sortByDesc(fn (array $row) => $row['assigned'])
                ->values(),
            'mapIncidents' => $allIncidents
            ->reject(fn (Incident $incident) => $incident->status === 'resolu')
            ->when(
                $user->isAdmin(),
                fn ($collection) => $collection->reject(fn (Incident $incident) => $dashboardIncidentIds->contains($incident->id)),
            )
            ->map(fn (Incident $incident) => [
                'id' => $incident->id,
                'title' => $incident->title,
                'category' => $incident->categoryLabel(),
                'status' => $incident->statusLabel(),
                'priority' => $incident->priority,
                'priorityLabel' => $incident->priorityLabel(),
                'district' => $incident->district,
                'description' => $incident->description,
                'commune' => $incident->commune?->name,
                'agent' => $incident->assignedAgent?->name ?? $incident->assigned_to,
                'photo' => $incident->photo_path ? asset($incident->photo_path) : null,
                'photos' => $incident->photos->pluck('path')->prepend($incident->photo_path)->filter()->unique()->map(fn (string $path) => asset($path))->values(),
                'date' => $incident->created_at->format('d/m/Y H:i'),
                'dashboardUrl' => route('incidents.dashboard.store', $incident, false),
                'latitude' => $incident->latitude,
                'longitude' => $incident->longitude,
            ])->values(),
            'mapCenter' => [
                'latitude' => $user->commune?->latitude ?? $allIncidents->first()?->latitude ?? 6.3703,
                'longitude' => $user->commune?->longitude ?? $allIncidents->first()?->longitude ?? 2.3912,
                'zoom' => 13,
            ],
        ]);
    }

    public function performanceReport(): View
    {
        $user = Auth::user();

        abort_unless($user->isAdmin(), 403);

        $allIncidents = Incident::with(['commune', 'assignedAgent', 'photos', 'completionPhotos'])
            ->where('commune_id', $user->commune_id)
            ->latest()
            ->get();

        $agents = User::where('role', 'agent')
            ->where('commune_id', $user->commune_id)
            ->orderBy('name')
            ->get();

        $agentStats = $agents
            ->map(function (User $agent) use ($allIncidents): array {
                $assignedIncidents = $allIncidents->where('assigned_agent_id', $agent->id);

                return [
                    'name' => $agent->name,
                    'assigned' => $assignedIncidents->count(),
                    'progress' => $assignedIncidents->whereIn('status', ['en_cours', 'en_validation'])->count(),
                    'resolved' => $assignedIncidents->where('status', 'resolu')->count(),
                    'resolutionRate' => $this->resolutionRate($assignedIncidents),
                    'averageResolutionHours' => $this->averageResolutionHours($assignedIncidents),
                    'late' => $this->lateIncidentsCount($assignedIncidents),
                ];
            })
            ->sortByDesc(fn (array $row) => $row['assigned'])
            ->values();

        return view('incidents.report', [
            'user' => $user,
            'commune' => $user->commune,
            'generatedAt' => now(),
            'stats' => [
                'total' => $allIncidents->count(),
                'pending' => $allIncidents->where('status', 'en_attente')->count(),
                'progress' => $allIncidents->where('status', 'en_cours')->count(),
                'validation' => $allIncidents->where('status', 'en_validation')->count(),
                'resolved' => $allIncidents->where('status', 'resolu')->count(),
                'high' => $allIncidents->where('priority', 'elevee')->count(),
            ],
            'performanceStats' => $this->performanceStats($allIncidents),
            'categoryStats' => $allIncidents
                ->groupBy('category')
                ->map(fn ($items, $category) => [
                    'label' => Incident::CATEGORIES[$category] ?? $category,
                    'count' => $items->count(),
                    'resolved' => $items->where('status', 'resolu')->count(),
                ])
                ->sortByDesc('count')
                ->values(),
            'agentStats' => $agentStats,
            'recentResolvedIncidents' => $allIncidents
                ->where('status', 'resolu')
                ->sortByDesc('resolved_at')
                ->take(8)
                ->values(),
        ]);
    }

    private function performanceStats($incidents): array
    {
        return [
            'resolutionRate' => $this->resolutionRate($incidents),
            'averageResolutionHours' => $this->averageResolutionHours($incidents),
            'averageInterventionHours' => $this->averageInterventionHours($incidents),
            'late' => $this->lateIncidentsCount($incidents),
        ];
    }

    private function resolutionRate($incidents): int
    {
        $total = $incidents->count();

        if ($total === 0) {
            return 0;
        }

        return (int) round(($incidents->where('status', 'resolu')->count() / $total) * 100);
    }

    private function averageResolutionHours($incidents): ?float
    {
        return $this->averageHours($incidents->where('status', 'resolu'), 'created_at', 'resolved_at');
    }

    private function averageInterventionHours($incidents): ?float
    {
        return $this->averageHours($incidents->where('status', 'resolu'), 'taken_at', 'resolved_at');
    }

    private function averageHours($incidents, string $startColumn, string $endColumn): ?float
    {
        $durations = $incidents
            ->filter(fn (Incident $incident) => $incident->{$startColumn} && $incident->{$endColumn})
            ->map(fn (Incident $incident) => max(0, $incident->{$startColumn}->diffInMinutes($incident->{$endColumn}) / 60));

        if ($durations->isEmpty()) {
            return null;
        }

        return round($durations->avg(), 1);
    }

    private function lateIncidentsCount($incidents): int
    {
        return $incidents
            ->filter(function (Incident $incident): bool {
                $endDate = $incident->resolved_at ?? now();

                return $incident->created_at->diffInHours($endDate) > 48;
            })
            ->count();
    }

    public function storeInDashboard(Incident $incident): JsonResponse
    {
        $user = Auth::user();

        abort_unless($user->isAdmin(), 403);
        abort_if($user->commune_id !== $incident->commune_id, 403);

        $user->dashboardIncidents()->syncWithoutDetaching([$incident->id]);

        return response()->json([
            'message' => 'Plainte ajoutée au dashboard.',
        ]);
    }

    public function markNotificationAsRead(UrbanNotification $notification): JsonResponse
    {
        abort_if($notification->user_id !== Auth::id(), 403);

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json([
            'message' => 'Notification lue.',
            'unread_count' => Auth::user()
                ->urbanNotifications()
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    public function submitCompletion(Request $request, Incident $incident): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user->isAgent(), 403);
        abort_if($incident->assigned_agent_id !== $user->id, 403);

        $validated = $request->validate([
            'completion_photo' => $this->uploadedImageRules('nullable', 'required_without:completion_photos'),
            'completion_photos' => ['nullable', 'array', 'required_without:completion_photo'],
            'completion_photos.*' => $this->uploadedImageRules(),
            'completion_note' => ['nullable', 'string', 'max:800'],
        ], [
            'completion_photo.required_without' => 'Ajoutez au moins une photo de preuve après intervention.',
            'completion_photos.required_without' => 'Ajoutez au moins une photo de preuve après intervention.',
            'completion_photo.image' => 'La preuve envoyée doit être une image.',
            'completion_photos.*.image' => 'Chaque preuve envoyée doit être une image.',
            'completion_photo.mimetypes' => 'La preuve envoyée doit être une image valide.',
            'completion_photos.*.mimetypes' => 'Chaque preuve envoyée doit être une image valide.',
        ]);

        $uploadedPhotos = collect($request->file('completion_photos', []));

        if ($request->hasFile('completion_photo')) {
            $uploadedPhotos->prepend($request->file('completion_photo'));
        }

        $photoPaths = $uploadedPhotos
            ->filter()
            ->map(fn ($file): string => $this->storeUploadedImage($file, 'completions', 'completion'))
            ->values();

        $incident->update([
            'completion_photo_path' => $photoPaths->first(),
            'completion_note' => $validated['completion_note'] ?? null,
            'completion_submitted_at' => now(),
            'status' => 'en_validation',
            'taken_at' => $incident->taken_at ?? now(),
            'resolved_at' => null,
        ]);

        $incident->completionPhotos()->delete();
        $incident->completionPhotos()->createMany(
            $photoPaths->map(fn (string $path) => ['path' => $path])->all()
        );

        User::where('role', 'admin')
            ->where('commune_id', $incident->commune_id)
            ->each(function (User $authority) use ($incident, $user): void {
                UrbanNotification::create([
                    'user_id' => $authority->id,
                    'incident_id' => $incident->id,
                    'title' => 'Preuve d’intervention reçue',
                    'message' => $user->name.' a envoyé une photo de fin d’intervention pour "'.$incident->title.'".',
                ]);
            });

        return redirect()
            ->route('incidents.index')
            ->with('success', 'Photo envoyée. La plainte attend maintenant la confirmation de l’administration.');
    }

    public function storePublic(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'custom_title' => [Rule::requiredIf(fn () => $this->needsCustomIncidentTitle($request->input('title'))), 'nullable', 'string', 'max:160'],
            'urgency' => ['required', 'in:normal,urgent,critique'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'geolocation_verified' => ['accepted'],
            'geolocation_accuracy' => ['nullable', 'numeric', 'min:0'],
            'location_country' => ['nullable', 'string', 'max:120'],
            'location_city' => ['nullable', 'string', 'max:120'],
            'location_zone' => ['nullable', 'string', 'max:160'],
            'location_address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:1200'],
            'photo' => $this->uploadedImageRules('nullable', 'required_without:photos'),
            'photos' => ['nullable', 'array', 'required_without:photo'],
            'photos.*' => $this->uploadedImageRules(),
        ], [
            'custom_title.required' => 'Précisez le titre de l’incident.',
            'custom_title.required_if' => 'Précisez le titre de l’incident.',
            'latitude.required' => 'Activez la localisation avant d’envoyer le signalement.',
            'longitude.required' => 'Activez la localisation avant d’envoyer le signalement.',
            'geolocation_verified.accepted' => 'La localisation est obligatoire pour envoyer le signalement.',
            'photo.required' => 'La photo de l’incident est obligatoire.',
            'photo.image' => 'Le fichier envoyé doit être une image.',
            'photo.mimetypes' => 'Le fichier envoyé doit être une image valide.',
            'photos.*.mimetypes' => 'Chaque fichier envoyé doit être une image valide.',
        ]);

        if ($this->needsCustomIncidentTitle($validated['title'])) {
            $validated['title'] = $validated['custom_title'];
        }

        $commune = $this->communeFromLocation($validated);

        if (! $commune) {
            return back()
                ->withErrors(['location' => 'Votre zone n’est pas encore prise en charge. Le signalement ne peut pas être envoyé.'])
                ->withInput();
        }

        $uploadedPhotos = collect($request->file('photos', []));

        if ($request->hasFile('photo')) {
            $uploadedPhotos->prepend($request->file('photo'));
        }

        $photoPaths = $uploadedPhotos
            ->filter()
            ->map(fn ($file): string => $this->storeUploadedImage($file, 'incidents', 'incident'))
            ->values();

        $category = $this->categoryFromTitle($validated['title']);
        $district = collect([
            $validated['location_zone'] ?? null,
            $validated['location_city'] ?? null,
            $validated['location_country'] ?? null,
        ])->filter()->implode(' - ') ?: 'Zone GPS';

        $description = filled($validated['description'] ?? null)
            ? $validated['description']
            : ($validated['location_address'] ?? $validated['title']);

        $incident = Incident::create([
            'citizen_name' => 'Citoyen anonyme',
            'user_id' => null,
            'commune_id' => $commune?->id,
            'citizen_phone' => null,
            'title' => $validated['title'],
            'category' => $category,
            'district' => $district,
            'description' => $description,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'urgency' => $validated['urgency'],
            'priority' => Incident::calculatePriority($category, $validated['urgency'], $district),
            'status' => 'en_attente',
            'photo_path' => $photoPaths->first(),
        ]);

        $incident->photos()->createMany(
            $photoPaths->map(fn (string $path) => ['path' => $path])->all()
        );

        User::whereIn('role', ['admin', 'agent'])
            ->when($incident->commune_id, fn ($query) => $query->where('commune_id', $incident->commune_id))
            ->each(function (User $authority) use ($incident): void {
                UrbanNotification::create([
                    'user_id' => $authority->id,
                    'incident_id' => $incident->id,
                    'title' => 'Nouveau signalement citoyen',
                    'message' => 'Un incident "'.$incident->categoryLabel().'" a été signalé anonymement avec une priorité '.$incident->priorityLabel().'.',
                ]);
            });

        return redirect()
            ->route('incidents.public.create')
            ->with('success', 'Signalement envoyé. Merci pour votre contribution.');
    }

    private function communeFromLocation(array $location): ?Commune
    {
        $haystacks = collect([
            $location['location_city'] ?? null,
            $location['location_zone'] ?? null,
            $location['location_address'] ?? null,
        ])
            ->filter()
            ->map(fn (string $value) => str($value)->ascii()->lower()->toString())
            ->values();

        $communes = Commune::query()
            ->orderByRaw('LENGTH(name) DESC')
            ->get();

        foreach ($communes as $commune) {
            $needle = str($commune->name)->ascii()->lower()->toString();

            if ($haystacks->contains(fn (string $value) => $value === $needle || str_contains($value, $needle))) {
                return $commune;
            }
        }

        return null;
    }

    private function categoryFromTitle(string $title): string
    {
        $normalized = str($title)->lower()->ascii()->toString();

        return match (true) {
            str_contains($normalized, 'route'), str_contains($normalized, 'trou') => 'route',
            str_contains($normalized, 'ordure'), str_contains($normalized, 'dechet') => 'dechets',
            str_contains($normalized, 'caniveau'), str_contains($normalized, 'inondation'), str_contains($normalized, 'eau') => 'inondation',
            str_contains($normalized, 'lampadaire'), str_contains($normalized, 'eclairage') => 'eclairage',
            str_contains($normalized, 'securite') => 'securite',
            default => 'autre',
        };
    }

    private function needsCustomIncidentTitle(?string $title): bool
    {
        return str_contains(str($title ?? '')->lower()->ascii()->toString(), 'autre');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'custom_title' => [Rule::requiredIf(fn () => $this->needsCustomIncidentTitle($request->input('title'))), 'nullable', 'string', 'max:160'],
            'category' => ['required', 'in:'.implode(',', array_keys(Incident::CATEGORIES))],
            'district' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:1200'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'geolocation_verified' => ['accepted'],
            'geolocation_accuracy' => ['nullable', 'numeric', 'min:0'],
            'urgency' => ['required', 'in:normal,urgent,critique'],
            'photo' => $this->uploadedImageRules('nullable'),
        ], [
            'latitude.required' => 'Activez la localisation GPS avant d’envoyer le signalement.',
            'longitude.required' => 'Activez la localisation GPS avant d’envoyer le signalement.',
            'geolocation_verified.accepted' => 'La localisation GPS est obligatoire pour envoyer une plainte.',
            'custom_title.required' => 'Précisez le titre de l’incident.',
            'custom_title.required_if' => 'Précisez le titre de l’incident.',
            'photo.mimetypes' => 'Le fichier envoyé doit être une image valide.',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $validated['photo_path'] = $this->storeUploadedImage($file, 'incidents', 'incident');
        }

        if ($this->needsCustomIncidentTitle($validated['title'])) {
            $validated['title'] = $validated['custom_title'];
        }

        unset($validated['custom_title'], $validated['geolocation_verified'], $validated['geolocation_accuracy']);

        $validated['priority'] = Incident::calculatePriority(
            $validated['category'],
            $validated['urgency'],
            $validated['district'],
        );
        $validated['status'] = 'en_attente';
        $validated['user_id'] = $user->id;
        $validated['commune_id'] = $user->commune_id;
        $validated['citizen_name'] = $user->name;
        $validated['citizen_phone'] = $user->phone;

        $incident = Incident::create($validated);

        UrbanNotification::create([
            'user_id' => $user->id,
            'incident_id' => $incident->id,
            'title' => 'Signalement reçu',
            'message' => 'Votre incident a été enregistré avec une priorité '.$incident->priorityLabel().'.',
        ]);

        User::whereIn('role', ['admin', 'agent'])
            ->where('commune_id', $incident->commune_id)
            ->each(function (User $authority) use ($incident): void {
            UrbanNotification::create([
                'user_id' => $authority->id,
                'incident_id' => $incident->id,
                'title' => 'Nouveau signalement citoyen',
                'message' => 'Un incident "'.$incident->categoryLabel().'" a été signalé à '.$incident->district.' dans votre commune avec une priorité '.$incident->priorityLabel().'.',
            ]);
        });

        return redirect()
            ->route('incidents.index')
            ->with('success', 'Signalement envoyé. Il apparaît maintenant dans le tableau de suivi.');
    }

    public function update(Request $request, Incident $incident): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user->isAdmin() || $user->isAgent(), 403);
        abort_if($user->isAdmin() && $user->commune_id !== $incident->commune_id, 403);
        abort_if($user->isAgent() && $incident->assigned_agent_id !== $user->id, 403);

        $allowedStatuses = $user->isAgent()
            ? ['en_cours']
            : array_keys(Incident::STATUSES);

        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', $allowedStatuses)],
            'assigned_agent_id' => ['nullable', 'exists:users,id'],
        ]);

        if (
            ! $user->isAgent()
            && $incident->status === 'en_validation'
            && $incident->completion_photo_path
            && $validated['status'] === 'en_validation'
        ) {
            $validated['status'] = 'resolu';
        }

        if ($user->isAgent()) {
            unset($validated['assigned_agent_id']);
        } elseif (! empty($validated['assigned_agent_id'])) {
            $agent = User::where('role', 'agent')
                ->where('commune_id', $incident->commune_id)
                ->findOrFail($validated['assigned_agent_id']);
            $validated['assigned_to'] = $agent->name;
        } else {
            $validated['assigned_to'] = null;
        }

        $validated['resolved_at'] = $validated['status'] === 'resolu' ? now() : null;
        $validated['taken_at'] = in_array($validated['status'], ['en_cours', 'resolu'], true)
            ? ($incident->taken_at ?? now())
            : null;

        $previousStatus = $incident->status;
        $previousAgentId = $incident->assigned_agent_id;
        $incident->update($validated);
        $incident->refresh();

        if ($incident->user_id) {
            UrbanNotification::create([
                'user_id' => $incident->user_id,
                'incident_id' => $incident->id,
                'title' => 'Statut mis à jour',
                'message' => 'Votre signalement "'.$incident->title.'" est maintenant : '.$incident->statusLabel().'.',
            ]);
        }

        if ($incident->assigned_agent_id && $incident->assigned_agent_id !== $previousAgentId) {
            UrbanNotification::create([
                'user_id' => $incident->assigned_agent_id,
                'incident_id' => $incident->id,
                'title' => 'Incident affecté',
                'message' => 'Un signalement "'.$incident->title.'" vous a été affecté à '.$incident->district.'.',
            ]);
        }

        if ($previousStatus !== $incident->status && $incident->status === 'en_cours' && $incident->assigned_agent_id) {
            UrbanNotification::create([
                'user_id' => $incident->assigned_agent_id,
                'incident_id' => $incident->id,
                'title' => 'Intervention démarrée',
                'message' => 'Le signalement "'.$incident->title.'" est maintenant en cours de traitement.',
            ]);
        }

        return redirect()
            ->route('incidents.index')
            ->with('success', 'Suivi administratif mis à jour.');
    }

    private function uploadedImageRules(string ...$rules): array
    {
        return [
            ...$rules,
            'file',
            'min:1',
            'max:20480',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! $this->isUploadedImage($value)) {
                    $this->logUnrecognizedUploadedImage($attribute, $value);
                    $fail('Le fichier envoye doit etre une image valide.');
                }
            },
        ];
    }

    private function storeUploadedImage($file, string $folder, string $prefix): string
    {
        try {
            return (new CloudinaryImageUploader())->upload($file, $folder, $prefix);
        } catch (\Throwable $exception) {
            Log::warning('Cloudinary upload failed.', [
                'folder' => $folder,
                'prefix' => $prefix,
                'message' => $exception->getMessage(),
            ]);

            return $this->storeUploadedImageLocally($file, $folder, $prefix);
        }
    }

    private function storeUploadedImageLocally($file, string $folder, string $prefix): string
    {
        if (! $this->isUploadedImage($file) || (int) $file->getSize() <= 0) {
            throw ValidationException::withMessages([
                'photos' => 'La photo envoyee est vide ou invalide.',
            ]);
        }

        $directory = public_path('uploads/'.$folder);
        File::ensureDirectoryExists($directory);

        $filename = uniqid($prefix.'_', true).'.'.$this->uploadedImageExtension($file);
        $file->move($directory, $filename);

        return 'uploads/'.$folder.'/'.$filename;
    }

    private function isUploadedImage($file): bool
    {
        if (! $file instanceof \Illuminate\Http\UploadedFile || ! $file->isValid()) {
            return false;
        }

        $serverMimeType = strtolower((string) $file->getMimeType());
        $clientMimeType = strtolower((string) $file->getClientMimeType());
        $extension = $this->uploadedImageExtension($file);

        if (
            in_array($extension, ['heic', 'heif'], true)
            || in_array($serverMimeType, ['image/heic', 'image/heif'], true)
            || in_array($clientMimeType, ['image/heic', 'image/heif'], true)
        ) {
            return true;
        }

        if (@getimagesize($file->getPathname()) !== false) {
            return true;
        }

        if ($this->fileHasImageSignature($file->getPathname())) {
            return true;
        }

        return in_array($serverMimeType, self::UPLOADED_IMAGE_MIME_TYPES, true)
            || in_array($clientMimeType, self::UPLOADED_IMAGE_MIME_TYPES, true);
    }

    private function fileHasImageSignature(string $path): bool
    {
        $handle = @fopen($path, 'rb');

        if (! $handle) {
            return false;
        }

        $bytes = fread($handle, 32) ?: '';
        fclose($handle);

        if ($bytes === '') {
            return false;
        }

        $hex = bin2hex($bytes);

        if (str_starts_with($hex, 'ffd8ff') || str_starts_with($hex, '89504e470d0a1a0a')) {
            return true;
        }

        if (str_starts_with($bytes, 'GIF87a') || str_starts_with($bytes, 'GIF89a') || str_starts_with($bytes, 'BM')) {
            return true;
        }

        if (str_starts_with($bytes, 'RIFF') && substr($bytes, 8, 4) === 'WEBP') {
            return true;
        }

        return substr($bytes, 4, 4) === 'ftyp'
            && preg_match('/heic|heix|hevc|hevx|heim|heis|mif1|msf1|avif/', substr($bytes, 8)) === 1;
    }

    private function logUnrecognizedUploadedImage(string $attribute, mixed $file): void
    {
        if (! $file instanceof \Illuminate\Http\UploadedFile) {
            Log::warning('Uploaded incident photo accepted but not recognized as an image: value is not an uploaded file.', [
                'attribute' => $attribute,
                'type' => get_debug_type($file),
            ]);

            return;
        }

        Log::warning('Uploaded incident photo accepted but not recognized as an image.', [
            'attribute' => $attribute,
            'valid' => $file->isValid(),
            'error' => $file->getError(),
            'client_name' => $file->getClientOriginalName(),
            'client_extension' => $file->getClientOriginalExtension(),
            'client_mime' => $file->getClientMimeType(),
            'server_mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $file->getPathname(),
            'signature' => $this->fileSignature($file->getPathname()),
        ]);
    }

    private function fileSignature(string $path): ?string
    {
        $handle = @fopen($path, 'rb');

        if (! $handle) {
            return null;
        }

        $bytes = fread($handle, 16) ?: '';
        fclose($handle);

        return $bytes === '' ? null : bin2hex($bytes);
    }

    private function uploadedImageExtension($file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');

        return match ($extension) {
            'jpeg', 'jpe' => 'jpg',
            'png', 'gif', 'webp', 'bmp', 'heic', 'heif' => $extension,
            default => 'jpg',
        };
    }
}
