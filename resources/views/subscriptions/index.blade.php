@extends('layouts.app')

@section('title', 'Abonnement de distribution - Aviatrax Studio')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">
    <header class="relative overflow-hidden rounded-3xl border border-indigo-400/20 bg-gradient-to-br from-indigo-950/70 via-slate-950 to-slate-950 px-5 py-8 sm:px-8 sm:py-10">
        <div class="pointer-events-none absolute -right-20 -top-24 h-72 w-72 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div class="relative max-w-3xl">
            <img src="{{ asset('logo-aviatrax-crop.png') }}" alt="Aviatrax Studio" class="mb-5 h-16 w-14 rounded-xl object-contain shadow-lg">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-indigo-300">Aviatrax distribution</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-5xl">Lancez votre musique dans le monde</h1>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">Activez votre accès mensuel à la plateforme, suivez votre distribution et accompagnez chaque sortie jusqu’à sa mise en ligne.</p>
        </div>
    </header>

    @if($subscription)
        <section class="relative overflow-hidden rounded-3xl border border-emerald-400/25 bg-gradient-to-br from-emerald-950/40 via-slate-950 to-slate-950 p-5 sm:p-8">
            <div class="pointer-events-none absolute -right-16 -top-20 h-52 w-52 rounded-full bg-emerald-400/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-emerald-300/30 bg-emerald-400/10 text-emerald-300">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12.5l4.2 4.2L19 7"/></svg>
                    </span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-300">Abonnement actif</p>
                        <h2 class="mt-1 text-2xl font-bold text-white">Votre accès est disponible</h2>
                        <p class="mt-2 text-sm text-slate-300">Valide jusqu’au <strong class="text-white">{{ $subscription->ends_at->format('d/m/Y') }}</strong></p>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-400 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-emerald-300">Accéder à mes sorties</a>
            </div>
        </section>
    @else
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(300px,.65fr)]">
            <div class="overflow-hidden rounded-3xl border border-slate-700/80 bg-slate-900/90 shadow-2xl shadow-slate-950/25">
                <div class="border-b border-slate-800 px-5 py-5 sm:px-7">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-300">Activation mensuelle</p>
                    <h2 class="mt-2 text-2xl font-bold text-white">Finaliser mon abonnement</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">Choisissez votre moyen de paiement sécurisé. Votre accès sera activé dès confirmation du paiement.</p>
                </div>

                <div class="p-5 sm:p-7">
                @if($errors->any())
                    <div class="mb-5 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200" role="alert">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                @if($pendingSubscription = $subscriptions->firstWhere('status', 'pending'))
                    @if($pendingSubscription->order_number)
                        <div class="rounded-2xl border border-amber-400/25 bg-amber-400/[0.07] p-5">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 h-2.5 w-2.5 shrink-0 rounded-full bg-amber-300 shadow-[0_0_14px_rgba(252,211,77,.5)]"></span>
                                <div>
                                    <h3 class="font-semibold text-amber-100">Paiement en cours de confirmation</h3>
                                    <p class="mt-2 text-sm leading-6 text-amber-100/70">Autorisez la transaction sur votre téléphone ou votre espace bancaire, puis vérifiez son statut.</p>
                                </div>
                            </div>
                            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                                <form action="{{ route('subscriptions.verify', $pendingSubscription) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl bg-amber-300 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-amber-200 sm:w-auto">Vérifier le paiement</button>
                                </form>
                                <form action="{{ route('subscriptions.cancel', $pendingSubscription) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl border border-slate-600 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-slate-800 sm:w-auto">Annuler et recommencer</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <p class="rounded-xl border border-amber-400/25 bg-amber-400/[0.07] p-4 text-sm text-amber-100">La demande précédente n’a pas reçu de référence. Vous pouvez soumettre une nouvelle demande.</p>
                    @endif
                @else
                    <form action="{{ route('subscriptions.checkout') }}" method="POST" class="space-y-6">
                        @csrf

                        <fieldset>
                            <legend class="mb-3 text-sm font-semibold text-white">Choisissez votre moyen de paiement</legend>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="group flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-700 bg-slate-950/70 p-4 transition has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/10 has-[:checked]:shadow-[0_0_24px_rgba(99,102,241,.09)]">
                                    <input type="radio" name="payment_type" value="1" @checked(old('payment_type', '1') === '1') required class="accent-indigo-400">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-700 bg-slate-900 text-emerald-300">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>
                                    </span>
                                    <span><strong class="block text-sm text-slate-100">Mobile Money</strong><small class="mt-1 block text-xs text-slate-500">Paiement depuis votre téléphone</small></span>
                                </label>
                                <label class="group flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-700 bg-slate-950/70 p-4 transition has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/10 has-[:checked]:shadow-[0_0_24px_rgba(99,102,241,.09)]">
                                    <input type="radio" name="payment_type" value="2" @checked(old('payment_type') === '2') required class="accent-indigo-400">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-700 bg-slate-900 text-indigo-300">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                                    </span>
                                    <span><strong class="block text-sm text-slate-100">Carte bancaire</strong><small class="mt-1 block text-xs text-slate-500">Visa crédit ou débit</small></span>
                                </label>
                            </div>
                        </fieldset>

                        <div id="phone-field">
                            <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-200">Numéro de paiement avec indicatif pays</label>
                            <input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" value="{{ old('phone', auth()->user()->telephone) }}" placeholder="+243…" required class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-2.5 text-white placeholder:text-slate-500 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">
                            @error('phone')<p class="mt-1 text-sm text-red-300">{{ $message }}</p>@enderror
                        </div>

                        <div id="card-fields" class="hidden space-y-4 rounded-xl border border-slate-600 bg-slate-950/70 p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-white">Ajouter une carte de crédit ou de débit</p>
                                    <p class="mt-1 text-xs text-slate-400">Tous les champs sont obligatoires</p>
                                </div>
                                <span class="rounded-md border border-slate-600 px-2 py-1 text-xs font-bold text-slate-200">VISA</span>
                            </div>

                            <div>
                                <label for="card_number" class="mb-1.5 block text-sm font-medium text-slate-200">Numéro de carte</label>
                                <input id="card_number" name="card_number" type="text" inputmode="numeric" autocomplete="cc-number" maxlength="19" placeholder="4242 4242 4242 4242" value="{{ old('card_number') }}" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-3 text-white placeholder:text-slate-500 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20" aria-describedby="card-number-status">
                                <p id="card-number-status" class="mt-1 hidden text-xs"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="card_expiry" class="mb-1.5 block text-sm font-medium text-slate-200">Date d’expiration</label>
                                    <input id="card_expiry" name="card_expiry" type="text" inputmode="numeric" autocomplete="cc-exp" maxlength="5" placeholder="MM/AA" value="{{ old('card_expiry') }}" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-3 text-white placeholder:text-slate-500 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">
                                </div>
                                <div>
                                    <label for="card_cvv" class="mb-1.5 block text-sm font-medium text-slate-200">CVV</label>
                                    <input id="card_cvv" name="card_cvv" type="password" inputmode="numeric" autocomplete="cc-csc" maxlength="4" placeholder="123" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-3 text-white placeholder:text-slate-500 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">
                                </div>
                            </div>

                            <div>
                                <label for="card_name" class="mb-1.5 block text-sm font-medium text-slate-200">Nom et prénom du titulaire</label>
                                <input id="card_name" name="card_name" type="text" autocomplete="cc-name" maxlength="100" placeholder="Nom complet" value="{{ old('card_name') }}" class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3 py-3 text-white placeholder:text-slate-500 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">
                            </div>
                        </div>
                        <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1 border-t border-slate-700 pt-4">
                            <span class="text-3xl font-bold text-white">{{ number_format((float) config('aviatrax.subscription_amount', 12), 0) }} {{ config('aviatrax.subscription_currency', 'USD') === 'USD' ? '$' : config('aviatrax.subscription_currency') }}</span>
                            <span class="text-sm text-slate-400">/ {{ (int) config('aviatrax.subscription_duration_days', 30) }} jours de distribution</span>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white shadow-lg shadow-indigo-950/30 transition hover:bg-indigo-500">Payer de manière sécurisée</button>
                        <p class="text-center text-xs text-slate-500">Paiement sécurisé par Mobile Money ou carte. La production en studio est distincte.</p>
                    </form>
                @endif
            </div>

            <aside class="space-y-4">
                <div class="rounded-3xl border border-indigo-400/20 bg-gradient-to-br from-indigo-950/50 via-slate-900 to-slate-950 p-5 sm:p-6">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-300">Pass distribution</p>
                    <div class="mt-4 flex items-end gap-2"><span class="text-5xl font-black tracking-tight text-white">{{ number_format((float) config('aviatrax.subscription_amount', 12), 0) }} {{ config('aviatrax.subscription_currency', 'USD') === 'USD' ? '$' : config('aviatrax.subscription_currency') }}</span><span class="pb-1 text-sm text-slate-400">/ {{ (int) config('aviatrax.subscription_duration_days', 30) }} jours</span></div>
                    <p class="mt-3 text-sm leading-6 text-slate-400">Un accès mensuel conçu pour préparer, soumettre et suivre vos sorties.</p>
                    <div class="my-5 h-px bg-slate-800"></div>
                    <ul class="space-y-4 text-sm text-slate-200">
                        <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-400/10 text-emerald-300">✓</span><span>Accès complet pendant {{ (int) config('aviatrax.subscription_duration_days', 30) }} jours</span></li>
                        <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-400/10 text-emerald-300">✓</span><span>Soumission professionnelle des sorties</span></li>
                        <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-400/10 text-emerald-300">✓</span><span>Suivi des validations et des plateformes</span></li>
                        <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-400/10 text-emerald-300">✓</span><span>Historique et suivi de votre portefeuille</span></li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-950/55 p-4">
                    <p class="text-sm font-semibold text-white">Paiement sécurisé</p>
                    <p class="mt-2 text-xs leading-5 text-slate-500">La confirmation provient du système de paiement sécurisé. L’enregistrement, le mixage et le mastering restent des prestations studio distinctes.</p>
                </div>
            </aside>
        </section>
    @endif

    @if($projects->isNotEmpty())
        <section class="rounded-xl border border-slate-700 bg-slate-900 p-5 sm:p-6">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Release Hub</p>
                    <h2 class="mt-1 text-xl font-semibold text-white">Mes sorties</h2>
                </div>
                <span class="text-sm text-slate-400">{{ $projects->count() }} projet(s)</span>
            </div>
            <div class="space-y-3">
                @foreach($projects as $project)
                    <article class="flex flex-col gap-4 rounded-lg border border-slate-700 bg-slate-950/70 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            @if($project->cover_art_url)
                                <img src="{{ $project->cover_art_url }}" alt="Pochette de {{ $project->title }}" class="h-14 w-14 rounded-md object-cover">
                            @endif
                            <div class="min-w-0">
                                <h3 class="truncate font-semibold text-white">{{ $project->title }}</h3>
                                <p class="mt-1 text-sm text-slate-400">{{ ucfirst(str_replace('_', ' ', $project->current_stage->value)) }}</p>
                            </div>
                        </div>
                        @if($project->release_date)
                            <p class="text-sm font-medium text-emerald-300">Sortie officielle : {{ $project->release_date->format('d/m/Y') }}</p>
                        @else
                            <p class="text-sm text-slate-400">Date de sortie à confirmer par l’administration</p>
                        @endif
                        @if($project->distributions->where('status', 'published')->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach($project->distributions->where('status', 'published') as $distribution)
                                    @if($distribution->platform_url)
                                        <a href="{{ $distribution->platform_url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-indigo-300 hover:text-indigo-200">{{ $distribution->platform_name }}</a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if($subscriptions->isNotEmpty())
        <details class="rounded-xl border border-slate-700 bg-slate-900/70 p-5">
            <summary class="cursor-pointer font-medium text-slate-200">Historique des abonnements</summary>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[480px] text-left text-sm">
                    <thead class="border-b border-slate-700 text-xs uppercase text-slate-400"><tr><th class="pb-2">Date</th><th class="pb-2">Montant</th><th class="pb-2">État</th><th class="pb-2">Expiration</th></tr></thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($subscriptions as $item)
                            <tr>
                                <td class="py-3 text-slate-300">{{ $item->created_at->format('d/m/Y') }}</td>
                                <td class="py-3 text-slate-200">{{ number_format((float) $item->amount, 2) }} {{ $item->currency }}</td>
                                <td class="py-3">
                                    @if($item->status === 'active')
                                        <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2 py-1 text-xs text-emerald-300">Actif</span>
                                    @elseif($item->status === 'pending')
                                        <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-2 py-1 text-xs text-amber-300">En attente</span>
                                    @else
                                        <span class="rounded-full border border-red-500/30 bg-red-500/10 px-2 py-1 text-xs text-red-300">Échec / annulé</span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-400">{{ $item->ends_at?->format('d/m/Y') ?? 'Non applicable' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
    @endif
</div>
<script>
    (() => {
        const paymentTypes = document.querySelectorAll('input[name="payment_type"]');
        const phoneField = document.getElementById('phone-field');
        const phoneInput = document.getElementById('phone');
        const cardFields = document.getElementById('card-fields');
        const cardNumber = document.getElementById('card_number');
        const cardExpiry = document.getElementById('card_expiry');
        const cardCvv = document.getElementById('card_cvv');
        const cardName = document.getElementById('card_name');
        const cardNumberStatus = document.getElementById('card-number-status');

        function luhnValid(value) {
            let sum = 0;
            let doubleDigit = false;
            for (let i = value.length - 1; i >= 0; i -= 1) {
                let digit = Number(value[i]);
                if (doubleDigit) {
                    digit *= 2;
                    if (digit > 9) digit -= 9;
                }
                sum += digit;
                doubleDigit = !doubleDigit;
            }
            return sum % 10 === 0;
        }

        function visaValid(value) {
            const digits = value.replace(/\D/g, '');
            return /^4[0-9]{15}$/.test(digits) && luhnValid(digits);
        }

        function updateCardNumberStatus() {
            if (!cardNumberStatus) return;
            const digits = cardNumber.value.replace(/\D/g, '');
            if (!digits) {
                cardNumberStatus.className = 'mt-1 hidden text-xs';
                cardNumberStatus.textContent = '';
                return;
            }

            cardNumberStatus.className = 'mt-1 text-xs ' + (visaValid(digits) ? 'text-emerald-300' : 'text-red-300');
            cardNumberStatus.textContent = visaValid(digits)
                ? 'Carte Visa valide.'
                : 'Saisissez une carte Visa valide.';
        }

        cardNumber?.addEventListener('input', function () {
            const digits = cardNumber.value.replace(/\D/g, '').slice(0, 16);
            cardNumber.value = digits.replace(/(.{4})(?=.)/g, '$1 ');
            updateCardNumberStatus();
        });

        cardExpiry?.addEventListener('input', function () {
            let value = cardExpiry.value.replace(/\D/g, '').slice(0, 4);
            if (value.length >= 3) value = value.slice(0, 2) + '/' + value.slice(2);
            cardExpiry.value = value;
        });

        cardCvv?.addEventListener('input', function () {
            cardCvv.value = cardCvv.value.replace(/\D/g, '').slice(0, 4);
        });

        function syncPaymentMethod() {
            const mobileMoney = document.querySelector('input[name="payment_type"]:checked')?.value === '1';
            phoneField.hidden = !mobileMoney;
            phoneInput.required = mobileMoney;
            phoneInput.disabled = !mobileMoney;

            const cardPayment = document.querySelector('input[name="payment_type"]:checked')?.value === '2';
            cardFields.classList.toggle('hidden', !cardPayment);
            [cardNumber, cardExpiry, cardCvv, cardName].forEach((field) => {
                field.disabled = !cardPayment;
                field.required = cardPayment;
            });
        }

        paymentTypes.forEach((input) => input.addEventListener('change', syncPaymentMethod));

        document.querySelector('form[action="{{ route('subscriptions.checkout') }}"]')?.addEventListener('submit', function (event) {
            if (document.querySelector('input[name="payment_type"]:checked')?.value !== '2') return;

            const digits = cardNumber.value.replace(/\D/g, '');
            const match = cardExpiry.value.match(/^(0[1-9]|1[0-2])\/([0-9]{2})$/);
            const now = new Date();
            const expiryValid = match && (2000 + Number(match[2]) > now.getFullYear()
                || (2000 + Number(match[2]) === now.getFullYear() && Number(match[1]) >= now.getMonth() + 1));

            if (!visaValid(digits) || !expiryValid || !/^[0-9]{3,4}$/.test(cardCvv.value) || cardName.value.trim().length < 2) {
                event.preventDefault();
                cardFields.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (cardName.value.trim().length < 2) {
                    cardName.focus();
                } else {
                    cardNumber.focus();
                }
            }
        });

        syncPaymentMethod();
    })();
</script>
@endsection
