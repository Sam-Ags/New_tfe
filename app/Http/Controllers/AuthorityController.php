<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthorityController extends Controller
{
    public function create(): View
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin(), 403);

        $communes = Commune::orderBy('department')
            ->orderBy('name')
            ->get();

        return view('authorities.create', [
            'authorities' => User::with('commune')
                ->whereIn('role', ['admin', 'agent', 'super_admin'])
                ->when(Auth::user()->isSuperAdmin(), fn ($query) => $query->where('department', Auth::user()->department))
                ->latest()
                ->get(),
            'communesForSelect' => $communes->map(fn (Commune $commune) => [
                'id' => $commune->id,
                'name' => $commune->name,
                'department' => $commune->department,
            ])->values(),
            'departments' => $communes->pluck('department')->unique()->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', 'in:admin,agent,super_admin'],
            'department' => ['required', 'string', 'max:120'],
            'commune_id' => ['nullable', 'required_unless:role,super_admin', 'exists:communes,id'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée. Choisissez une autre adresse.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'commune_id.required_unless' => 'La commune est obligatoire pour un administrateur communal ou un agent technique.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        if (Auth::user()->isSuperAdmin() && $validated['department'] !== Auth::user()->department) {
            abort(403);
        }

        if ($validated['role'] !== 'super_admin') {
            $commune = Commune::where('department', $validated['department'])
                ->findOrFail($validated['commune_id']);
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'commune_id' => $commune->id ?? null,
            'department' => $validated['department'],
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('authorities.create')
            ->with('success', 'Compte autorité créé avec succès.');
    }
}
