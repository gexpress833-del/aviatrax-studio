@extends('layouts.app')

@section('title', 'Tableau de bord - Responsable Studio')

@section('content')
<div class="space-y-6 py-4">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.25em] text-blue-400">Responsable studio</p>
            <h1 class="text-3xl font-bold text-white mt-2">Dashboard Responsable Studio</h1>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('projets.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 rounded-lg font-medium shadow-lg shadow-blue-900/30 transition">
                + Nouveau projet
            </a>
            <a href="{{ route('secretariat.artistes.create') }}" class="bg-orange-500 hover:bg-orange-400 text-slate-950 px-4 py-2.5 rounded-lg font-medium shadow-lg shadow-orange-900/30 transition">
                + Créer un compte artiste
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4">
            <p class="text-sm text-slate-400">Enregistrement</p>
            <p class="mt-2 text-3xl font-bold text-blue-400">{{ $projetsParEtape['enregistrement'] ?? 0 }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4">
            <p class="text-sm text-slate-400">Mixage</p>
            <p class="mt-2 text-3xl font-bold text-pink-400">{{ $projetsParEtape['mixage'] ?? 0 }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4">
            <p class="text-sm text-slate-400">Mastering</p>
            <p class="mt-2 text-3xl font-bold text-yellow-400">{{ $projetsParEtape['mastering'] ?? 0 }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4">
            <p class="text-sm text-slate-400">Distribution</p>
            <p class="mt-2 text-3xl font-bold text-orange-400">{{ $projetsParEtape['distribution_en_cours'] ?? 0 }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4">
            <p class="text-sm text-slate-400">Terminé</p>
            <p class="mt-2 text-3xl font-bold text-green-400">{{ $projetsParEtape['distribution_terminee'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-slate-900 border border-slate-700 rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-white">Suivi des projets</h2>
                <a href="{{ route('projets.index') }}" class="text-blue-400 hover:text-blue-300 text-sm">Voir tout</a>
            </div>

            @if($projets->count())
                <div class="space-y-4">
                    @foreach($projets as $projet)
                        @php
                            $color = match($projet->etape) {
                                'enregistrement' => '#60a5fa',
                                'mixage' => '#f472b6',
                                'mastering' => '#fbbf24',
                                'distribution_en_cours' => '#fb923c',
                                'distribution_terminee' => '#4ade80',
                                default => '#94a3b8',
                            };
                        @endphp

                        <div class="border border-slate-700 rounded-lg p-4 bg-slate-950/50">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-white">{{ $projet->titre }}</h3>
                                    <p class="text-sm text-slate-400 mt-1">{{ $projet->artiste->name ?? 'Artiste non assigné' }}</p>
                                </div>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium border" style="background-color: {{ $color }}20; color: {{ $color }}; border-color: {{ $color }}80;">
                                    {{ $projet->etape_label ?? ucfirst(str_replace('_', ' ', $projet->etape)) }}
                                </span>
                            </div>

                            <div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div class="text-sm text-slate-300">
                                    <p>Début : {{ $projet->date_debut ? \Carbon\Carbon::parse($projet->date_debut)->format('d/m/Y') : '—' }}</p>
                                </div>

                                <form action="{{ route('projets.updateEtape', $projet) }}" method="POST" class="w-full md:w-auto">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex gap-2 items-center">
                                        <select name="etape" class="rounded-md border border-slate-600 bg-slate-800 text-white text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="enregistrement" @selected($projet->etape === 'enregistrement')>Enregistrement</option>
                                            <option value="mixage" @selected($projet->etape === 'mixage')>Mixage</option>
                                            <option value="mastering" @selected($projet->etape === 'mastering')>Mastering</option>
                                            @if(auth()->user()->isAdmin())
                                                <option value="distribution_en_cours" @selected($projet->etape === 'distribution_en_cours')>Distribution en cours</option>
                                                <option value="distribution_terminee" @selected($projet->etape === 'distribution_terminee')>Distribution terminée</option>
                                            @endif
                                        </select>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-2 rounded-md text-sm font-medium">
                                            Mettre à jour
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="border border-dashed border-slate-700 rounded-xl p-10 text-center text-slate-400">
                    Aucun projet n’est actuellement assigné.
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
                <h2 class="text-xl font-semibold text-white mb-4">Gestion artistes</h2>
                <div class="space-y-3">
                    <a href="{{ route('secretariat.artistes.create') }}" class="block w-full bg-orange-500 hover:bg-orange-400 text-slate-950 font-medium px-4 py-3 rounded-lg text-center transition">
                        Créer un compte artiste
                    </a>
                    <a href="{{ route('projets.create') }}" class="block w-full bg-slate-800 hover:bg-slate-700 text-white font-medium px-4 py-3 rounded-lg text-center border border-slate-600 transition">
                        Créer un projet
                    </a>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
                <h2 class="text-xl font-semibold text-white mb-4">Artistes</h2>
                @php
                    $artistes = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'artiste'))->latest()->take(5)->get();
                @endphp

                @if($artistes->count())
                    <div class="space-y-3">
                        @foreach($artistes as $artiste)
                            <div class="flex items-center justify-between border border-slate-700 rounded-lg p-3 bg-slate-950/60">
                                <div>
                                    <p class="text-white font-medium">{{ $artiste->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $artiste->email }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $artiste->statut === 'valide' ? 'bg-green-500/20 text-green-300' : 'bg-yellow-500/20 text-yellow-300' }}">
                                    {{ $artiste->statut ?? 'valide' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-400 text-sm">Aucun artiste enregistré pour le moment.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
