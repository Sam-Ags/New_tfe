<?php

namespace App\Http\Controllers;

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
        abort_unless(Auth::user()->isAdmin(), 403);

        return view('authorities.create', [
            'authorities' => User::with('commune')
                ->whereIn('role', ['admin', 'agent'])
                ->whereNotNull('created_by_user_id')
                ->where('created_by_user_id', Auth::id())
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', 'in:admin,agent'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée. Choisissez une autre adresse.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $commune = $user->commune;

        abort_if($commune === null, 403);

        $department = $user->department ?: $commune->department;

        $createdAuthority = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'commune_id' => $commune->id ?? null,
            'department' => $department,
            'created_by_user_id' => $user->id,
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('authorities.create')
            ->with('success', $createdAuthority->role === 'agent'
                ? 'Compte agent créé avec succès. Identifiant agent : '.$createdAuthority->accountIdentifier().'.'
                : 'Compte autorité créé avec succès.'
            )
            ->with('created_authority', [
                'name' => $createdAuthority->name,
                'role' => $createdAuthority->role,
                'identifier' => $createdAuthority->accountIdentifier(),
                'email' => $createdAuthority->email,
                'phone' => $createdAuthority->phone,
            ]);
    }
}
