@extends('layouts.app')

@section('title', $projet->titre . ' - Aviatrax Studio')

@section('content')
<div class="space-y-6 text-white">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-blue-400">{{ $projet->titre }}</h1>
        @can('update', $projet)
            @if(!auth()->user()->isArtiste())
                <a href="{{ route('projets.edit', $projet) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Modifier
                </a>
            @endif
        @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2">
            <div class="bg-[#101828] shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-orange-400 mb-4">Informations du projet</h3>
                    
                    <div class="space-y-4">
                        @if($projet->description)
                            <div>
                                <label class="block text-sm font-medium text-blue-300">Description</label>
                                <p class="mt-1 text-sm text-white">{{ $projet->description }}</p>
                            </div>
                        @endif

                        @if($projet->etape === 'distribution_terminee')
                            <div class="bg-green-900 text-green-300 rounded p-3 font-semibold flex items-center">
                                <span class="mr-2">🎉</span> Votre musique est maintenant disponible sur toutes les plateformes de streaming musical dans le monde !
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-blue-300">Artiste</label>
                                <p class="mt-1 text-sm text-white">{{ $projet->artiste->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-blue-300">Responsable</label>
                                <p class="mt-1 text-sm text-white">{{ $projet->responsable->name }}</p>
                            </div>
                            @if($projet->date_debut)
                                <div>
                                    <label class="block text-sm font-medium text-blue-300">Date de début</label>
                                    <p class="mt-1 text-sm text-white">{{ $projet->date_debut->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            @if($projet->date_fin_prevue)
                                <div>
                                    <label class="block text-sm font-medium text-blue-300">Date de fin prévue</label>
                                    <p class="mt-1 text-sm text-white">{{ $projet->date_fin_prevue->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            @if($projet->release_date)
                                <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-3 sm:col-span-2">
                                    <label class="block text-sm font-medium text-emerald-300">Date de sortie officielle</label>
                                    <p class="mt-1 text-lg font-semibold text-white">{{ $projet->release_date->format('d/m/Y') }}</p>
                                </div>
                            @elseif(auth()->user()->isArtiste())
                                <div class="rounded-lg border border-slate-700 bg-slate-900 p-3 sm:col-span-2">
                                    <p class="text-sm text-slate-400">La date de sortie officielle sera communiquée par l’administration.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des étapes -->
            <div class="bg-[#101828] shadow rounded-lg mt-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-orange-400 mb-4">Historique des étapes</h3>
                    
                    @if($projet->etapes->count() > 0)
                        <div class="space-y-3">
                            @foreach($projet->etapes->sortByDesc('created_at') as $etape)
                                <div class="flex flex-col gap-3 p-3 bg-[#1a2233] rounded sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full text-white" style="background-color: {{ $projet->etape_color }}">
                                                {{ $etape->etape_label }}
                                            </span>
                                            <span class="text-sm text-blue-200">
                                                par {{ $etape->modifiePar->name }}
                                            </span>
                                        </div>
                                        @if($etape->commentaire)
                                            <p class="mt-1 text-sm text-white">{{ $etape->commentaire }}</p>
                                        @endif
                                    </div>
                                    <span class="shrink-0 text-xs text-blue-300">{{ $etape->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-blue-200">Aucun historique disponible</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statut et actions -->
        <div class="space-y-6">
            <!-- Statut actuel -->
            <div class="bg-[#101828] shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-orange-400 mb-4">Statut actuel</h3>
                    
                    <div class="text-center">
                        <span class="inline-block px-4 py-2 text-lg font-medium rounded-full text-black bg-orange-400" style="background-color: {{ $projet->etape_color }}">
                            {{ $projet->etape_label }}
                        </span>
                    </div>

                    @can('update', $projet)
                        @if(!auth()->user()->isArtiste())
                            @if(auth()->user()->isAdmin())
                                <form action="{{ route('admin.projects.release-date', $projet) }}" method="POST" class="mt-5 border-t border-slate-700 pt-4">
                                    @csrf
                                    @method('PUT')
                                    <label for="release-date" class="block text-sm font-medium text-blue-300">Date de sortie officielle</label>
                                    <div class="mt-2 flex gap-2">
                                        <input id="release-date" type="date" name="release_date" value="{{ $projet->release_date?->format('Y-m-d') }}" class="min-w-0 flex-1 rounded-md border border-slate-600 bg-slate-950 px-3 py-2 text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">Enregistrer</button>
                                    </div>
                                </form>
                            @endif
                            <div class="mt-4">
                                <form action="{{ route('projets.update-etape', $projet) }}" method="POST">
                                    @csrf
                                    <div class="space-y-3">
                                        <div>
                                            <label for="etape" class="block text-sm font-medium text-blue-300">Changer l'étape</label>
                                            <select name="etape" id="etape" required 
                                                    class="mt-1 block w-full rounded-md border border-slate-600 bg-slate-950 text-white shadow-sm focus:border-indigo-400 focus:ring-indigo-500">
                                                <option value="enregistrement" {{ $projet->etape == 'enregistrement' ? 'selected' : '' }}>Enregistrement</option>
                                                <option value="mixage" {{ $projet->etape == 'mixage' ? 'selected' : '' }}>Mixage</option>
                                                <option value="mastering" {{ $projet->etape == 'mastering' ? 'selected' : '' }}>Mastering</option>
                                                @if(auth()->user()->isAdmin())
                                                    <option value="distribution_en_cours" {{ $projet->etape == 'distribution_en_cours' ? 'selected' : '' }}>Distribution en cours</option>
                                                    <option value="distribution_terminee" {{ $projet->etape == 'distribution_terminee' ? 'selected' : '' }}>Distribution terminée</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div>
                                            <label for="commentaire" class="block text-sm font-medium text-blue-300">Commentaire (optionnel)</label>
                                            <textarea name="commentaire" id="commentaire" rows="2" 
                                                      class="mt-1 block w-full rounded-md border border-slate-600 bg-slate-950 text-white shadow-sm focus:border-indigo-400 focus:ring-indigo-500"></textarea>
                                        </div>
                                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                            Mettre à jour l'étape
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endcan

                    <!-- Section Upload de fichiers pour le mastering -->
                    @if($projet->etape === 'mastering' && auth()->user()->isResponsableStudio())
                        <div class="mt-6 overflow-hidden rounded-xl border border-orange-500/30 bg-[#151d2d]">
                            <div class="border-b border-slate-700 px-5 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-orange-300">Livraison finale</p>
                                <h4 class="mt-1 text-lg font-semibold text-white">Envoyer les masters à l’administration</h4>
                                <p class="mt-1 text-sm text-slate-400">Le responsable transmet les fichiers définitifs pour validation avant la diffusion.</p>
                            </div>

                            @if(!$projet->hasFichierAudio() && !$projet->hasFichierVideo())
                                <form action="{{ route('projets.upload-media', $projet) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-4 p-5">
                                    @csrf
                                    <label class="rounded-xl border border-dashed border-blue-500/40 bg-slate-950/50 p-4 transition hover:border-blue-400">
                                        <span class="block text-sm font-semibold text-blue-300">Master audio</span>
                                        <span class="mt-1 block text-xs text-slate-400">MP3, WAV, AAC, FLAC, OGG ou M4A — 500 MB maximum</span>
                                        <input type="file" name="fichier_audio" accept=".mp3,.wav,.aac,.flac,.ogg,.m4a" class="mt-3 block w-full text-sm text-slate-300 file:mr-3 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2.5 file:text-white">
                                        @error('fichier_audio')<span class="mt-2 block text-xs text-red-300">{{ $message }}</span>@enderror
                                    </label>
                                    <label class="rounded-xl border border-dashed border-violet-500/40 bg-slate-950/50 p-4 transition hover:border-violet-400">
                                        <span class="block text-sm font-semibold text-violet-300">Clip ou fichier vidéo</span>
                                        <span class="mt-1 block text-xs text-slate-400">MP4, AVI, MOV ou MKV — 1 GB maximum</span>
                                        <input type="file" name="fichier_video" accept=".mp4,.avi,.mov,.mkv" class="mt-3 block w-full text-sm text-slate-300 file:mr-3 file:rounded-md file:border-0 file:bg-violet-600 file:px-4 file:py-2.5 file:text-white">
                                        @error('fichier_video')<span class="mt-2 block text-xs text-red-300">{{ $message }}</span>@enderror
                                    </label>
                                    <button type="submit" class="rounded-lg bg-orange-400 px-5 py-3 font-semibold text-slate-950 hover:bg-orange-300">Envoyer pour validation</button>
                                </form>
                            @else
                                <div class="p-5">
                                    <div class="mb-4 flex items-center gap-3 rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-3">
                                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                                        <div>
                                            <p class="text-sm font-semibold text-emerald-300">Livraison enregistrée</p>
                                            <p class="text-xs text-slate-500">{{ $projet->fichiers_valides_admin ? 'Validée par l’administration' : 'En attente de validation administrative' }}</p>
                                        </div>
                                    </div>
                                    @if($projet->hasFichierAudio())
                                        <div class="mb-2 flex items-center justify-between gap-3 rounded-lg bg-slate-950/70 p-3">
                                            <div class="min-w-0"><p class="truncate text-sm text-blue-200">🎵 {{ $projet->fichier_audio_nom_original }}</p><p class="text-xs text-slate-500">{{ $projet->getAudioSizeFormatted() }}</p></div>
                                            <a href="{{ route('projets.download-audio', $projet) }}" class="shrink-0 rounded-md bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">Télécharger</a>
                                        </div>
                                    @endif
                                    @if($projet->hasFichierVideo())
                                        <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-950/70 p-3">
                                            <div class="min-w-0"><p class="truncate text-sm text-violet-200">🎬 {{ $projet->fichier_video_nom_original }}</p><p class="text-xs text-slate-500">{{ $projet->getVideoSizeFormatted() }}</p></div>
                                            @if(auth()->user()->isAdmin())
                                                <a href="{{ route('projets.download-video', $projet) }}" class="shrink-0 rounded-md bg-violet-600 px-3 py-2 text-xs font-semibold text-white hover:bg-violet-500">Télécharger</a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Section validation admin des fichiers -->
                    @if(auth()->user()->isAdmin() && ($projet->hasFichierAudio() || $projet->hasFichierVideo()))
                        <div class="mt-6 p-4 bg-[#1a2233] rounded-lg">
                            <h4 class="text-md font-medium text-orange-400 mb-3">🔍 Validation des fichiers</h4>
                            
                            @if(!$projet->fichiers_valides_admin)
                                <form action="{{ route('projets.validate-files', $projet) }}" method="POST" class="space-y-4">
                                    @csrf
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-blue-300 mb-2">Commentaire (optionnel)</label>
                                        <textarea name="commentaire" rows="3" 
                                                  class="w-full border-blue-900 bg-black text-white rounded-md focus:ring-blue-400 focus:border-orange-400"
                                                  placeholder="Vos observations sur les fichiers..."></textarea>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2 sm:flex-row">
                                        <button type="submit" name="valide" value="1" 
                                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                            ✅ Approuver
                                        </button>
                                        <button type="submit" name="valide" value="0" 
                                                class="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                            ❌ Rejeter
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="p-3 bg-green-900 border border-green-700 rounded">
                                    <p class="text-green-400 font-medium">✅ Fichiers validés par l'administrateur</p>
                                    @if($projet->commentaire_admin_fichiers)
                                        <p class="text-sm text-gray-300 mt-1">{{ $projet->commentaire_admin_fichiers }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <div class="mt-6 rounded-xl border border-indigo-500/30 bg-indigo-950/20 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Publication</p>
                            <h4 class="mt-1 font-semibold text-white">Liens des plateformes</h4>
                            <form action="{{ route('admin.projects.platform-links.update', $projet) }}" method="POST" class="mt-3 space-y-3">
                                @csrf
                                @method('PUT')
                                <div class="space-y-2">
                                    @foreach(['Spotify','Apple Music','Boomplay','Deezer','TikTok','YouTube Music','Audiomack'] as $platform)
                                        @php
                                            $existing = collect($projet->platform_links ?? [])->firstWhere('name', $platform);
                                        @endphp
                                        <label class="grid grid-cols-1 gap-1 text-xs text-slate-300 sm:grid-cols-[110px_1fr] sm:items-center">
                                            <span>{{ $platform }}</span>
                                            <input type="url" name="platform_links[{{ $loop->index }}][url]" value="{{ $existing['url'] ?? '' }}" placeholder="https://…" class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                                            <input type="hidden" name="platform_links[{{ $loop->index }}][name]" value="{{ $platform }}">
                                        </label>
                                    @endforeach
                                </div>
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <input type="date" name="release_date" value="{{ $projet->release_date?->format('Y-m-d') }}" class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                                    <select name="distribution_status" class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                                        <option value="pending" @selected($projet->distribution_status !== 'published')>Préparation</option>
                                        <option value="published" @selected($projet->distribution_status === 'published')>Publié en ligne</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white hover:bg-indigo-500">Publier les liens</button>
                            </form>
                        </div>

                        <div class="mt-6 space-y-2">
                            <form action="{{ route('projets.update', $projet) }}" method="POST" onsubmit="return confirm('Valider ce projet ?');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="statut" value="valide">
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Valider le projet</button>
                            </form>
                            <form action="{{ route('projets.update', $projet) }}" method="POST" onsubmit="return confirm('Terminer ce projet ?');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="statut" value="termine">
                                <button type="submit" class="w-full bg-green-700 text-white px-4 py-2 rounded-md hover:bg-green-800">Marquer comme terminé</button>
                            </form>
                            <form action="{{ route('projets.destroy', $projet) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce projet ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Supprimer le projet</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progression -->
            <div class="bg-[#101828] shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-orange-400 mb-4">Progression</h3>
                    
                    <div class="space-y-3">
                        @php
                            $etapes = ['enregistrement', 'mixage', 'mastering', 'distribution_en_cours', 'distribution_terminee'];
                            $etapeActuelle = array_search($projet->etape, $etapes);
                            $pourcentage = (($etapeActuelle + 1) / count($etapes)) * 100;
                        @endphp
                        
                        <div class="w-full bg-blue-900 rounded-full h-4">
                            <div class="bg-orange-400 h-4 rounded-full transition-all duration-300" style="width: {{ $pourcentage }}%"></div>
                        </div>
                        <div class="text-sm text-blue-200 text-center">
                            {{ number_format($pourcentage, 1) }}% terminé
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isArtiste() && $projet->artiste_id === auth()->id())
        <div id="pochette" class="mt-6 p-5 rounded-xl border border-indigo-500/30 bg-indigo-950/20">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Identité visuelle</p>
                    <h4 class="mt-1 text-lg font-semibold text-white">Pochette du morceau</h4>
                    <p class="mt-1 text-sm text-slate-400">Format obligatoire : carré <strong class="text-slate-200">1600 × 1600 px</strong>. JPG, PNG ou WebP — 20 MB maximum.</p>
                    <div class="mt-3 grid gap-2 text-xs text-slate-300 sm:grid-cols-2">
                        <p class="rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-3 py-2"><strong class="text-emerald-300">À inclure :</strong> uniquement le titre du chant ou de l’album et le nom de l’artiste.</p>
                        <p class="rounded-lg border border-red-500/20 bg-red-500/10 px-3 py-2"><strong class="text-red-300">À éviter :</strong> aucune photo, aucun visage, aucun logo externe, aucune URL ou promotion supplémentaire.</p>
                    </div>
                </div>
                @if($projet->cover_art_url)
                    <img src="{{ $projet->cover_art_url }}" alt="Pochette de {{ $projet->titre }}" class="h-28 w-28 rounded-xl object-cover ring-1 ring-slate-700">
                @endif
            </div>
            <form action="{{ route('projets.cover-art.upload', $projet) }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-col gap-3 sm:flex-row">
                @csrf
                <input type="file" name="cover_art" accept="image/jpeg,image/png,image/webp" required class="block min-w-0 flex-1 text-sm text-slate-300 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-600 file:px-4 file:py-2.5 file:text-white">
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 font-semibold text-white hover:bg-indigo-500">{{ $projet->cover_art_url ? 'Remplacer la pochette' : 'Envoyer la pochette' }}</button>
            </form>
        </div>

        @if($projet->fichiers_valides_admin)
            <div class="mt-6 rounded-xl border border-emerald-500/30 bg-emerald-950/20 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-300">Livraison validée</p>
                <h3 class="mt-1 text-lg font-semibold text-white">Fichiers finaux</h3>
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @if($projet->hasFichierAudio())
                        <a href="{{ route('projets.download-audio', $projet) }}" class="flex items-center justify-between rounded-lg border border-slate-700 bg-slate-950/70 p-4 hover:border-blue-400">
                            <span><span class="block text-sm font-semibold text-blue-200">Master audio</span><span class="text-xs text-slate-400">{{ $projet->getAudioSizeFormatted() }}</span></span>
                            <span class="text-sm font-semibold text-blue-300">Télécharger</span>
                        </a>
                    @endif
                    @if($projet->hasFichierVideo())
                        <a href="{{ route('projets.download-video', $projet) }}" class="flex items-center justify-between rounded-lg border border-slate-700 bg-slate-950/70 p-4 hover:border-violet-400">
                            <span><span class="block text-sm font-semibold text-violet-200">Fichier vidéo</span><span class="text-xs text-slate-400">{{ $projet->getVideoSizeFormatted() }}</span></span>
                            <span class="text-sm font-semibold text-violet-300">Télécharger</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @if($projet->distribution_status === 'published' && is_array($projet->platform_links) && count($projet->platform_links))
            <div class="mt-6 rounded-xl border border-indigo-500/30 bg-indigo-950/20 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Maintenant disponible</p>
                <h3 class="mt-1 text-lg font-semibold text-white">Votre musique en ligne</h3>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($projet->platform_links as $link)
                        @if(!empty($link['url']))
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-indigo-500/30 bg-indigo-500/10 px-4 py-2 text-sm font-semibold text-indigo-200 hover:bg-indigo-500/20">{{ $link['name'] }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <section class="mt-6 overflow-hidden rounded-xl border border-slate-700 bg-slate-900/90">
        <div class="border-b border-slate-800 px-5 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Accompagnement personnalisé</p>
            <h3 class="mt-1 text-lg font-semibold text-white">Messages du projet — {{ $projet->titre }}</h3>
            <p class="mt-1 text-sm text-slate-400">Cette conversation appartient uniquement à ce projet.</p>
        </div>
        <div class="max-h-[420px] space-y-3 overflow-y-auto px-5 py-4">
            @forelse($projet->messages()->with('sender')->get() as $message)
                @php $mine = $message->sender_id === auth()->id(); @endphp
                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[86%] rounded-2xl border px-4 py-3 {{ $mine ? 'border-indigo-500/30 bg-indigo-500/10' : 'border-slate-700 bg-slate-950/80' }}">
                        <p class="text-xs font-semibold {{ $mine ? 'text-indigo-300' : 'text-slate-300' }}">{{ $mine ? 'Vous' : ($message->sender->name ?? 'Aviatrax') }}</p>
                        <p class="mt-1 whitespace-pre-wrap text-sm leading-6 text-slate-100">{{ $message->message }}</p>
                        <p class="mt-2 text-[10px] text-slate-500">{{ $message->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-slate-700 py-8 text-center text-sm text-slate-500">Aucun message pour ce projet pour le moment.</div>
            @endforelse
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isResponsableStudio() || (auth()->user()->isArtiste() && $projet->artiste_id === auth()->id()))
            <form action="{{ route('projets.messages.store', $projet) }}" method="POST" class="border-t border-slate-800 px-5 py-4">
                @csrf
                <label for="project-message" class="mb-2 block text-sm font-medium text-slate-200">{{ auth()->user()->isArtiste() ? 'Écrire au responsable ou à l’administration' : 'Informer l’artiste sur ce projet' }}</label>
                <textarea id="project-message" name="message" rows="3" maxlength="3000" required placeholder="Instructions, corrections, prochaines étapes…" class="w-full resize-y rounded-lg border border-slate-700 bg-slate-950 px-3 py-3 text-sm text-white placeholder:text-slate-500 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20"></textarea>
                <div class="mt-3 flex justify-end"><button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">Envoyer le message</button></div>
            </form>
        @endif
    </section>
</div>
@endsection