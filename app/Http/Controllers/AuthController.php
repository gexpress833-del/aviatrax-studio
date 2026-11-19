<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        session()->forget('pending_user_id');

        return view('auth.login');
    }

    public function pendingApproval()
    {
        $user = Auth::check() ? Auth::user() : User::find(session('pending_user_id'));

        if ($user && $user->isValide()) {
            return redirect()->route('dashboard');
        }

        if (!$user || !$user->isPendingValidation()) {
            return redirect()->route('login');
        }

        return view('auth.pending', ['user' => $user]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L’adresse e-mail est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse e-mail valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée par un autre compte.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $roleName = 'artiste';
        $role = Role::firstOrCreate(
            ['name' => $roleName],
            ['description' => 'Artiste']
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'status' => 'valide',
            'statut' => 'valide',
        ]);

        Log::info('Nouvel utilisateur inscrit', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $roleName,
            'statut' => $user->statut,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Inscription réussie ! Votre compte artiste est actif.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'L’adresse e-mail est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse e-mail valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->isValide()) {
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Votre compte est actuellement indisponible.']);
            }

            $request->session()->regenerate();
            session()->forget('pending_user_id');
            
            Log::info('Connexion utilisateur', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.']);
    }

    public function logout(Request $request)
    {
        Log::info('Déconnexion utilisateur', ['user_id' => Auth::id()]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('pending_user_id');

        return redirect()->route('login');
    }
}