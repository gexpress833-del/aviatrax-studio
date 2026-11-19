@extends('layouts.app')

@section('title', 'Dashboard Admin - Aviatrax Studio')

@section('content')
<div class="space-y-6 text-white">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-blue-300">Dashboard Administrateur</h1>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.logs') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Voir les logs
            </a>
            <a href="{{ route('admin.users') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Gérer les utilisateurs
            </a>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">👥</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-400 truncate">Total utilisateurs</dt>
                            <dd class="text-lg font-medium text-slate-100">{{ $stats['total_users'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">⏳</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-400 truncate">En attente de validation</dt>
                            <dd class="text-lg font-medium text-slate-100">{{ $stats['users_en_attente'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">🎵</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-400 truncate">Total projets</dt>
                            <dd class="text-lg font-medium text-slate-100">{{ $stats['total_projets'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#101828] overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">▶️</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-400 truncate">Projets actifs</dt>
                            <dd class="text-lg font-medium text-slate-100">{{ $stats['projets_actifs'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Utilisateurs en attente de validation -->
        <div class="rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
            <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-slate-100 mb-4">Utilisateurs en attente de validation</h3>
                
                @if($usersEnAttente->count() > 0)
                    <div class="space-y-3">
                        @foreach($usersEnAttente as $user)
                            <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-700 bg-slate-800/80 p-3">
                                <div>
                                    <h4 class="font-medium text-slate-100">{{ $user->name }}</h4>
                                    <p class="text-sm text-slate-300">{{ $user->email }}</p>
                                    <p class="text-xs text-slate-400">Inscrit le {{ $user->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <form action="{{ route('admin.users.validate', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                            Valider
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.suspend', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                            Rejeter
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-4">✅</div>
                        <h3 class="text-lg font-medium text-slate-100 mb-2">Aucun utilisateur en attente</h3>
                        <p class="text-slate-400">Tous les utilisateurs ont été validés.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Projets récents -->
        <div class="rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
            <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-slate-100 mb-4">Projets récents</h3>
                
                @if($projetsRecents->count() > 0)
                    <div class="space-y-3">
                        @foreach($projetsRecents as $projet)
                            <div class="rounded-lg border border-slate-700 bg-slate-800/80 p-3">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-slate-100">{{ $projet->titre }}</h4>
                                        <p class="text-sm text-slate-300">{{ $projet->description }}</p>
                                        <div class="flex items-center space-x-4 mt-2 text-xs text-slate-400">
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
                        <p class="text-slate-400">Aucun projet n'a encore été créé.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($distributionProjects->isNotEmpty())
        <section class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-orange-300">Sorties en distribution directe</h3>
                <p class="mt-1 text-sm text-slate-400">Examinez chaque dépôt, puis fixez la date officielle lors de son approbation.</p>
                <div class="mt-4 space-y-3">
                    @foreach($distributionProjects as $distributionProject)
                        <article class="flex flex-col gap-4 rounded-lg border border-slate-700 bg-slate-800/70 p-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                @if($distributionProject->cover_art_url)
                                    <img src="{{ $distributionProject->cover_art_url }}" alt="Pochette de {{ $distributionProject->title }}" class="h-14 w-14 rounded-md object-cover">
                                @endif
                                <div class="min-w-0">
                                    <h4 class="truncate font-semibold text-slate-100">{{ $distributionProject->title }}</h4>
                                    <p class="text-sm text-slate-300">Artiste : {{ $distributionProject->artist?->stage_name ?: $distributionProject->artist?->name }}</p>
                                    <p class="text-xs text-slate-400">État : {{ $distributionProject->distribution_status === 'approved' ? 'Approuvé' : ($distributionProject->distribution_status === 'rejected' ? 'À corriger' : 'À examiner') }}</p>
                                </div>
                            </div>
                            <form action="{{ route('admin.distribution-projects.review', $distributionProject) }}" method="POST" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label for="release-date-{{ $distributionProject->id }}" class="mb-1 block text-xs font-medium text-slate-300">Date de sortie officielle</label>
                                    <input id="release-date-{{ $distributionProject->id }}" type="date" name="release_date" value="{{ $distributionProject->release_date?->format('Y-m-d') }}" class="rounded-md border border-slate-600 bg-slate-950 px-3 py-2 text-sm text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">
                                </div>
                                <div>
                                    <label for="distribution-status-{{ $distributionProject->id }}" class="mb-1 block text-xs font-medium text-slate-300">Décision</label>
                                    <select id="distribution-status-{{ $distributionProject->id }}" name="distribution_status" required class="rounded-md border border-slate-600 bg-slate-950 px-3 py-2 text-sm text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">
                                        <option value="approved">Approuver</option>
                                        <option value="rejected">Demander une correction</option>
                                    </select>
                                </div>
                                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">Enregistrer</button>
                            </form>
                            <div class="mt-3 border-t border-slate-700 pt-3 xl:min-w-64">
                                <p class="mb-2 text-xs font-medium text-slate-300">Liens des plateformes publiées</p>
                                @if($distributionProject->distributions->isNotEmpty())
                                    <ul class="mb-3 space-y-1 text-xs text-slate-400">
                                        @foreach($distributionProject->distributions as $distribution)
                                            <li>{{ $distribution->platform_name }}: {{ $distribution->status === 'published' ? 'en ligne' : 'en attente' }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <form action="{{ route('admin.distribution-projects.platforms.store', $distributionProject) }}" method="POST" class="flex flex-col gap-2 sm:flex-row">
                                    @csrf
                                    <select name="platform_name" required class="min-w-0 rounded-md border border-slate-600 bg-slate-950 px-2 py-2 text-sm text-white">
                                        @foreach(['Spotify', 'Apple Music', 'Boomplay', 'Deezer', 'TikTok', 'YouTube Music', 'Audiomack'] as $platform)
                                            <option value="{{ $platform }}">{{ $platform }}</option>
                                        @endforeach
                                    </select>
                                    <input type="url" name="platform_url" required placeholder="https://…" class="min-w-0 flex-1 rounded-md border border-slate-600 bg-slate-950 px-2 py-2 text-sm text-white placeholder:text-slate-500">
                                    <select name="status" class="rounded-md border border-slate-600 bg-slate-950 px-2 py-2 text-sm text-white">
                                        <option value="published">En ligne</option>
                                        <option value="pending">En attente</option>
                                    </select>
                                    <button type="submit" class="rounded-md border border-slate-600 bg-slate-700 px-3 py-2 text-sm font-medium text-white hover:bg-slate-600">Ajouter le lien</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Actions rapides -->
    <div class="rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-slate-100 mb-4">Actions rapides</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('admin.users') }}" class="flex items-center rounded-lg border border-blue-500/20 bg-blue-500/10 p-4 transition-colors hover:bg-blue-500/20">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">👥</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-slate-100">Gérer les utilisateurs</h4>
                        <p class="text-sm text-slate-300">Valider, suspendre, changer les rôles</p>
                    </div>
                </a>

                <a href="{{ route('projets.create') }}" class="flex items-center rounded-lg border border-green-500/20 bg-green-500/10 p-4 transition-colors hover:bg-green-500/20">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">➕</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-slate-100">Créer un projet</h4>
                        <p class="text-sm text-slate-300">Nouveau projet musical</p>
                    </div>
                </a>

                <a href="{{ route('projets.index') }}" class="flex items-center rounded-lg border border-purple-500/20 bg-purple-500/10 p-4 transition-colors hover:bg-purple-500/20">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">📋</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-slate-100">Voir tous les projets</h4>
                        <p class="text-sm text-slate-300">Liste complète des projets</p>
                    </div>
                </a>

                <a href="{{ route('admin.logs') }}" class="flex items-center rounded-lg border border-slate-700 bg-slate-800/70 p-4 transition-colors hover:bg-slate-800">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">📊</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-slate-100">Voir les logs</h4>
                        <p class="text-sm text-slate-300">Activité de l'application</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Demand
