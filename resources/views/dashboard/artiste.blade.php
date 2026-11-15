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

    <div class="overflow-hidden rounded-[26px] border border-sky-900/70 bg-gradient-to-r from-[#f7e8d6] via-[#fff7d6] to-[#d9f7df] p-5 shadow-[0_20px_45px_rgba(15,23,42,0.15)] sm:p-6">
        <div class="flex flex-col items-center justify-center gap-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-2xl border-4 border-white shadow-[0_18px_38px_rgba(0,0,0,0.18)]">
                <video autoplay loop muted playsinline poster="{{ asset('logo.jpg') }}" class="h-full w-full object-cover bg-black">
                    <source src="{{ asset('Vidéo label aviator.mp4') }}" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture vidéo.
                </video>
            </div>
            <p class="max-w-xl text-center text-sm text-slate-700">Découvrez l'univers Aviatrax Studio et lancez-vous dans vos projets musicaux avec passion !</p>
        </div>
    </div>

    <div id="mes-projets" class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Mes projets</h2>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5 sm:gap-5 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $projetsParEtape['enregistrement'] }}</div>
                    <div class="text-sm text-blue-500">Enregistrement</div>
                </div>
                <div class="bg-pink-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-pink-600">{{ $projetsParEtape['mixage'] }}</div>
                    <div class="text-sm text-pink-500">Mixage</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $projetsParEtape['mastering'] }}</div>
                    <div class="text-sm text-yellow-500">Mastering</div>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $projetsParEtape['distribution_en_cours'] }}</div>
                    <div class="text-sm text-orange-500">Distribution</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $projetsParEtape['distribution_terminee'] }}</div>
                    <div class="text-sm text-green-500">Terminé</div>
                </div>
            </div>

            @if($projets->count() > 0)
                <div class="space-y-4">
                    @foreach($projets as $projet)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $projet->titre }}</h3>
                                    @if($projet->description)
                                        <p class="text-gray-600 mt-1">{{ Str::limit($projet->description, 100) }}</p>
                                    @endif
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-500">
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
                                    <a href="{{ route('projets.show', $projet) }}" class="text-indigo-600 hover:text-indigo-900">Voir détails</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-6xl mb-4">🎵</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun projet pour le moment</h3>
                    <p class="text-gray-500">Vos projets apparaîtront ici une fois qu'ils seront créés par le studio.</p>
                </div>
            @endif
        </div>
    </div>

    @if($projets->count() > 0)
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Progression globale</h3>
                @php
                    $totalProjets = $projets->count();
                    $projetsTermines = $projets->where('etape', 'distribution_terminee')->count();
                    $pourcentage = $totalProjets > 0 ? ($projetsTermines / $totalProjets) * 100 : 0;
                @endphp
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-green-600 h-4 rounded-full transition-all duration-300" style="width: {{ $pourcentage }}%"></div>
                </div>
                <div class="mt-2 text-sm text-gray-600">
                    {{ $projetsTermines }} sur {{ $totalProjets }} projets terminés ({{ number_format($pourcentage, 1) }}%)
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 