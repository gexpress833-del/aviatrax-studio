@extends('layouts.app')

@section('title', 'Dashboard Artiste - Aviatrax Studio')

@section('content')
<div class="space-y-6">
    <div class="rounded-[28px] border border-orange-500/30 bg-gradient-to-r from-[#1d140a] via-[#2a160e] to-[#0d1d2a] p-5 shadow-[0_20px_45px_rgba(0,0,0,0.28)] sm:p-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-xl">
                <p class="text-[10px] font-black uppercase tracking-[0.28em] text-orange-300">Partenariat stratégique</p>
                <h2 class="mt-2 text-2xl font-black text-white sm:text-3xl">Bienvenue sur votre espace artiste !</h2>
                <p class="mt-3 text-sm leading-6 text-slate-200/90">Aviator Music œuvre en collaboration avec Maisha Sound pour accompagner les talents dans un cadre professionnel, créatif et rigoureux.</p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-4 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                <div class="flex h-20 w-28 items-center justify-center overflow-hidden rounded-xl bg-white p-2 shadow-lg shadow-black/20 ring-1 ring-slate-200">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo Aviator Music" class="max-h-full max-w-full object-contain">
                </div>
                <div class="text-xl font-black text-orange-300">×</div>
                <div class="flex h-20 w-28 items-center justify-center overflow-hidden rounded-xl bg-white p-2 shadow-lg shadow-black/20 ring-1 ring-slate-200">
                    <img src="{{ asset('logoMS.jpg') }}" alt="Logo Maisha Sound" class="max-h-full max-w-full object-contain">
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-[26px] border border-slate-700 bg-gradient-to-r from-slate-900 via-slate-900 to-slate-800 p-5 shadow-[0_20px_45px_rgba(0,0,0,0.35)] sm:p-6">
        <div class="flex flex-col items-center justify-center gap-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-2xl border border-slate-700 shadow-[0_18px_38px_rgba(0,0,0,0.35)]">
                <video autoplay loop muted playsinline poster="{{ asset('logo.jpg') }}" class="h-full w-full object-cover bg-black">
                    <source src="{{ asset('Vidéo label aviator.mp4') }}" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture vidéo.
                </video>
            </div>
            <p class="max-w-xl text-center text-sm text-slate-300">Découvrez l'univers Aviatrax Studio et lancez-vous dans vos projets musicaux avec passion !</p>
        </div>
    </div>

    <div id="mes-projets" class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-slate-100 mb-4">Mes projets</h2>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5 sm:gap-5 mb-6">
                <div class="rounded-lg border border-blue-500/20 bg-blue-500/10 p-4 text-center">
                    <div class="text-2xl font-bold text-blue-300">{{ $projetsParEtape['enregistrement'] }}</div>
                    <div class="text-sm text-blue-200">Enregistrement</div>
                </div>
                <div class="rounded-lg border border-pink-500/20 bg-pink-500/10 p-4 text-center">
                    <div class="text-2xl font-bold text-pink-300">{{ $projetsParEtape['mixage'] }}</div>
                    <div class="text-sm text-pink-200">Mixage</div>
                </div>
                <div class="rounded-lg border border-yellow-500/20 bg-yellow-500/10 p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-300">{{ $projetsParEtape['mastering'] }}</div>
                    <div class="text-sm text-yellow-200">Mastering</div>
                </div>
                <div class="rounded-lg border border-orange-500/20 bg-orange-500/10 p-4 text-center">
                    <div class="text-2xl font-bold text-orange-300">{{ $projetsParEtape['distribution_en_cours'] }}</div>
                    <div class="text-sm text-orange-200">Distribution</div>
                </div>
                <div class="rounded-lg border border-green-500/20 bg-green-500/10 p-4 text-center">
                    <div class="text-2xl font-bold text-green-300">{{ $projetsParEtape['distribution_terminee'] }}</div>
                    <div class="text-sm text-green-200">Terminé</div>
                </div>
            </div>

            @if($projets->count() > 0)
                <div class="space-y-4">
                    @foreach($projets as $projet)
                        <div class="rounded-lg border border-slate-700 bg-slate-800/70 p-4 transition-shadow hover:shadow-md hover:shadow-black/20">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-medium text-slate-100">{{ $projet->titre }}</h3>
                                    @if($projet->description)
                                        <p class="mt-1 text-slate-300">{{ Str::limit($projet->description, 100) }}</p>
                                    @endif
                                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-400">
                                        <span>Responsable: {{ $projet->responsable->name }}</span>
                                        @if($projet->date_debut)
                                            <span>Début: {{ $projet->date_debut->format('d/m/Y') }}</span>
                                        @endif
                                        @if($projet->date_fin_prevue)
                                            <span>Fin prévue: {{ $projet->date_fin_prevue->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="px-3 py-1 text-sm font-medium rounded-full text-white" style="background-color: {{ $projet->etape_color }}">
                                        {{ $projet->etape_label }}
                                    </span>
                                    <a href="{{ route('projets.show', $projet) }}" class="text-indigo-300 hover:text-indigo-200">Voir détails</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-6xl mb-4">🎵</div>
                    <h3 class="text-lg font-medium text-slate-100 mb-2">Aucun projet pour le moment</h3>
                    <p class="text-slate-400">Vos projets apparaîtront ici une fois qu'ils seront créés par le studio.</p>
                </div>
            @endif
        </div>
    </div>

    @if($projets->count() > 0)
        <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
            <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-slate-100 mb-4">Progression globale</h3>
                @php
                    $totalProjets = $projets->count();
                    $projetsTermines = $projets->where('etape', 'distribution_terminee')->count();
                    $pourcentage = $totalProjets > 0 ? ($projetsTermines / $totalProjets) * 100 : 0;
                @endphp
                <div class="h-4 w-full rounded-full bg-slate-700">
                    <div class="bg-green-600 h-4 rounded-full transition-all duration-300" style="width: {{ $pourcentage }}%"></div>
                </div>
                <div class="mt-2 text-sm text-slate-300">
                    {{ $projetsTermines }} sur {{ $totalProjets }} projets terminés ({{ number_format($pourcentage, 1) }}%)
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 