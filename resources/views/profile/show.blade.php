@extends('layouts.app')

@section('title', 'Mon profil - Aviatrax Studio')

@section('content')
@php
    $roleLabels = [
        'administrateur' => 'Administrateur',
        'secretariat' => 'Secrétariat',
        'responsable_studio' => 'Responsable studio',
        'artiste' => 'Artiste',
    ];
    $accountStatus = $user->statut ?? $user->status ?? 'inconnu';
    $statusLabels = [
        'valide' => 'Compte actif',
        'en_attente_validation' => 'En attente de validation',
        'suspendu' => 'Compte suspendu',
    ];
    $statusClasses = [
        'valide' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
        'en_attente_validation' => 'border-amber-500/30 bg-amber-500/10 text-amber-300',
        'suspendu' => 'border-red-500/30 bg-red-500/10 text-red-300',
    ];
@endphp

<div class="mx-auto max-w-5xl space-y-6 text-white">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-orange-300">Compte personnel</p>
            <h1 class="mt-1 text-3xl font-bold text-blue-300">Mon profil</h1>
        </div>
        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-400 px-5 py-2.5 font-semibold text-slate-950 transition hover:bg-orange-300">
            Modifier le profil
        </a>
    </div>

    <section class="flex flex-col gap-6 rounded-xl border border-slate-700 bg-slate-900 p-6 sm:flex-row sm:items-center sm:p-8">
        @if($user->photo_path)
            <div class="h-24 w-24 shrink-0 overflow-hidden rounded-full border-2 border-blue-400 bg-slate-800">
                <img src="{{ $user->photo_url }}" alt="Photo de profil de {{ $user->name }}" class="h-full w-full object-cover" loading="lazy">
            </div>
        @else
            <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full border-2 border-blue-400 bg-blue-950 text-4xl font-semibold text-blue-200">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif

        <div class="min-w-0 flex-1">
            <p class="text-sm text-slate-400">{{ $user->isArtiste() && $user->stage_name ? 'Nom de scène' : 'Nom du compte' }}</p>
            <h2 class="mt-1 break-words text-2xl font-bold text-white">
                {{ $user->isArtiste() ? ($user->stage_name ?: $user->name) : $user->name }}
            </h2>
            @if($user->isArtiste() && $user->stage_name)
                <p class="mt-1 text-sm text-slate-300">{{ $user->name }}</p>
            @endif
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                    {{ $roleLabels[$user->role?->name ?? ''] ?? ($user->role?->name ?? 'Rôle non défini') }}
                </span>
                <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClasses[$accountStatus] ?? 'border-slate-600 bg-slate-800 text-slate-300' }}">
                    {{ $statusLabels[$accountStatus] ?? 'Statut non défini' }}
                </span>
                <span class="text-xs text-slate-400">Membre depuis le {{ $user->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
    </section>

    @if($profileStats)
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3" aria-label="Résumé artiste">
            <div class="rounded-xl border border-slate-700 bg-slate-900 p-5">
                <p class="text-sm text-slate-400">Mes projets</p>
                <p class="mt-2 text-3xl font-bold text-blue-300">{{ $profileStats['projects'] }}</p>
                <a href="{{ route('projets.index') }}" class="mt-3 inline-block text-sm font-medium text-blue-300 hover:text-blue-200">Voir mes projets</a>
            </div>
            <div class="rounded-xl border border-slate-700 bg-slate-900 p-5">
                <p class="text-sm text-slate-400">Projets distribués</p>
                <p class="mt-2 text-3xl font-bold text-emerald-300">{{ $profileStats['completed_projects'] }}</p>
                <p class="mt-3 text-sm text-slate-400">Distribution terminée</p>
            </div>
            <div class="rounded-xl border border-slate-700 bg-slate-900 p-5">
                <p class="text-sm text-slate-400">Solde de points</p>
                <p class="mt-2 text-3xl font-bold text-orange-300">{{ number_format($profileStats['wallet_points'], 0, ',', ' ') }}</p>
                <a href="{{ route('wallet') }}" class="mt-3 inline-block text-sm font-medium text-orange-300 hover:text-orange-200">Ouvrir mon portefeuille</a>
            </div>
        </section>
    @endif

    <section class="rounded-xl border border-slate-700 bg-slate-900 p-6 sm:p-8">
        <div class="mb-5 border-b border-slate-700 pb-4">
            <h2 class="text-lg font-semibold text-white">Coordonnées</h2>
            <p class="mt-1 text-sm text-slate-400">Informations utilisées pour vous contacter et accéder à votre compte.</p>
        </div>
        <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-slate-400">Nom complet</dt>
                <dd class="mt-1 break-words text-base font-medium text-white">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-400">Adresse e-mail</dt>
                <dd class="mt-1 break-all text-base font-medium text-white">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-400">Téléphone</dt>
                <dd class="mt-1 text-base font-medium text-white">{{ $user->telephone ?: 'Non renseigné' }}</dd>
            </div>
            @if($user->isArtiste())
                <div>
                    <dt class="text-sm text-slate-400">Nom de scène</dt>
                    <dd class="mt-1 text-base font-medium text-white">{{ $user->stage_name ?: 'Non renseigné' }}</dd>
                </div>
            @endif
            @if($user->isArtiste() && $user->responsable)
                <div>
                    <dt class="text-sm text-slate-400">Responsable studio</dt>
                    <dd class="mt-1 text-base font-medium text-white">{{ $user->responsable->name }}</dd>
                </div>
            @endif
        </dl>
    </section>
</div>
@endsection 