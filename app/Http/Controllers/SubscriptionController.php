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
            'card_number' => ['nullable', 'string', 'regex:/^[0-9][0-9 ]{12,18}$/'],
            'card_expiry' => ['nullable', 'string', 'regex:/^(0[1-9]|1[0-2])\/([0-9]{2})$/'],
            'card_cvv' => ['nullable', 'string', 'regex:/^[0-9]{3,4}$/'],
        ], [
            'phone.regex' => 'Saisissez un numéro de téléphone valide avec indicatif pays.',
            'payment_type.in' => 'Choisissez Mobile Money ou carte bancaire.',
            'card_number.regex' => 'Saisissez un numéro de carte Visa valide.',
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
                    'FlexPay n’a pas encore confirmé un paiement réussi. Attendez le webhook officiel.'
                );
            }

            return redirect()->route('subscriptions.index')->with(
                'info',
                'Paiement confirmé par FlexPay. Activation définitive attendue par webhook.'
            );
        } catch (Throwable $exception) {
            Log::warning('La vérification du paiement est temporairement indisponible.', [
                'subscription_id' => $subscription->id,
                'exception' => $exception::class,
            ]);

            return redirect()->route('subscriptions.index')->withErrors([
                'payment' => 'Le statut du paiement n’est pas encore disponible. Réessayez dans quelques instants.'
            ]);
        }

        return redirect()->route('subscriptions.index')->with(
            $subscription->status === 'active' ? 'success' : 'error',
            $subscription->status === 'active'
                ? 'Votre abonnement est maintenant actif pour 1 mois.'
                : 'Le paiement n’est pas encore confirmé. Vous pourrez réessayer après l’autorisation.'
        );
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