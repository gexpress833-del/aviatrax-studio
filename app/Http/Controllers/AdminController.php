<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Projet;
use App\Models\Project;
use App\Models\ProjectDistribution;
use App\Services\WalletService;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        Gate::authorize('admin-access');
        
        $stats = [
            'total_users' => User::count(),
            'users_en_attente' => User::where('statut', 'en_attente_validation')->count(),
            'total_projets' => Projet::count(),
            'projets_actifs' => Projet::where('statut', 'actif')->count(),
        ];

        $usersEnAttente = User::where('statut', 'en_attente_validation')->get();
        $projetsRecents = Projet::with(['artiste', 'responsable'])->latest()->take(5)->get();
        $distributionProjects = Project::with('artist')
            ->where('project_type', 'distribution_only')
            ->latest()
            ->take(10)
            ->get();
        $withdrawalRequests = WithdrawalRequest::with('user')
            ->where('status', 'en_attente')
            ->latest()
            ->get();

        return view('admin.dashboard', compact('stats', 'usersEnAttente', 'projetsRecents', 'distributionProjects', 'withdrawalRequests'));
    }

    public function users()
    {
        Gate::authorize('admin-access');
        
        $users = User::with('role')->get();
        $roles = Role::all();

        return view('admin.users', compact('users', 'roles'));
    }

    public function validateUser(User $user)
    {
        Gate::authorize('admin-access');
        
        $user->update(['status' => 'valide', 'statut' => 'valide']);

        Log::info('Utilisateur validé', [
            'user_id' => $user->id,
            'validated_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Utilisateur validé avec succès.');
    }

    public function updateUserRole(Request $request, User $user)
    {
        Gate::authorize('admin-access');
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $adminRole = Role::where('name', 'administrateur')->first();
        $currentUser = Auth::user();

        // Empêcher qu'un non-admin attribue le rôle administrateur
        if ($request->role_id == $adminRole->id && !$currentUser->isAdmin()) {
            return redirect()->back()->with('error', 'Seul un administrateur peut attribuer le rôle Administrateur.');
        }
        // Empêcher qu'un admin se retire lui-même le rôle admin
        if ($user->id == $currentUser->id && $request->role_id != $adminRole->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas retirer votre propre rôle Administrateur.');
        }

        // Récupérer l'ancien rôle de manière sécurisée
        $ancienRole = $user->role && $user->role->name ? $user->role->name : 'Aucun';
        $user->update(['role_id' => $request->role_id]);
        // Récupérer le nouveau rôle de manière sécurisée
        $nouveauRole = $user->fresh()->role && $user->fresh()->role->name ? $user->fresh()->role->name : 'Aucun';

        Log::info('Rôle utilisateur modifié', [
            'user_id' => $user->id,
            'ancien_role' => $ancienRole,
            'nouveau_role' => $nouveauRole,
            'modified_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Rôle mis à jour avec succès.');
    }

    public function suspendUser(User $user)
    {
        Gate::authorize('admin-access');
        
        $user->update(['status' => 'suspendu', 'statut' => 'suspendu']);

        Log::info('Utilisateur suspendu', [
            'user_id' => $user->id,
            'suspended_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Utilisateur suspendu avec succès.');
    }

    public function activateUser(User $user)
    {
        Gate::authorize('admin-access');
        
        $user->update(['status' => 'valide', 'statut' => 'valide']);

        Log::info('Utilisateur réactivé', [
            'user_id' => $user->id,
            'activated_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Utilisateur réactivé avec succès.');
    }

    public function updateProjectReleaseDate(Request $request, Projet $projet)
    {
        Gate::authorize('admin-access');

        $validated = $request->validate([
            'release_date' => 'nullable|date',
        ]);

        $projet->update(['release_date' => $validated['release_date'] ?? null]);

        Log::info('Date de sortie officielle mise à jour', [
            'project_id' => $projet->id,
            'release_date' => $validated['release_date'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'La date de sortie officielle a été mise à jour.');
    }

    public function updateDistributionProjectReleaseDate(Request $request, Project $project)
    {
        Gate::authorize('admin-access');

        $validated = $request->validate([
            'release_date' => 'nullable|date',
        ]);

        $project->update(['release_date' => $validated['release_date'] ?? null]);

        Log::info('Date de sortie officielle de la distribution mise à jour', [
            'project_id' => $project->id,
            'release_date' => $validated['release_date'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'La date de sortie officielle a été mise à jour.');
    }

    public function reviewDistributionProject(Request $request, Project $project)
    {
        Gate::authorize('admin-access');
        abort_unless($project->project_type === 'distribution_only', 404);

        $validated = $request->validate([
            'distribution_status' => 'required|in:approved,rejected',
            'release_date' => 'required_if:distribution_status,approved|nullable|date',
        ]);

        $project->update([
            'distribution_status' => $validated['distribution_status'],
            'release_date' => $validated['distribution_status'] === 'approved' ? $validated['release_date'] : null,
        ]);

        Log::info('Projet de distribution directe examiné', [
            'project_id' => $project->id,
            'distribution_status' => $project->distribution_status,
            'release_date' => $project->release_date?->toDateString(),
            'reviewed_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'La décision de distribution a été enregistrée.');
    }

    public function saveDistributionPlatform(Request $request, Project $project)
    {
        Gate::authorize('admin-access');
        abort_unless($project->project_type === 'distribution_only', 404);

        $validated = $request->validate([
            'platform_name' => 'required|in:Spotify,Apple Music,Boomplay,Deezer,TikTok,YouTube Music,Audiomack',
            'platform_url' => 'required|url:https|max:2048',
            'status' => 'required|in:published,pending',
        ]);

        $distribution = ProjectDistribution::query()
            ->where('project_id', $project->id)
            ->where('platform_name', $validated['platform_name'])
            ->first();

        if ($distribution) {
            $distribution->update([
                'platform_url' => $validated['platform_url'],
                'status' => $validated['status'],
            ]);
        } else {
            $project->distributions()->create([
                'platform_name' => $validated['platform_name'],
                'platform_url' => $validated['platform_url'],
                'status' => $validated['status'],
            ]);
        }

        return redirect()->back()->with('success', 'Le lien de distribution a été enregistré.');
    }

    public function logs()
    {
        Gate::authorize('admin-access');
        
        // Lire les logs Laravel
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (file_exists($logFile)) {
            $logs = file($logFile);
            $logs = array_slice($logs, -100); // Dernières 100 lignes
        }

        return view('admin.logs', compact('logs'));
    }

    public function currencySettings(WalletService $walletService)
    {
        Gate::authorize('admin-access');

        $pointsPerDollar = $walletService->pointsPerDollar();
        $cdfPerDollar = $walletService->cdfPerDollar();
        $withdrawalThreshold = $walletService->withdrawalThreshold();

        return view('admin.currency-settings', compact('pointsPerDollar', 'cdfPerDollar', 'withdrawalThreshold'));
    }

    public function updateCurrencySettings(Request $request)
    {
        Gate::authorize('admin-access');

        $validated = $request->validate([
            'points_per_usd' => 'required|integer|min:1|max:1000000',
            'cdf_per_usd' => 'required|numeric|min:0.0001|max:1000000000',
            'minimum_withdrawal_points' => 'required|integer|min:1|max:1000000000',
        ], [
            'points_per_usd.required' => 'Le nombre de points par dollar est obligatoire.',
            'points_per_usd.min' => 'Le nombre de points par dollar doit être supérieur à zéro.',
            'cdf_per_usd.required' => 'Le taux du franc congolais par dollar est obligatoire.',
            'cdf_per_usd.min' => 'Le taux CDF doit être supérieur à zéro.',
            'minimum_withdrawal_points.required' => 'Le seuil minimum de retrait est obligatoire.',
            'minimum_withdrawal_points.min' => 'Le seuil minimum doit être supérieur à zéro.',
        ]);

        Setting::updateOrCreate(
            ['key' => 'points_conversion_rate'],
            ['value' => [
                'points_per_dollar' => (int) $validated['points_per_usd'],
                'label' => $validated['points_per_usd'] . ' points = 1 USD',
                'currency' => 'USD',
            ]]
        );

        Setting::updateOrCreate(
            ['key' => 'currency_exchange_rate'],
            ['value' => [
                'cdf_per_usd' => (float) $validated['cdf_per_usd'],
                'base_currency' => 'USD',
                'target_currency' => 'CDF',
            ]]
        );

        Setting::updateOrCreate(
            ['key' => 'minimum_withdrawal_points'],
            ['value' => [
                'threshold' => (int) $validated['minimum_withdrawal_points'],
                'currency' => 'points',
            ]]
        );

        Log::info('Paramètres financiers mis à jour', [
            'points_per_usd' => (int) $validated['points_per_usd'],
            'cdf_per_usd' => (float) $validated['cdf_per_usd'],
            'minimum_withwithdrawal_points' => (int) $validated['minimum_withdrawal_points'],
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.currency.edit')->with('success', 'Les paramètres de devise et de retrait ont été mis à jour.');
    }

    public function createUser()
    {
        Gate::authorize('admin-access');

        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        Gate::authorize('admin-access');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:30',
            'role_id' => 'required|exists:roles,id',
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L’adresse e-mail est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse e-mail valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée par un autre compte.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 30 caractères.',
            'role_id.required' => 'Veuillez sélectionner un rôle.',
            'role_id.exists' => 'Le rôle sélectionné est invalide.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
            'role_id' => $request->role_id,
            'status' => 'valide',
            'statut' => 'valide',
        ]);

        Log::info('Nouvel utilisateur créé par admin', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('admin.users')->with('success', 'Utilisateur créé avec succès.');
    }

    public function deleteUser(User $user)
    {
        Gate::authorize('admin-access');

        // Empêcher de supprimer l'utilisateur actuel
        if ($user->id == Auth::id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Empêcher de supprimer le dernier admin
        $adminRole = Role::where('name', 'administrateur')->first();
        if ($user->role_id == $adminRole->id) {
            $adminCount = User::where('role_id', $adminRole->id)->count();
            if ($adminCount <= 1) {
                return redirect()->back()->with('error', 'Impossible de supprimer le dernier administrateur.');
            }
        }

        $userId = $user->id;
        $userEmail = $user->email;
        $user->delete();

        Log::info('Utilisateur supprimé par admin', [
            'user_id' => $userId,
            'email' => $userEmail,
            'deleted_by' => Auth::id()
        ]);

        return redirect()->route('admin.users')->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function approveWithdrawal(Request $request, WithdrawalRequest $withdrawal, WalletService $walletService)
    {
        Gate::authorize('admin-access');

        $validated = $request->validate([
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        try {
            $walletService->approveWithdrawal(
                $withdrawal,
                (int) Auth::id(),
                $validated['transaction_reference'] ?? null
            );
        } catch (\Throwable $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        Log::info('Demande de retrait approuvée', [
            'withdrawal_id' => $withdrawal->id,
            'processed_by' => Auth::id(),
            'transaction_reference' => $validated['transaction_reference'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Le retrait a été marqué comme payé.');
    }

    public function rejectWithdrawal(Request $request, WithdrawalRequest $withdrawal, WalletService $walletService)
    {
        Gate::authorize('admin-access');

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $walletService->rejectWithdrawal(
                $withdrawal,
                (int) Auth::id(),
                $validated['reason'] ?? null
            );
        } catch (\Throwable $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        Log::info('Demande de retrait rejetée', [
            'withdrawal_id' => $withdrawal->id,
            'processed_by' => Auth::id(),
            'reason' => $validated['reason'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Le retrait a été rejeté et les points ont été restitués.');
    }
}