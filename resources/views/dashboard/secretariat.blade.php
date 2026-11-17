@extends('layouts.app')

@section('title', 'Dashboard Secrétariat - Aviatrax Studio')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-blue-300">Dashboard Secrétariat</h1>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('projets.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Nouveau projet
            </a>
            <a href="{{ route('projets.index') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Voir les projets
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">📋</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-400 truncate">Total projets</dt>
                            <dd class="text-lg font-medium text-slate-100">{{ $projets->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">▶️</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-400 truncate">Projets actifs</dt>
                            <dd class="text-lg font-medium text-slate-100">{{ $projets->where('statut', 'actif')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">✅</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-400 truncate">Projets terminés</dt>
                            <dd class="text-lg font-medium text-slate-100">{{ $projets->where('statut', 'termine')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projets récents -->
    <div class="rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-slate-100 mb-4">Projets récents</h3>
            
            @if($projets->count() > 0)
                <div class="space-y-3">
                    @foreach($projets->take(5) as $projet)
                        <div class="rounded-lg border border-slate-700 bg-slate-800/80 p-3">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-slate-100">{{ $projet->titre }}</h4>
                                    <p class="text-sm text-slate-300">{{ $projet->description }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400">
                                        <span>Artiste: {{ $projet->artiste->name ?? 'Non assigné' }}</span>
                                        <span>Responsable: {{ $projet->responsable->name ?? 'Non assigné' }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($projet->statut === 'actif') bg-green-500/15 text-green-300
                                        @elseif($projet->statut === 'termine') bg-slate-700 text-slate-300
                                        @else bg-yellow-500/15 text-yellow-300 @endif">
                                        {{ ucfirst($projet->statut) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('projets.show', $projet) }}" class="text-blue-300 hover:text-blue-200 text-sm">
                                    Voir les détails →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-4xl mb-4">🎵</div>
                    <h3 class="text-lg font-medium text-slate-100 mb-2">Aucun projet</h3>
                    <p class="text-slate-400">Commencez par créer votre premier projet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-slate-100 mb-4">Actions rapides</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('projets.create') }}" class="flex items-center rounded-lg border border-blue-500/20 bg-blue-500/10 p-4 transition-colors hover:bg-blue-500/20">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">➕</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-slate-100">Créer un projet</h4>
                        <p class="text-sm text-slate-300">Nouveau projet musical</p>
                    </div>
                </a>

                <a href="{{ route('projets.index') }}" class="flex items-center rounded-lg border border-green-500/20 bg-green-500/10 p-4 transition-colors hover:bg-green-500/20">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">📋</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-slate-100">Gérer les projets</h4>
                        <p class="text-sm text-slate-300">Voir et modifier les projets</p>
                    </div>
                </a>

                <a href="{{ route('dashboard') }}" class="flex items-center rounded-lg border border-purple-500/20 bg-purple-500/10 p-4 transition-colors hover:bg-purple-500/20">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">🏠</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-slate-100">Dashboard principal</h4>
                        <p class="text-sm text-slate-300">Retour au dashboard</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 