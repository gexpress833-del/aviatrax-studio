@extends('layouts.app')

@section('title', 'Abonnement activé - Aviatrax Studio')

@section('content')
<div class="mx-auto max-w-5xl">
    <section class="relative overflow-hidden rounded-3xl border border-emerald-400/25 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950/30 px-5 py-10 shadow-2xl shadow-emerald-950/20 sm:px-8 sm:py-14">
        <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 left-1/4 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>

        <div class="relative mx-auto max-w-3xl text-center">
            <img src="{{ asset('logo_aviatrax-studio.jfif') }}" alt="Aviatrax Studio" class="mx-auto mb-6 h-16 w-16 rounded-2xl bg-slate-100 object-contain p-2 shadow-lg">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border border-emerald-300/30 bg-emerald-400/10 shadow-[0_0_40px_rgba(52,211,153,.18)] sm:h-24 sm:w-24">
                <svg class="h-10 w-10 text-emerald-300 sm:h-12 sm:w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12.5l4.2 4.2L19 7"/>
                </svg>
            </div>

            <p class="mt-6 text-xs font-bold uppercase tracking-[0.22em] text-emerald-300">Paiement confirmé</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-5xl">Votre abonnement est actif</h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">
                Votre accès à la distribution est disponible immédiatement. Vous pouvez maintenant reprendre vos activités sur Aviatrax Studio.
            </p>

            <div class="mx-auto mt-7 grid max-w-2xl gap-3 rounded-2xl border border-slate-700/80 bg-slate-950/60 p-4 text-left sm:grid-cols-3 sm:p-5">
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Statut</p>
                    <p class="mt-2 flex items-center gap-2 font-bold text-emerald-300"><span class="h-2 w-2 rounded-full bg-emerald-400"></span>Actif</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Valide jusqu’au</p>
                    <p class="mt-2 font-bold text-white">{{ $subscription->ends_at?->format('d/m/Y') }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Montant</p>
                    <p class="mt-2 font-bold text-white">{{ number_format((float) $subscription->amount, 2) }} {{ $subscription->currency === 'USD' ? '$' : $subscription->currency }} <span class="text-xs font-normal text-slate-400">/ {{ (int) config('aviatrax.subscription_duration_days', 30) }} jours</span></p>
                </div>
            </div>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-400 px-5 py-3.5 text-sm font-bold text-slate-950 shadow-lg shadow-emerald-950/40 transition hover:bg-emerald-300">
                    Accéder au tableau de bord
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-600 bg-slate-900/70 px-5 py-3.5 text-sm font-semibold text-white transition hover:border-indigo-400 hover:bg-slate-800">
                    Gérer mes sorties
                </a>
                <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center justify-center rounded-xl px-5 py-3.5 text-sm font-semibold text-slate-300 transition hover:text-white">
                    Voir mon abonnement
                </a>
            </div>
        </div>
    </section>

    <section class="mt-5 grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <p class="text-sm font-semibold text-white">Soumettre une sortie</p>
            <p class="mt-2 text-sm leading-6 text-slate-400">Envoyez vos masters et pochettes pour examen.</p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <p class="text-sm font-semibold text-white">Suivre la distribution</p>
            <p class="mt-2 text-sm leading-6 text-slate-400">Consultez les étapes et les plateformes disponibles.</p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <p class="text-sm font-semibold text-white">Continuer votre activité</p>
            <p class="mt-2 text-sm leading-6 text-slate-400">Retrouvez vos projets, notifications et portefeuille.</p>
        </div>
    </section>
</div>
@endsection
