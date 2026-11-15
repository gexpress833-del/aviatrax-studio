<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Services\WalletService;

class UserController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user();
        $user->load(['role', 'responsable']);
        $profileStats = null;

        if ($user->isArtiste()) {
            $projects = $user->projetsArtiste()->get(['id', 'etape']);
            $profileStats = [
                'projects' => $projects->count(),
                'completed_projects' => $projects->where('etape', 'distribution_terminee')->count(),
                'wallet_points' => $user->wallet()->value('points_balance') ?? 0,
            ];
        }

        return view('profile.show', compact('user', 'profileStats'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stage_name' => $user->isArtiste() ? 'nullable|string|max:255' : 'prohibited',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:30',
            'photo' => 'nullable|image|max:51200', // 50MB
        ], [
            'photo.max' => 'La photo ne doit pas dépasser 50MB (limite serveur PHP).',
        ]);

        if ($request->hasFile('photo')) {
            if (! $user::isCloudinaryConfigured()) {
                return back()->withInput()->withErrors([
                    'photo' => 'Le stockage Cloudinary n’est pas configuré. La photo ne peut pas être enregistrée.',
                ]);
            }

            if ($user->photo_path) {
                try {
                    Storage::disk('cloudinary')->delete($user->photo_path);
                } catch (\Throwable $e) {
                    // Keep the new upload even if an old remote asset cannot be removed.
                }
            }

            $filename = 'profile_' . $user->id . '_' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $path = $request->file('photo')->storeAs('profiles', $filename, 'cloudinary');

            if (! $path) {
                return back()->withInput()->withErrors([
                    'photo' => 'Échec de l’envoi de la photo vers Cloudinary. Réessayez plus tard.',
                ]);
            }

            $user->photo_path = $path;
        }

        $user->name = $validated['name'];
        if ($user->isArtiste()) {
            $user->stage_name = $validated['stage_name'] ?? null;
        }
        $user->email = $validated['email'];
        $user->telephone = $validated['telephone'] ?? null;
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profil mis à jour avec succès.');
    }

    public function wallet()
    {
        $user = Auth::user();
        $wallet = $user->wallet()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'points_balance' => 0,
        ]);
        $withdrawals = $user->withdrawalRequests()->latest()->get();
        $walletService = app(WalletService::class);
        $withdrawalThreshold = $walletService->withdrawalThreshold();
        $pointsPerDollar = $walletService->pointsPerDollar();
        $cdfPerDollar = $walletService->cdfPerDollar();

        return view('wallet.index', compact('user', 'wallet', 'withdrawals', 'withdrawalThreshold', 'pointsPerDollar', 'cdfPerDollar'));
    }

    public function storeWithdrawalRequest(Request $request)
    {
        $user = Auth::user();
        $walletService = app(WalletService::class);
        $wallet = $user->wallet()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'points_balance' => 0,
        ]);

        $request->validate([
            'points_amount' => ['required', 'integer', 'min:' . $walletService->withdrawalThreshold(), 'max:' . $wallet->points_balance],
            'currency' => 'required|in:USD,CDF',
            'payment_method' => 'required|in:bank_transfer,mobile_money,crypto',
            'payment_account' => 'required|string|min:4|max:255',
        ], [
            'points_amount.required' => 'Le montant du retrait est obligatoire.',
            'points_amount.min' => 'Le montant minimum de retrait est de ' . number_format($walletService->withdrawalThreshold(), 0, ',', ' ') . ' points.',
            'points_amount.max' => 'Le montant demandé dépasse votre solde disponible.',
            'currency.required' => 'Veuillez choisir la devise de paiement.',
            'currency.in' => 'La devise sélectionnée n’est pas prise en charge.',
            'payment_method.required' => 'Le mode de paiement est obligatoire.',
            'payment_method.in' => 'Le mode de paiement sélectionné est invalide.',
            'payment_account.required' => 'Le compte de paiement est obligatoire.',
            'payment_account.min' => 'Le compte de paiement est trop court.',
        ]);

        if ($request->currency === 'CDF' && $walletService->cdfPerDollar() <= 0) {
            return redirect()->back()->withErrors(['currency' => 'Le taux USD/CDF n’a pas encore été défini par l’administrateur.'])->withInput();
        }

        $cashAmount = $walletService->toCash((int) $request->points_amount, $request->currency);

        \App\Models\WithdrawalRequest::create([
            'user_id' => $user->id,
            'points_amount' => (int) $request->points_amount,
            'cash_amount' => $cashAmount,
            'currency' => $request->currency,
            'payment_method' => $request->payment_method,
            'payment_account' => $request->payment_account,
            'status' => 'en_attente',
        ]);

        return redirect()->route('wallet')->with('success', 'Votre demande de retrait a bien été enregistrée et est en attente de validation.');
    }

    public function notificationFeed()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->limit(8)->get();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications->map(function ($notification) {
                $data = $notification->data ?? [];
                $projectId = $data['projet_id'] ?? null;

                return [
                    'id' => $notification->id,
                    'title' => $data['titre'] ?? ($data['artiste_name'] ?? 'Nouvelle notification'),
                    'message' => $data['etape_label'] ?? ($data['artiste_email'] ?? 'Vous avez une mise à jour importante.'),
                    'url' => $projectId ? route('projets.show', $projectId) : route('dashboard'),
                    'created_at' => $notification->created_at->diffForHumans(),
                    'read' => ! is_null($notification->read_at),
                ];
            })->values(),
        ]);
    }

    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();

        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function markNotificationAsRead($notificationId)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $notification = $user->notifications()->whereKey($notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }
} 