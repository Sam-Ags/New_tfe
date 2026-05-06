<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Incident;
use App\Models\UrbanNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncidentController extends Controller
{
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

        $zoneScope = Incident::with(['user', 'commune', 'assignedAgent'])
            ->when($user->isSuperAdmin(), fn ($query) => $query->whereHas(
                'commune',
                fn ($communeQuery) => $communeQuery->where('department', $user->department),
            ))
            ->when(! $user->isSuperAdmin() && $user->commune_id, fn ($query) => $query->where('commune_id', $user->commune_id));

        $filteredScope = (clone $zoneScope)
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(request('category'), fn ($query, $category) => $query->where('category', $category));

        $dashboardScope = $user->dashboardIncidents()
            ->with(['user', 'commune', 'assignedAgent'])
            ->when($user->isSuperAdmin(), fn ($query) => $query->whereHas(
                'commune',
                fn ($communeQuery) => $communeQuery->where('department', $user->department),
            ))
            ->when(! $user->isSuperAdmin() && $user->commune_id, fn ($query) => $query->where('commune_id', $user->commune_id))
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(request('category'), fn ($query, $category) => $query->where('category', $category));

        $incidents = match (true) {
            $user->isSuperAdmin(), $user->isAdmin() => $dashboardScope->latest('incidents.created_at')->get(),
            $user->isAgent() => (clone $filteredScope)->where('assigned_agent_id', $user->id)->latest()->get(),
            default => $user->incidents()->with(['commune', 'assignedAgent'])->latest()->get(),
        };
        $dashboardIncidentIds = $incidents->pluck('id');

        $allIncidents = $user->canManageIncidents()
            ? (clone $zoneScope)->latest()->get()
            : Incident::where('commune_id', $user->commune_id)->latest()->get();
        $notifications = $user->urbanNotifications()
            ->with('incident')
            ->latest()
            ->limit(6)
            ->get();

        return view('incidents.index', [
            'incidents' => $incidents,
            'allIncidents' => $allIncidents,
            'notifications' => $notifications,
            'categories' => Incident::CATEGORIES,
            'statuses' => Incident::STATUSES,
            'titleOptions' => Incident::TITLE_OPTIONS,
            'agents' => User::where('role', 'agent')
                ->when($user->isSuperAdmin(), fn ($query) => $query->where('department', $user->department))
                ->when(! $user->isSuperAdmin() && $user->commune_id, fn ($query) => $query->where('commune_id', $user->commune_id))
                ->orderBy('name')
                ->get(),
            'activeFilters' => [
                'status' => request('status'),
                'category' => request('category'),
            ],
            'stats' => [
                'total' => $allIncidents->count(),
                'pending' => $allIncidents->where('status', 'en_attente')->count(),
                'progress' => $allIncidents->where('status', 'en_cours')->count(),
                'resolved' => $allIncidents->where('status', 'resolu')->count(),
                'high' => $allIncidents->where('priority', 'elevee')->count(),
            ],
            'categoryStats' => $allIncidents
                ->groupBy('category')
                ->map(fn ($items, $category) => [
                    'label' => Incident::CATEGORIES[$category] ?? $category,
                    'count' => $items->count(),
                ])
                ->values(),
            'zoneStats' => $allIncidents
                ->groupBy('district')
                ->map(fn ($items, $district) => [
                    'label' => $district,
                    'count' => $items->count(),
                ])
                ->sortByDesc('count')
                ->take(5)
                ->values(),
            'mapIncidents' => $allIncidents
            ->when(
                $user->isAdmin() || $user->isSuperAdmin(),
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
                'date' => $incident->created_at->format('d/m/Y H:i'),
                'dashboardUrl' => route('incidents.dashboard.store', $incident),
                'latitude' => $incident->latitude,
                'longitude' => $incident->longitude,
            ])->values(),
            'mapCenter' => [
                'latitude' => $user->commune?->latitude ?? $allIncidents->first()?->latitude ?? 6.3703,
                'longitude' => $user->commune?->longitude ?? $allIncidents->first()?->longitude ?? 2.3912,
                'zoom' => $user->isSuperAdmin() ? 9 : 13,
            ],
        ]);
    }

    public function storeInDashboard(Incident $incident): JsonResponse
    {
        $user = Auth::user();

        abort_unless($user->isAdmin() || $user->isSuperAdmin(), 403);
        abort_if(
            $user->isSuperAdmin()
                ? $user->department !== $incident->commune?->department
                : $user->commune_id !== $incident->commune_id,
            403,
        );

        $user->dashboardIncidents()->syncWithoutDetaching([$incident->id]);

        return response()->json([
            'message' => 'Plainte ajoutée au dashboard.',
        ]);
    }

    public function storePublic(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'custom_title' => ['nullable', 'required_if:title,Autre incident urbain', 'string', 'max:160'],
            'urgency' => ['required', 'in:normal,urgent,critique'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'geolocation_verified' => ['accepted'],
            'geolocation_accuracy' => ['nullable', 'numeric', 'min:0'],
            'location_country' => ['nullable', 'string', 'max:120'],
            'location_city' => ['nullable', 'string', 'max:120'],
            'location_zone' => ['nullable', 'string', 'max:160'],
            'location_address' => ['nullable', 'string', 'max:500'],
            'photo' => ['required', 'image', 'max:4096'],
        ], [
            'custom_title.required_if' => 'Précisez le titre de l’incident.',
            'latitude.required' => 'Activez la localisation avant d’envoyer le signalement.',
            'longitude.required' => 'Activez la localisation avant d’envoyer le signalement.',
            'geolocation_verified.accepted' => 'La localisation est obligatoire pour envoyer le signalement.',
            'photo.required' => 'La photo de l’incident est obligatoire.',
            'photo.image' => 'Le fichier envoyé doit être une image.',
        ]);

        if ($validated['title'] === 'Autre incident urbain') {
            $validated['title'] = $validated['custom_title'];
        }

        $commune = $this->communeFromLocation($validated);

        if (! $commune) {
            return back()
                ->withErrors(['location' => 'Votre zone n’est pas encore prise en charge. Le signalement ne peut pas être envoyé.'])
                ->withInput();
        }

        $directory = public_path('uploads/incidents');
        File::ensureDirectoryExists($directory);

        $file = $request->file('photo');
        $filename = uniqid('incident_', true).'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);

        $category = $this->categoryFromTitle($validated['title']);
        $district = collect([
            $validated['location_zone'] ?? null,
            $validated['location_city'] ?? null,
            $validated['location_country'] ?? null,
        ])->filter()->implode(' - ') ?: 'Zone GPS';

        $description = $validated['location_address'] ?? $validated['title'];

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
            'photo_path' => 'uploads/incidents/'.$filename,
        ]);

        User::whereIn('role', ['admin', 'agent', 'super_admin'])
            ->when($incident->commune_id, fn ($query) => $query->where(function ($query) use ($incident): void {
                $query->where('commune_id', $incident->commune_id)
                    ->orWhere(function ($departmentQuery) use ($incident): void {
                        $departmentQuery->where('role', 'super_admin')
                            ->where('department', $incident->commune?->department);
                    });
            }))
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

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'custom_title' => ['nullable', 'required_if:title,Autre incident urbain', 'string', 'max:160'],
            'category' => ['required', 'in:'.implode(',', array_keys(Incident::CATEGORIES))],
            'district' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:1200'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'geolocation_verified' => ['accepted'],
            'geolocation_accuracy' => ['nullable', 'numeric', 'min:0'],
            'urgency' => ['required', 'in:normal,urgent,critique'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ], [
            'latitude.required' => 'Activez la localisation GPS avant d’envoyer le signalement.',
            'longitude.required' => 'Activez la localisation GPS avant d’envoyer le signalement.',
            'geolocation_verified.accepted' => 'La localisation GPS est obligatoire pour envoyer une plainte.',
            'custom_title.required_if' => 'Précisez le titre de l’incident.',
        ]);

        if ($request->hasFile('photo')) {
            $directory = public_path('uploads/incidents');
            File::ensureDirectoryExists($directory);

            $file = $request->file('photo');
            $filename = uniqid('incident_', true).'.'.$file->getClientOriginalExtension();
            $file->move($directory, $filename);
            $validated['photo_path'] = 'uploads/incidents/'.$filename;
        }

        if ($validated['title'] === 'Autre incident urbain') {
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

        User::whereIn('role', ['admin', 'agent', 'super_admin'])
            ->where(function ($query) use ($incident): void {
                $query->where('commune_id', $incident->commune_id)
                    ->orWhere(function ($departmentQuery) use ($incident): void {
                        $departmentQuery->where('role', 'super_admin')
                            ->where('department', $incident->commune?->department);
                    });
            })
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
        abort_unless(Auth::user()->canManageIncidents(), 403);
        abort_if(
            Auth::user()->isSuperAdmin()
                ? Auth::user()->department !== $incident->commune?->department
                : Auth::user()->commune_id !== $incident->commune_id,
            403,
        );
        abort_if(Auth::user()->isAgent() && $incident->assigned_agent_id !== Auth::id(), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Incident::STATUSES))],
            'assigned_agent_id' => ['nullable', 'exists:users,id'],
        ]);

        if (Auth::user()->isAgent()) {
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
}
