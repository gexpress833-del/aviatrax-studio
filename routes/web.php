<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SecretariatController;
use App\Http\Controllers\StudioController;

// 🔐 Authentification
Route::get('/compte-en-attente', [AuthController::class, 'pendingApproval'])->name('account.pending');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// 🌐 Routes protégées
Route::middleware('auth')->group(function () {

    // Dashboard commun
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 🛠️ Routes des projets générales (pour les admins ou accès global)
    Route::resource('projets', ProjetController::class);
    Route::post('/projets/{projet}/etape', [ProjetController::class, 'updateEtape'])->name('projets.update-etape');

    // Ajout de la nouvelle route
    Route::put('/projets/{projet}/etape', [ProjetController::class, 'updateEtape'])->name('projets.updateEtape');

    // Routes pour les fichiers média
    Route::post('/projets/{projet}/upload-media', [ProjetController::class, 'uploadMedia'])->name('projets.upload-media');
    Route::get('/projets/{projet}/download-audio', [ProjetController::class, 'downloadAudio'])->name('projets.download-audio');
    Route::get('/projets/{projet}/download-video', [ProjetController::class, 'downloadVideo'])->name('projets.download-video');
    Route::post('/projets/{projet}/validate-files', [ProjetController::class, 'validateFiles'])->name('projets.validate-files');

    // Profil utilisateur
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'showProfile'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\UserController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('profile.update');
    Route::middleware('role:artiste')->group(function () {
        Route::get('/wallet', [App\Http\Controllers\UserController::class, 'wallet'])->name('wallet');
        Route::post('/wallet/withdraw', [App\Http\Controllers\UserController::class, 'storeWithdrawalRequest'])->name('wallet.withdraw');
    });
    Route::post('/notifications/read-all', [App\Http\Controllers\UserController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');
    Route::get('/notifications/feed', [App\Http\Controllers\UserController::class, 'notificationFeed'])->name('notifications.feed');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\UserController::class, 'markNotificationAsRead'])->name('notifications.read');

    // 🛡️ Administration
    Route::middleware('role:administrateur')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
        Route::post('/users/{user}/validate', [AdminController::class, 'validateUser'])->name('users.validate');
        Route::post('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
        Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('users.suspend');
        Route::post('/users/{user}/activate', [AdminController::class, 'activateUser'])->name('users.activate');
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
        Route::get('/settings/currency', [AdminController::class, 'currencySettings'])->name('currency.edit');
        Route::put('/settings/currency', [AdminController::class, 'updateCurrencySettings'])->name('currency.update');
    });

    // 📋 Gestion des artistes - autorisée au secrétariat, responsable studio et admin
    Route::middleware(['auth', 'artiste-management'])->prefix('secretariat')->name('secretariat.')->group(function () {
        Route::get('/', [SecretariatController::class, 'dashboard'])->name('dashboard');
        Route::get('/artistes/create', [SecretariatController::class, 'createArtiste'])->name('artistes.create');
        Route::post('/artistes', [SecretariatController::class, 'storeArtiste'])->name('artistes.store');
        Route::get('/artistes/{artiste}/edit', [SecretariatController::class, 'editArtiste'])->name('artistes.edit');
        Route::put('/artistes/{artiste}', [SecretariatController::class, 'updateArtiste'])->name('artistes.update');
        Route::post('/artistes/{artiste}/suspend', [SecretariatController::class, 'suspendArtiste'])->name('artistes.suspend');
        Route::post('/artistes/{artiste}/activate', [SecretariatController::class, 'activateArtiste'])->name('artistes.activate');
        Route::post('/artistes/{artiste}/affecter', [SecretariatController::class, 'affecterResponsable'])->name('artistes.affecter');
    });

    // 🎛️ Responsable studio
    Route::middleware('role:responsable_studio')->prefix('dashboard/responsable')->name('dashboard.responsable.')->group(function () {
        Route::get('/', [DashboardController::class, 'responsable'])->name('index');

        Route::get('/projets/create', [StudioController::class, 'createProjet'])->name('projets.create');
        Route::post('/projets', [StudioController::class, 'storeProjet'])->name('projets.store');

        Route::get('/projets/{projet}', [StudioController::class, 'showProjet'])->name('projets.show');
        Route::get('/projets/{projet}/edit', [StudioController::class, 'editProjet'])->name('projets.edit');
        Route::put('/projets/{projet}', [StudioController::class, 'updateProjet'])->name('projets.update');
        Route::post('/projets/{projet}/etape', [StudioController::class, 'updateEtape'])->name('projets.update-etape');
        Route::post('/projets/{projet}/upload-media', [StudioController::class, 'uploadMedia'])->name('projets.upload-media');
    });
});

// 🔁 Route par défaut
Route::get('/', function () {
    return redirect()->route('dashboard');
});