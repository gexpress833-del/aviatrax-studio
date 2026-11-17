@extends('layouts.app')

@section('title', 'Compte en attente')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-slate-950">
    <div class="w-full max-w-xl rounded-3xl border border-blue-400/40 bg-slate-900/90 p-8 shadow-2xl shadow-slate-950/60">
        <a href="{{ url('/') }}" class="mb-8 inline-flex items-center gap-3 text-lg font-bold text-blue-300 transition-colors hover:text-orange-300" aria-label="Aviatrax Studio, accueil">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 text-white">A</span>
            Aviatrax Studio
        </a>

        <div class="mb-6 flex justify-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-full border border-amber-400/50 bg-amber-500/10 text-amber-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="text-center">
            <p class="text-sm uppercase tracking-[0.2em] text-blue-300/80">Vérification du compte</p>
            <h1 class="mt-4 text-3xl font-black text-white sm:text-4xl">Compte en attente</h1>
            <p class="mt-5 text-base text-slate-300">
                Bonjour <span class="font-semibold text-blue-300">{{ $user->name }}</span>, votre inscription a bien été reçue.
            </p>
            <p class="mt-3 text-base text-slate-300">
                Votre compte artiste est en cours de validation par l’administration. Vous recevrez un accès dès qu’il sera activé.
            </p>
        </div>

        <div class="mt-8 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-sm text-amber-100">
            <p class="font-medium">Informations utiles</p>
            <ul class="mt-2 list-disc space-y-2 pl-5 text-amber-50/90">
                <li>Vous pouvez patienter sans refaire l’inscription.</li>
                <li>Une fois validé, vous serez automatiquement redirigé vers votre tableau de bord.</li>
                <li>Vous pouvez fermer cette page et revenir plus tard.</li>
            </ul>
        </div>

        <div class="mt-8 flex justify-center">
            <a href="{{ url('/') }}" class="rounded-xl border border-orange-400/50 bg-orange-500/10 px-5 py-3 text-sm font-semibold text-orange-200 transition hover:bg-orange-500/20">
                Retour à la page d’accueil
            </a>
        </div>
    </div>
</div>
@endsection
