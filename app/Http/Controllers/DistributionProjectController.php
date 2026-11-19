<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStage;
use App\Models\Project;
use App\Services\CloudinaryMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class DistributionProjectController extends Controller
{
    public function store(Request $request, CloudinaryMediaService $mediaService)
    {
        $artist = $request->user();
        abort_unless($artist && $artist->isArtiste(), 403);

        if (! $artist->hasActiveSubscription()) {
            return redirect()->route('subscriptions.index')->withErrors([
                'subscription' => 'Un abonnement de distribution actif est nécessaire pour envoyer un projet.',
            ]);
        }

        if (! \App\Models\User::isCloudinaryConfigured()) {
            return redirect()->route('subscriptions.index')->withErrors([
                'storage' => 'Le stockage sécurisé est temporairement indisponible. Réessayez plus tard.',
            ]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'master' => 'required|file|mimes:mp3,wav,aac,flac,m4a|max:512000',
            'cover_art' => 'required|image|mimes:jpg,jpeg,png,webp|max:20480',
        ]);

        $project = Project::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::lower(Str::random(8)),
            'artist_id' => $artist->id,
            'project_type' => 'distribution_only',
            'current_stage' => ProjectStage::DISTRIBUTION_DIRECTE,
            'start_date' => now()->toDateString(),
            'description' => $validated['description'] ?? null,
        ]);

        try {
            $master = $mediaService->upload($request->file('master'), $project->id, 'distribution_directe', 'master');
            $cover = $mediaService->upload($request->file('cover_art'), $project->id, 'distribution_directe', 'cover');

            DB::transaction(function () use ($project, $master, $cover, $request) {
                $project->update(['cover_art_url' => $cover['file_url']]);

                $project->files()->create([
                    'uploaded_by' => $request->user()->id,
                    'stage' => ProjectStage::DISTRIBUTION_DIRECTE,
                    'file_name' => $request->file('master')->getClientOriginalName(),
                    ...$master,
                ]);

                $project->files()->create([
                    'uploaded_by' => $request->user()->id,
                    'stage' => ProjectStage::DISTRIBUTION_DIRECTE,
                    'file_name' => $request->file('cover_art')->getClientOriginalName(),
                    ...$cover,
                ]);
            });
        } catch (Throwable $exception) {
            $project->delete();

            Log::error('Échec de dépôt d’un projet de distribution directe.', [
                'project_id' => $project->id,
                'artist_id' => $artist->id,
                'exception' => $exception::class,
            ]);

            return redirect()->route('subscriptions.index')->withErrors([
                'upload' => 'Le dépôt n’a pas abouti. Vérifiez vos fichiers puis réessayez.',
            ])->withInput();
        }

        Log::info('Projet de distribution directe soumis.', [
            'project_id' => $project->id,
            'artist_id' => $artist->id,
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'Votre projet a été envoyé à l’équipe pour validation.');
    }
}
