<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Subscription;
use App\Services\FlexPayPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = $this->artist();
        $subscription = $user->activeSubscription()->first();
        $subscriptions = $user->subscriptions()->latest()->limit(10)->get();
        $subscriptions->where('status', 'pending')
            ->filter(fn (Subscription $subscription) => $subscription->updated_at->lt(now()->subMinutes(30)))
            ->each(function (Subscription $subscription) {
                $subscription->update([
                    'status' => 'failed',
                    'order_number' => null,
                ]);
            });
        $projects = $user->projects()
            ->where('project_type', 'distribution_only')
            ->with('distributions')
            ->latest()
            ->get();

        return view('subscriptions.index', compact('subscription', 'subscriptions', 'projects'));
    }

    public function checkout(Request $request, FlexPayPaymentService $paymentService)
    {
        $user = $this->artist();
        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'regex:/^\+?[0-9]{9,15}$/'],
            'payment_type' => 'required|in:1,2',
            'card_name' => ['nullable', 'string', 'max:100'],
            'card_number' => ['nullable', 'string', 'regex:/^[0-9][0-9 ]{12,18}$/'],
            'card_expiry' => ['nullable', 'string', 'regex:/^(0[1-9]|1[0-2])\/([0-9]{2})$/'],
            'card_cvv' => ['nullable', 'string', 'regex:/^[0-9]{3,4}$/'],
        ], [
            'phone.regex' => 'Saisissez un numéro de téléphone valide avec indicatif pays.',
            'payment_type.in' => 'Choisissez Mobile Money ou carte bancaire.',
            'card_name.max' => 'Le nom du titulaire ne peut pas dépasser 100 caractères.',
            'card_number.regex' => 'Saisissez un numéro de carte valide.',
            'card_expiry.regex' => 'Saisissez une date au format MM/AA.',
            'card_cvv.regex' => 'Saisissez un CVV valide.',
        ]);

        if ($validated['payment_type'] === '1' && blank($validated['phone'] ?? null)) {
            return redirect()->route('subscriptions.index')->withErrors([
                'phone' => 'Le numéro de téléphone est obligatoire pour Mobile Money.',
            ]); 
        }

        if ($validated['payment_type'] === '2') {
            if (! filled($validated['card_number'] ?? null)
                || ! filled($validated['card_expiry'] ?? null)
                || ! filled($validated['card_cvv'] ?? null)) {
                return redirect()->route('subscriptions.index')->withErrors([
                    'card_number' => 'Les informations de carte sont obligatoires pour payer par carte.',
                ]);
            }

            $cardName = trim((string) ($validated['card_name'] ?? ''));
            $cardNumber = preg_replace('/\s+/', '', (string) ($validated['card_number'] ?? ''));

            if ($cardName === '') {
                return redirect()->route('subscriptions.index')->withErrors(['card_name' => 'Le nom du titulaire est obligatoire.']);
            }

            if (! str_starts_with($cardNumber, '4') || strlen($cardNumber) !== 16) {
                return redirect()->route('subscriptions.index')->withErrors(['card_number' => 'Saisissez un numéro de carte Visa valide.']);
            }

            $sum = 0;
            $doubleDigit = false;
            for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
                $digit = (int) $cardNumber[$i];
                if ($doubleDigit) {
                    $digit *= 2;
                    if ($digit > 9) {
                        $digit -= 9;
                    }
                }
                $sum += $digit;
                $doubleDigit = ! $doubleDigit;
            }

            if ($sum % 10 !== 0) {
                return redirect()->route('subscriptions.index')->withErrors(['card_number' => 'Le numéro de carte ne passe pas la validation Visa/Luhn.']);
            }

            if (preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', (string) ($validated['card_expiry'] ?? ''), $matches) !== 1) {
                return redirect()->route('subscriptions.index')->withErrors(['card_expiry' => 'Saisissez une date d’expiration valide au format MM/AA.']);
            }

            $expiryYear = 2000 + (int) $matches[2];
            $expiryMonth = (int) $matches[1];
            if ($expiryYear < now()->year || ($expiryYear === now()->year && $expiryMonth < now()->month)) {
                return redirect()->route('subscriptions.index')->withErrors(['card_expiry' => 'La carte est expirée.']);
            }

            if (blank($validated['card_cvv'] ?? null)) {
                return redirect()->route('subscriptions.index')->withErrors(['card_cvv' => 'Le CVV est obligatoire.']);
            }
        }

        $phone = (string) ($validated['phone'] ?? '');

        if ($user->hasActiveSubscription()) {
            return redirect()->route('subscriptions.index')->with('success', 'Votre abonnement de distribution est déjà actif.');
        }

        if (! $paymentService->isConfigured()) {
            return redirect()->route('subscriptions.index')->withErrors([
                'payment' => 'Le paiement sécurisé est temporairement indisponible. Réessayez plus tard.',
            ]);
        }

        $subscription = $user->subscriptions()->create([
            'amount' => (float) config('aviatrax.subscription_amount', 12),
            'currency' => (string) config('aviatrax.subscription_currency', 'USD'),
            'status' => 'pending',
            'payment_reference' => (string) Str::uuid(),
        ]);

        try {
            $cardNumber = isset($validated['card_number']) ? preg_replace('/\s+/', '', $validated['card_number']) : null;
            $cardExpiry = $validated['card_expiry'] ?? null;
            $cardCvv = $validated['card_cvv'] ?? null;

            $payment = $paymentService->requestPayment(
                $subscription,
                $phone,
                $validated['payment_type'],
                route('payments.webhook'),
                $cardNumber,
                $cardExpiry,
                $cardCvv
            );

            $subscription->update(['order_number' => $payment['order_number']]);

            if (! blank($payment['redirect_url'])) {
                return redirect()->away($payment['redirect_url']);
            }

            return redirect()->route('subscriptions.index')->with(
                'success',
                'Une demande de paiement sécurisé a été envoyée. Confirmez-la sur votre téléphone, puis actualisez le statut ci-dessous.'
            );
        } catch (Throwable $exception) {
            $subscription->update(['status' => 'failed']);

            Log::warning('Impossible de démarrer une session de paiement d’abonnement.', [
                'subscription_id' => $subscription->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('subscriptions.index')->withErrors([
                'payment' => $exception->getMessage(),
            ]);
        }
    }

    public function verify(Request $request, Subscription $subscription, FlexPayPaymentService $paymentService)
    {
        abort_unless($subscription->user_id === $request->user()->id, 404);
        abort_unless($subscription->status === 'pending' && $subscription->order_number, 404);

        try {
            $result = $paymentService->verifyTransaction($subscription, $subscription->order_number);

            if (! $result['paid']) {
                return redirect()->route('subscriptions.index')->with(
                    'error',
                    'FlexPay n’a pas encore confirmé un paiement réussi. Attendez quelques instants puis réessayez.'
                );
            }

            $subscription = $this->applyVerifiedResult($subscription, $result);

            if ($subscription->status !== 'active') {
                return redirect()->route('subscriptions.index')->with(
                    'error',
                    'Le paiement a été reçu, mais l’abonnement n’a pas pu être activé.'
                );
            }

            return redirect()->route('subscriptions.success', $subscription)
                ->with('success', 'Votre abonnement de distribution est maintenant actif.');
        } catch (Throwable $exception) {
            Log::warning('La vérification du paiement est temporairement indisponible.', [
                'subscription_id' => $subscription->id,
                'exception' => $exception::class,
            ]);

            return redirect()->route('subscriptions.index')->withErrors([
                'payment' => 'Le statut du paiement n’est pas encore disponible. Réessayez dans quelques instants.'
            ]);
        }
    }

    public function webhook(Request $request, FlexPayPaymentService $paymentService): JsonResponse
    {
        $reference = trim((string) $request->input('reference', $request->input('paymentReference', '')));
        $orderNumber = trim((string) $request->input('orderNumber', $request->input('order_number', '')));

        if ($reference === '' || $orderNumber === '') {
            return response()->json(['message' => 'Payload de paiement incomplet.'], 422);
        }

        $subscription = Subscription::query()
            ->where('payment_reference', $reference)
            ->first();

        if (! $subscription || $subscription->status !== 'pending') {
            return response()->json(['received' => true]);
        }

        if (! $subscription->order_number || ! hash_equals($subscription->order_number, $orderNumber)) {
            return response()->json(['message' => 'Référence de transaction invalide.'], 400);
        }

        try {
            $result = $paymentService->verifyTransaction($subscription, $orderNumber);
            $this->applyVerifiedResult($subscription, $result);
        } catch (Throwable $exception) {
            Log::warning('Callback reçu mais transaction non vérifiable.', [
                'subscription_id' => $subscription->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'Transaction non vérifiable.'], 202);
        }

        return response()->json(['received' => true]);
    }

    public function cancel(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->user_id === $request->user()->id, 404);
        abort_unless($subscription->status === 'pending', 404);

        $subscription->update([
            'status' => 'failed',
            'order_number' => null,
        ]);

        return redirect()->route('subscriptions.index')->with(
            'success',
            'Paiement annulé. Vous pouvez soumettre une nouvelle demande.'
        );
    }

    public function success(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->user_id === $request->user()->id, 404);
        abort_unless($subscription->status === 'active', 404);

        if ($subscription->ends_at && $subscription->ends_at->isPast()) {
            $subscription->update(['status' => 'expired']);
            abort(404);
        }

        return view('subscriptions.success', compact('subscription'));
    }

    private function applyVerifiedResult(Subscription $subscription, array $result): Subscription
    {
        return DB::transaction(function () use ($subscription, $result) {
            $lockedSubscription = Subscription::query()
                ->whereKey($subscription->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSubscription->status !== 'pending') {
                return $lockedSubscription;
            }

            if ($result['paid']) {
                $lockedSubscription->user->subscriptions()
                    ->where('status', 'active')
                    ->update(['status' => 'expired']);

                $lockedSubscription->update([
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addDays((int) config('aviatrax.subscription_duration_days', 30)),
                    'provider_reference' => $result['provider_reference'],
                ]);
            } else {
                $lockedSubscription->update([
                    'status' => 'failed',
                    'provider_reference' => $result['provider_reference'],
                ]);
            }

            return $lockedSubscription->fresh();
        });
    }

    private function artist(): \App\Models\User
    {
        $user = request()->user();

        abort_unless($user && $user->isArtiste(), 403);

        return $user;
    }
}