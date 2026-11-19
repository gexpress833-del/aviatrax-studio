<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FlexPayPaymentService
{
    public function isConfigured(): bool
    {
        return filled(config('services.flexpay.base_url'))
            && filled(config('services.flexpay.token'))
            && filled(config('services.flexpay.merchant'));
    }

    public function requestPayment(Subscription $subscription, string $phone, string $type, string $callbackUrl, ?string $cardNumber = null, ?string $cardExpiry = null, ?string $cardCvv = null): array
    {
        $this->ensureConfigured();

        $phone = $this->normalizePhone($phone);

        $payload = [
            'merchant' => config('services.flexpay.merchant'),
            'type' => $type,
            'reference' => $subscription->payment_reference,
            'amount' => number_format((float) $subscription->amount, 2, '.', ''),
            'currency' => $subscription->currency,
            'callbackUrl' => $callbackUrl,
        ];

        if ($type === '1') {
            $payload['phone'] = $phone;
        } elseif ($type === '2') {
            $payload['customerCard'] = $cardNumber;
            $payload['customerExpirationCard'] = $cardExpiry;
            $payload['cvv'] = $cardCvv;
        }

        $response = Http::acceptJson()
            ->withToken(config('services.flexpay.token'))
            ->timeout(20)
            ->post($this->endpoint('paymentService'), $payload);

        $payment = $response->json();

        if (! $response->successful()) {
            $message = is_array($payment) ? ($payment['message'] ?? null) : null;
            throw new RuntimeException($message ? 'FlexPay : ' . $message : 'Le paiement a été refusé par le serveur sécurisé.');
        }

        if (! is_array($payment)
            || (string) ($payment['code'] ?? '') !== '0'
            || blank($payment['orderNumber'] ?? null)) {
            $message = is_array($payment) ? ($payment['message'] ?? null) : null;
            throw new RuntimeException($message ? 'FlexPay : ' . $message : 'La demande de paiement a été refusée.');
        }

        $redirectUrl = $payment['redirectUrl']
            ?? $payment['redirectURL']
            ?? $payment['paymentUrl']
            ?? $payment['paymentURL']
            ?? $payment['url']
            ?? null;

        return [
            'order_number' => (string) $payment['orderNumber'],
            'message' => (string) ($payment['message'] ?? ''),
            'redirect_url' => is_string($redirectUrl) ? $redirectUrl : null,
        ];
    }

    public function verifyTransaction(Subscription $subscription, string $orderNumber): array
    {
        $this->ensureConfigured();

        $response = Http::acceptJson()
            ->withToken(config('services.flexpay.token'))
            ->timeout(20)
            ->get($this->endpoint('check/' . rawurlencode($orderNumber)))
            ->throw()
            ->json();

        $transaction = $response['transaction'] ?? null;

        if (! is_array($response)
            || (string) ($response['code'] ?? '') !== '0'
            || ! is_array($transaction)
            || (string) ($transaction['orderNumber'] ?? '') !== $orderNumber
            || (string) ($transaction['reference'] ?? '') !== $subscription->payment_reference) {
            throw new RuntimeException('La transaction n’a pas pu être vérifiée.');
        }

        $amountMatches = number_format((float) ($transaction['amount'] ?? -1), 2, '.', '')
            === number_format((float) $subscription->amount, 2, '.', '');
        $currencyMatches = strtoupper((string) ($transaction['currency'] ?? ''))
            === strtoupper($subscription->currency);

        if (! $amountMatches || ! $currencyMatches) {
            throw new RuntimeException('Les détails de la transaction ne correspondent pas à l’abonnement.');
        }

        return [
            'paid' => (string) ($transaction['status'] ?? '') === '0',
            'provider_reference' => isset($transaction['provider_reference'])
                ? (string) $transaction['provider_reference']
                : null,
        ];
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Le paiement sécurisé n’est pas configuré.');
        }
    }

    private function endpoint(string $path): string
    {
        return rtrim((string) config('services.flexpay.base_url'), '/') . '/api/rest/v1/' . ltrim($path, '/');
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (str_starts_with($phone, '243')) {
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        return '243' . $phone;
    }
}