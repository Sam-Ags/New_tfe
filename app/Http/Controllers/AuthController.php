<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showAdminLogin(Request $request): View
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! User::where('email', $credentials['email'])->exists()) {
            return back()
                ->withErrors(['email' => 'Aucun compte inscrit avec cette adresse email.'])
                ->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('incidents.index'));
    }

    public function showRegister(): View
    {
        $communes = Commune::orderBy('department')
            ->orderBy('name')
            ->get();

        return view('auth.register', [
            'communes' => $communes,
            'communesForSelect' => $communes->map(fn (Commune $commune) => [
                'id' => $commune->id,
                'name' => $commune->name,
                'department' => $commune->department,
            ])->values(),
            'departments' => $communes->pluck('department')->unique()->values(),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'commune_id' => ['required', 'exists:communes,id'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $commune = Commune::findOrFail($validated['commune_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'commune_id' => $commune->id,
            'department' => $commune->department,
            'password' => $validated['password'],
            'role' => 'citoyen',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('incidents.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function updateAgentProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user->isAgent(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'npi' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:255'],
            'sex' => ['nullable', 'in:homme,femme,autre'],
            'profile_photo' => ['nullable', 'image', 'max:8192'],
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'profile_photo.image' => 'La photo de profil doit être une image.',
        ]);

        if ($request->hasFile('profile_photo')) {
            $directory = public_path('uploads/profiles');
            File::ensureDirectoryExists($directory);

            $file = $request->file('profile_photo');
            $filename = uniqid('agent_', true).'.'.$file->getClientOriginalExtension();
            $file->move($directory, $filename);

            $validated['profile_photo_path'] = 'uploads/profiles/'.$filename;
        }

        unset($validated['profile_photo']);

        $user->update($validated);

        return redirect()
            ->to(route('incidents.index').'#profil')
            ->with('success', 'Profil agent mis à jour.');
    }
}
