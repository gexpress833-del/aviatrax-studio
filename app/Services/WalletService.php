<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Wallet;
use App\Models\PointTransaction;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getOrCreateWallet(User $user): Wallet
    {
        return $user->wallet()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'points_balance' => 0,
        ]);
    }

    public function credit(User $user, int $points, ?Project $project = null, string $description = 'Crédit de points', ?int $createdBy = null): PointTransaction
    {
        $wallet = $this->getOrCreateWallet($user);

        $wallet->increment('points_balance', $points);

        return PointTransaction::create([
            'wallet_id' => $wallet->id,
            'project_id' => $project?->id,
            'type' => 'credit',
            'points' => $points,
            'description' => $description,
            'created_by' => $createdBy ?? $user->id,
        ]);
    }

    public function debit(User $user, int $points, ?Project $project = null, string $description = 'Débit de points', ?int $createdBy = null): PointTransaction
    {
        $wallet = $this->getOrCreateWallet($user);

        if ($wallet->points_balance < $points) {
            throw new \RuntimeException('Solde insuffisant pour cette opération.');
        }

        $wallet->decrement('points_balance', $points);

        return PointTransaction::create([
            'wallet_id' => $wallet->id,
            'project_id' => $project?->id,
            'type' => 'debit',
            'points' => $points,
            'description' => $description,
            'created_by' => $createdBy ?? $user->id,
        ]);
    }

    public function withdrawalThreshold(): int
    {
        $setting = Setting::where('key', 'minimum_withdrawal_points')->first();

        return (int) ($setting?->value['threshold'] ?? config('aviatrax.withdrawal_threshold', 5000));
    }

    public function pointsPerDollar(): int
    {
        $setting = Setting::where('key', 'points_conversion_rate')->first();

        return max(1, (int) ($setting?->value['points_per_dollar'] ?? config('aviatrax.points_conversion_rate', 100)));
    }

    public function cdfPerDollar(): float
    {
        $setting = Setting::where('key', 'currency_exchange_rate')->first();

        return (float) ($setting?->value['cdf_per_usd'] ?? 0);
    }

    public function conversionRate(): float
    {
        return (float) $this->pointsPerDollar();
    }

    public function toCash(int $points, string $currency = 'USD'): float
    {
        $usdAmount = $points / $this->pointsPerDollar();

        return match (strtoupper($currency)) {
            'USD' => round($usdAmount, 2),
            'CDF' => round($usdAmount * $this->cdfPerDollar(), 2),
            default => throw new \InvalidArgumentException('Devise non prise en charge.'),
        };
    }
}
