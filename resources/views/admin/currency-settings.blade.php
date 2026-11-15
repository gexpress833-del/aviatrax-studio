@extends('layouts.app')

@section('title', 'Taux de change - Aviatrax Studio')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-orange-300">Administration financière</p>
        <h1 class="mt-1 text-3xl font-bold text-white">Devises et taux de change</h1>
        <p class="mt-2 text-slate-300">Le taux USD/CDF défini ici sera appliqué aux nouvelles demandes de retrait.</p>
    </div>

    <form method="POST" action="{{ route('admin.currency.update') }}" class="space-y-5 rounded-xl border border-slate-700 bg-slate-900 p-5 sm:p-7">
        @csrf
        @method('PUT')

        <div>
            <label for="points_per_usd" class="mb-2 block text-sm font-medium text-slate-200">Points correspondant à 1 USD</label>
            <input id="points_per_usd" name="points_per_usd" type="number" min="1" max="1000000" step="1" value="{{ old('points_per_usd', $pointsPerDollar) }}" required class="w-full rounded-lg border border-slate-600 bg-slate-950 px-4 py-3 text-white focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-400/30">
            <p class="mt-1 text-xs text-slate-400">Exemple : 100 points = 1 USD.</p>
        </div>

        <div>
            <label for="cdf_per_usd" class="mb-2 block text-sm font-medium text-slate-200">Francs congolais pour 1 USD</label>
            <input id="cdf_per_usd" name="cdf_per_usd" type="number" min="0.0001" max="1000000000" step="0.01" value="{{ old('cdf_per_usd', $cdfPerDollar > 0 ? $cdfPerDollar : '') }}" required placeholder="Saisir le taux USD/CDF" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-4 py-3 text-white placeholder:text-slate-500 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/30">
            <p class="mt-1 text-xs text-slate-400">Saisissez le taux officiel que vous souhaitez appliquer, par exemple le nombre de CDF équivalant à 1 USD.</p>
            @if($cdfPerDollar <= 0)
                <p class="mt-2 text-sm text-amber-300">Le taux USD/CDF n’est pas encore configuré. Les retraits en CDF resteront désactivés jusque-là.</p>
            @else
                <p class="mt-2 text-sm text-emerald-300">Taux actif : 1 USD = {{ number_format($cdfPerDollar, 2, ',', ' ') }} CDF.</p>
            @endif
        </div>

        <div>
            <label for="minimum_withdrawal_points" class="mb-2 block text-sm font-medium text-slate-200">Seuil minimum de retrait (points)</label>
            <input id="minimum_withdrawal_points" name="minimum_withdrawal_points" type="number" min="1" max="1000000000" step="1" value="{{ old('minimum_withdrawal_points', $withdrawalThreshold) }}" required class="w-full rounded-lg border border-slate-600 bg-slate-950 px-4 py-3 text-white focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-400/30">
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-700 pt-5">
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-slate-300 hover:text-white">Retour au tableau de bord</a>
            <button type="submit" class="rounded-lg bg-orange-500 px-5 py-3 font-semibold text-slate-950 transition hover:bg-orange-400">Enregistrer les paramètres</button>
        </div>
    </form>
</div>
@endsection
