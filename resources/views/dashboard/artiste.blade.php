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
                                    @if($projet->release_date)
                                        <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300">Sortie prévue le {{ $projet->release_date->format('d/m/Y') }}</span>
                                    @endif
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

    <section id="release-hub" class="rounded-xl border border-slate-700 bg-slate-900/90 p-5 shadow-xl shadow-slate-950/30 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Distribution directe</p>
                <h2 class="mt-1 text-xl font-semibold text-white">Release Hub</h2>
                <p class="mt-1 text-sm text-slate-400">Envoyez un master déjà prêt et sa pochette pour validation de l’équipe.</p>
            </div>
            @if($activeSubscription)
                <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-sm font-medium text-emerald-300">Abonnement actif jusqu’au {{ $activeSubscription->ends_at->format('d/m/Y') }}</span>
            @else
                <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">Voir l’abonnement de distribution</a>
            @endif
        </div>

        @if($activeSubscription)
            <form action="{{ route('distribution.projects.store') }}" method="POST" enctype="multipart/form-data" class="mt-5 grid grid-cols-1 gap-4 border-t border-slate-700 pt-5 md:grid-cols-2">
                @csrf
                <div class="md:col-span-2">
                    <label for="distribution-title" class="mb-1.5 block text-sm font-medium text-slate-200">Titre de sortie</label>
                    <input id="distribution-title" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-2.5 text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">
                    @error('title')<p class="mt-1 text-sm text-red-300">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="distribution-description" class="mb-1.5 block text-sm font-medium text-slate-200">Description (facultative)</label>
                    <textarea id="distribution-description" name="description" rows="3" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-2.5 text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label for="distribution-master" class="mb-1.5 block text-sm font-medium text-slate-200">Master audio</label>
                    <input id="distribution-master" name="master" type="file" accept=".mp3,.wav,.aac,.flac,.m4a" required class="block w-full text-sm text-slate-300 file:mr-3 file:rounded-md file:border-0 file:bg-slate-700 file:px-3 file:py-2 file:text-slate-100 hover:file:bg-slate-600">
                    @error('master')<p class="mt-1 text-sm text-red-300">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="distribution-cover" class="mb-1.5 block text-sm font-medium text-slate-200">Pochette</label>
                    <input id="distribution-cover" name="cover_art" type="file" accept="image/jpeg,image/png,image/webp" required class="block w-full text-sm text-slate-300 file:mr-3 file:rounded-md file:border-0 file:bg-slate-700 file:px-3 file:py-2 file:text-slate-100 hover:file:bg-slate-600">
                    @error('cover_art')<p class="mt-1 text-sm text-red-300">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 font-semibold text-white transition hover:bg-indigo-500">Envoyer pour validation</button>
                </div>
            </form>
        @endif

        @if($distributionProjects->isNotEmpty())
            <div class="mt-5 space-y-3">
                @foreach($distributionProjects as $distributionProject)
                    <article class="flex flex-col gap-3 rounded-lg border border-slate-700 bg-slate-800/70 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            @if($distributionProject->cover_art_url)
                                <img src="{{ $distributionProject->cover_art_url }}" alt="Pochette de {{ $distributionProject->title }}" class="h-14 w-14 rounded-md object-cover">
                            @endif
                            <div class="min-w-0">
                                <h3 class="truncate font-medium text-white">{{ $distributionProject->title }}</h3>
                                <p class="text-sm text-slate-400">{{ ucfirst(str_replace('_', ' ', $distributionProject->current_stage->value)) }}</p>
                            </div>
                        </div>
                        @if($distributionProject->release_date)
                            <p class="text-sm font-medium text-emerald-300">Sortie officielle : {{ $distributionProject->release_date->format('d/m/Y') }}</p>
                        @else
                            <p class="text-sm text-slate-400">Date de sortie en attente de l’administration</p>
                        @endif
                        @if($distributionProject->distributions->where('status', 'published')->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach($distributionProject->distributions->where('status', 'published') as $distribution)
                                    @if($distribution->platform_url)
                                        <a href="{{ $distribution->platform_url }}" target="_blank" rel="noopener noreferrer" class="rounded-md border border-indigo-500/30 bg-indigo-500/10 px-2.5 py-1 text-xs font-medium text-indigo-200 hover:bg-indigo-500/20">{{ $distribution->platform_name }}</a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>

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