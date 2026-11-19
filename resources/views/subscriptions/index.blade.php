@extends('layouts.app')

@section('title', 'Abonnement de distribution - Aviatrax Studio')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <header class="rounded-2xl border border-indigo-500/30 bg-gradient-to-br from-indigo-950/80 via-slate-900 to-slate-950 p-6 sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Aviatrax Studio</p>
        <h1 class="mt-2 text-3xl font-bold text-white">Abonnement de distribution</h1>
        <p class="mt-3 max-w-3xl leading-7 text-slate-300">La création de compte est gratuite. L’abonnement de 12 $ couvre uniquement l’accès à la plateforme et la distribution mondiale pendant 1 mois. La production, l’enregistrement, le mixage et le mastering en studio sont facturés séparément par Maisha Sound Studio.</p>
    </header>

    @if($subscription)
        <section class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-emerald-300">Abonnement actif</p>
                    <p class="mt-1 text-slate-200">Valide jusqu’au {{ $subscription->ends_at->format('d/m/Y') }}</p>
                </div>
                <span class="rounded-lg border border-emerald-500/30 bg-slate-950/40 px-4 py-2 text-lg font-bold text-white">12 $ / 1 mois</span>
            </div>
        </section>
    @else
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(280px,0.9fr)]">
            <div class="rounded-xl border border-slate-700 bg-slate-900 p-5 sm:p-6">
                <h2 class="text-xl font-semibold text-white">Activer votre accès distribution</h2>
                <p class="mt-2 text-sm leading-6 text-slate-400">Après confirmation du paiement sécurisé, votre abonnement sera actif pendant 30 jours. Choisissez le moyen de paiement et indiquez le numéro associé au compte à débiter.</p>

                @if($errors->any())
                    <div class="mt-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200" role="alert">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                @if($pendingSubscription = $subscriptions->firstWhere('status', 'pending'))
                    @if($pendingSubscription->order_number)
                        <div class="mt-5 rounded-lg border border-amber-500/30 bg-amber-500/10 p-4">
                            <p class="text-sm text-amber-100">Une demande de paiement est en attente. Confirmez-la sur votre téléphone, puis vérifiez son statut.</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <form action="{{ route('subscriptions.verify', $pendingSubscription) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">Vérifier le paiement</button>
                                </form>
                                <form action="{{ route('subscriptions.cancel', $pendingSubscription) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Annuler / recommencer</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-amber-200">La demande précédente n’a pas reçu de numéro de transaction. Vous pouvez soumettre une nouvelle demande.</p>
                    @endif
                @else
                    <form action="{{ route('subscriptions.checkout') }}" method="POST" class="mt-5 space-y-4">
                        @csrf
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
                        <fieldset>
                            <legend class="mb-2 block text-sm font-medium text-slate-200">Moyen de paiement</legend>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-700 bg-slate-950 p-3 text-sm text-slate-200 has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/10">
                                    <input type="radio" name="payment_type" value="1" @checked(old('payment_type', '1') === '1') required class="accent-indigo-400">
                                    Mobile Money
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-700 bg-slate-950 p-3 text-sm text-slate-200 has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/10">
                                    <input type="radio" name="payment_type" value="2" @checked(old('payment_type') === '2') required class="accent-indigo-400">
                                    Carte bancaire
                                </label>
                            </div>
                        </fieldset>
                        <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1 border-t border-slate-700 pt-4">
                            <span class="text-3xl font-bold text-white">12 $</span>
                            <span class="text-sm text-slate-400">pour 1 mois de distribution</span>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white shadow-lg shadow-indigo-950/30 transition hover:bg-indigo-500">Payer de manière sécurisée</button>
                        <p class="text-center text-xs text-slate-500">Paiement sécurisé par Mobile Money ou carte. La production en studio est distincte.</p>
                    </form>
                @endif
            </div>

            <aside class="rounded-xl border border-slate-700 bg-slate-900/70 p-5 sm:p-6">
                <h2 class="text-lg font-semibold text-white">Ce que couvre l’abonnement</h2>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-300">
                    <li class="flex gap-2"><span class="text-emerald-300">✓</span><span>Accès à la plateforme pendant 30 jours</span></li>
                    <li class="flex gap-2"><span class="text-emerald-300">✓</span><span>Soumission de masters et pochettes pour distribution mondiale</span></li>
                    <li class="flex gap-2"><span class="text-emerald-300">✓</span><span>Suivi de validation, date de sortie et liens de distribution</span></li>
                    <li class="flex gap-2"><span class="text-emerald-300">✓</span><span>Suivi du portefeuille de points et des revenus</span></li>
                </ul>
                <p class="mt-5 rounded-lg border border-slate-700 bg-slate-950/70 p-3 text-xs leading-5 text-slate-400">L’enregistrement, le mixage et le mastering sont des services distincts, facturés par Maisha Sound Studio selon le projet.</p>
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
