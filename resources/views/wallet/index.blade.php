@extends('layouts.app')

@section('title', 'Portefeuille artiste - Aviatrax Studio')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-orange-300">Espace artiste</p>
            <h1 class="mt-1 text-3xl font-bold text-white">Portefeuille et retraits</h1>
            <p class="mt-2 text-slate-300">Consultez votre solde et suivez vos demandes de paiement.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-blue-300 hover:text-blue-200">Retour à mon espace artiste</a>
    </div>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3" aria-label="Résumé du portefeuille">
        <div class="rounded-xl border border-emerald-500/30 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Solde disponible</p>
            <p class="mt-2 text-3xl font-bold text-emerald-300">{{ number_format($wallet->points_balance, 0, ',', ' ') }} <span class="text-base font-medium">points</span></p>
        </div>
        <div class="rounded-xl border border-blue-500/30 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Seuil minimum de retrait</p>
            <p class="mt-2 text-3xl font-bold text-blue-300">{{ number_format($withdrawalThreshold, 0, ',', ' ') }} <span class="text-base font-medium">points</span></p>
        </div>
        <div class="rounded-xl border border-orange-500/30 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Valeur indicative en USD</p>
            <p class="mt-2 text-3xl font-bold text-orange-300">{{ number_format($wallet->points_balance / $pointsPerDollar, 2, ',', ' ') }} <span class="text-base font-medium">USD</span></p>
            @if($cdfPerDollar > 0)
                <p class="mt-1 text-xs text-slate-400">Soit environ {{ number_format(($wallet->points_balance / $pointsPerDollar) * $cdfPerDollar, 0, ',', ' ') }} CDF</p>
            @endif
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        <div class="rounded-xl border border-slate-700 bg-slate-900 p-5 sm:p-6">
            <div class="mb-4 overflow-hidden rounded-xl border border-slate-700 bg-slate-950/70">
                <img src="{{ asset('payment-methods-banner.png') }}" alt="Méthodes de paiement" class="h-20 w-full object-cover object-center">
            </div>

            <h2 class="text-xl font-semibold text-white">Demander un retrait</h2>
            <p class="mt-1 text-sm text-slate-400">Les demandes sont soumises à validation par l’équipe.</p>

            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-lg border border-slate-700 bg-slate-950/60 p-3">
                    <p class="text-xs uppercase tracking-wide text-blue-300">Virement</p>
                    <p class="mt-2 text-sm font-semibold text-white">Banque / IBAN</p>
                    <p class="mt-1 text-xs text-slate-400">Pour les paiements sécurisés via banque.</p>
                </div>
                <div class="rounded-lg border border-slate-700 bg-slate-950/60 p-3">
                    <p class="text-xs uppercase tracking-wide text-emerald-300">Mobile Money</p>
                    <p class="mt-2 text-sm font-semibold text-white">Orange Money / Airtel</p>
                    <p class="mt-1 text-xs text-slate-400">Paiements rapides sur téléphone mobile.</p>
                </div>
                <div class="rounded-lg border border-slate-700 bg-slate-950/60 p-3">
                    <p class="text-xs uppercase tracking-wide text-orange-300">Crypto</p>
                    <p class="mt-2 text-sm font-semibold text-white">USDT / USDC</p>
                    <p class="mt-1 text-xs text-slate-400">Transfert numérique à haute sécurité.</p>
                </div>
            </div>

            @if($wallet->points_balance < $withdrawalThreshold)
                <div class="mt-5 rounded-lg border border-amber-500/30 bg-amber-500/10 p-4 text-sm text-amber-200">
                    Le retrait sera disponible dès que votre solde atteindra {{ number_format($withdrawalThreshold, 0, ',', ' ') }} points.
                </div>
            @else
                <form id="withdrawal-form" data-points-per-usd="{{ $pointsPerDollar }}" data-cdf-per-usd="{{ $cdfPerDollar }}" action="{{ route('wallet.withdraw') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="points_amount" class="mb-2 block text-sm font-medium text-slate-200">Montant en points</label>
                        <input type="number" id="points_amount" name="points_amount" min="{{ $withdrawalThreshold }}" max="{{ $wallet->points_balance }}" step="1" value="{{ $withdrawalThreshold }}" required class="w-full rounded-lg border border-slate-600 bg-slate-950 px-4 py-3 text-white focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/30">
                        <p class="mt-1 text-xs text-slate-400">Maximum disponible : {{ number_format($wallet->points_balance, 0, ',', ' ') }} points.</p>
                    </div>
                    <div>
                        <label for="currency" class="mb-2 block text-sm font-medium text-slate-200">Devise du retrait</label>
                        <select id="currency" name="currency" required class="w-full rounded-lg border border-slate-600 bg-slate-950 px-4 py-3 text-white focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/30">
                            <option value="USD" @selected(old('currency', 'USD') === 'USD')>Dollar américain (USD)</option>
                            <option value="CDF" @selected(old('currency') === 'CDF') @disabled($cdfPerDollar <= 0)>Franc congolais (CDF){{ $cdfPerDollar <= 0 ? ' - indisponible actuellement' : '' }}</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Montant estimé : <output id="cash-estimate">{{ number_format($withdrawalThreshold / $pointsPerDollar, 2, ',', ' ') }} USD</output></p>
                    </div>
                    <div>
                        <label for="payment_method" class="mb-2 block text-sm font-medium text-slate-200">Mode de paiement</label>
                        <select id="payment_method" name="payment_method" required class="w-full rounded-lg border border-slate-600 bg-slate-950 px-4 py-3 text-white focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/30">
                            <option value="bank_transfer">Virement bancaire</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="crypto">Crypto</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Choisissez le canal qui correspond au mode de réception le plus pratique pour votre compte.</p>
                    </div>
                    <div>
                        <label for="payment_account" class="mb-2 block text-sm font-medium text-slate-200">Coordonnées de paiement</label>
                        <input type="text" id="payment_account" name="payment_account" value="{{ old('payment_account') }}" placeholder="IBAN, numéro Mobile Money, adresse crypto ou compte bénéficiaire" required class="w-full rounded-lg border border-slate-600 bg-slate-950 px-4 py-3 text-white placeholder:text-slate-500 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/30">
                    </div>
                    <button type="submit" class="rounded-lg bg-orange-500 px-5 py-3 font-semibold text-slate-950 transition hover:bg-orange-400">Envoyer la demande</button>
                </form>
            @endif
        </div>

        <div class="rounded-xl border border-slate-700 bg-slate-900 p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-white">Historique des retraits</h2>
                <span class="text-sm text-slate-400">{{ $withdrawals->count() }} demande(s)</span>
            </div>
            @if($withdrawals->isNotEmpty())
                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-[420px] w-full text-left text-sm">
                        <thead class="border-b border-slate-700 text-xs uppercase text-slate-400">
                            <tr>
                                <th class="pb-3 pr-3 font-medium">Date</th>
                                <th class="pb-3 pr-3 font-medium">Montant</th>
                                <th class="pb-3 font-medium">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($withdrawals as $withdrawal)
                                @php($status = $withdrawal->status->value)
                                <tr>
                                    <td class="py-3 pr-3 text-slate-300">{{ $withdrawal->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3 pr-3 text-white">{{ number_format($withdrawal->cash_amount, $withdrawal->currency === 'CDF' ? 0 : 2, ',', ' ') }} {{ $withdrawal->currency }}<span class="block text-xs text-slate-400">{{ number_format($withdrawal->points_amount, 0, ',', ' ') }} points</span></td>
                                    <td class="py-3">
                                        <span class="font-medium {{ $status === 'paye' ? 'text-emerald-300' : ($status === 'rejete' ? 'text-red-300' : 'text-amber-300') }}">{{ $withdrawal->status->label() }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mt-5 rounded-lg border border-slate-700 bg-slate-950/60 p-4 text-sm text-slate-400">Aucune demande de retrait pour le moment.</p>
            @endif
        </div>
    </section>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('withdrawal-form');
        if (!form) return;

        const pointsInput = document.getElementById('points_amount');
        const currencySelect = document.getElementById('currency');
        const estimate = document.getElementById('cash-estimate');
        const pointsPerUsd = Number(form.dataset.pointsPerUsd);
        const cdfPerUsd = Number(form.dataset.cdfPerUsd);

        function updateEstimate() {
            const usdAmount = Number(pointsInput.value || 0) / pointsPerUsd;
            if (currencySelect.value === 'CDF') {
                estimate.textContent = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(usdAmount * cdfPerUsd) + ' CDF';
                return;
            }
            estimate.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(usdAmount) + ' USD';
        }

        pointsInput.addEventListener('input', updateEstimate);
        currencySelect.addEventListener('change', updateEstimate);
    });
</script>
@endsection
