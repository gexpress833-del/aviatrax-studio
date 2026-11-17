@extends('layouts.app')

@section('title', 'Dashboard Secrétariat - Aviatrax Studio')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-blue-300">Dashboard Secrétariat</h1>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('secretariat.artistes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Nouvel artiste
            </a>
            <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-slate-200 transition hover:bg-slate-700">
                Dashboard principal
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
                            <span class="text-white text-sm font-medium">👥</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-400 truncate">Total artistes</dt>
                            <dd class="text-lg font-medium text-slate-100">{{ $stats['total_artistes'] }}</dd>
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
                            <span class="text-white text-sm font-medium">✅</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-400 truncate">Artistes actifs</dt>
                            <dd class="text-lg font-medium text-slate-100">{{ $stats['artistes_actifs'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">⏳</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-400 truncate">En attente</dt>
                            <dd class="text-lg font-medium text-slate-100">{{ $stats['artistes_en_attente'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des artistes -->
    <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-medium text-slate-100">Gestion des artistes</h3>
                <a href="{{ route('secretariat.artistes.create') }}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                    + Nouvel artiste
                </a>
            </div>
            
            @if($artistes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-[900px] divide-y divide-slate-700">
                        <thead class="bg-slate-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Artiste</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Date d'inscription</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Responsable studio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 bg-slate-900">
                            @foreach($artistes as $artiste)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-100">{{ $artiste->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-200">{{ $artiste->email }}</div>
                                        <div class="text-sm text-slate-400">{{ $artiste->telephone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($artiste->statut === 'valide') bg-green-500/15 text-green-300
                                            @elseif($artiste->statut === 'suspendu') bg-red-500/15 text-red-300
                                            @else bg-yellow-500/15 text-yellow-300 @endif">
                                            {{ ucfirst($artiste->statut) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                                        {{ $artiste->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-200">
                                        @if($artiste->statut === 'valide')
                                            @if(auth()->user()->isSecretariat() || auth()->user()->isAdmin())
                                                <form action="{{ route('secretariat.artistes.affecter', $artiste) }}" method="POST" class="flex items-center space-x-2">
                                                    @csrf
                                                    <select name="responsable_id" class="rounded-md border border-slate-600 bg-slate-950 text-sm text-slate-100" onchange="this.form.submit()">
                                                        <option value="">-- Choisir --</option>
                                                        @php
                                                            $responsables = \App\Models\User::whereHas('role', function($q){ $q->where('name', 'responsable_studio'); })->get();
                                                        @endphp
                                                        @foreach($responsables as $responsable)
                                                            <option value="{{ $responsable->id }}" {{ $artiste->responsable_id == $responsable->id ? 'selected' : '' }}>
                                                                {{ $responsable->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if($artiste->responsable)
                                                        <span class="ml-2 text-xs text-blue-300">({{ $artiste->responsable->name }})</span>
                                                    @endif
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-xs text-slate-400">Affectation impossible</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('secretariat.artistes.edit', $artiste) }}" 
                                               class="text-blue-300 hover:text-blue-200">Modifier</a>
                                            
                                            @if($artiste->statut === 'valide')
                                                <form action="{{ route('secretariat.artistes.suspend', $artiste) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Suspendre</button>
                                                </form>
                                            @else
                                                <form action="{{ route('secretariat.artistes.activate', $artiste) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Réactiver</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-4xl mb-4">🎵</div>
                    <h3 class="text-lg font-medium text-slate-100 mb-2">Aucun artiste</h3>
                    <p class="text-slate-400">Commencez par créer votre premier artiste.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-slate-100 mb-4">Actions rapides</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('secretariat.artistes.create') }}" class="flex items-center rounded-lg border border-blue-500/20 bg-blue-500/10 p-4 transition-colors hover:bg-blue-500/20">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">➕</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-slate-100">Créer un artiste</h4>
                        <p class="text-sm text-slate-300">Nouveau compte artiste</p>
                    </div>
                </a>

                <a href="{{ route('dashboard') }}" class="flex items-center rounded-lg border border-green-500/20 bg-green-500/10 p-4 transition-colors hover:bg-green-500/20">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">🏠</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-slate-100">Dashboard principal</h4>
                        <p class="text-sm text-slate-300">Retour au dashboard</p>
                    </div>
                </a>

                <a href="{{ route('projets.index') }}" class="flex items-center rounded-lg border border-purple-500/20 bg-purple-500/10 p-4 transition-colors hover:bg-purple-500/20">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">📋</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-slate-100">Voir les projets</h4>
                        <p class="text-sm text-slate-300">Liste des projets</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 