<?php

namespace App\Services\Affiliate;

use App\Models\AffiliateProgram;
use App\Models\User;
use App\Services\Helper;
use App\Services\Referral\ReferralStatisticService;
use Carbon\Carbon;

class AffiliateService
{
    private $statisticService;

    public function __construct(ReferralStatisticService $statisticService)
    {
        $this->statisticService = $statisticService;
    }

    public function rewardReferrer(User $user)
    {
        $referrer = $user->referredBy;

        if (!$referrer) {
            return;
        }

        $transactionsData = $this->getLastHourTransactions($user);

        if (!$transactionsData) {
            return;
        }

        $reward = $transactionsData['totalReward'];

        if ($reward > 0) {
            $this->sendReward($reward, $user, $referrer->user);
        }
    }


    public function getLastHourTransactions(User $user): ?array
    {
        $transactionTypes = config('enums.transaction_types');
        $startOfHour = Carbon::now()->startOfHour();
        $endOfHour = Carbon::now()->endOfHour();
        $lastProviderType = null;
        $totalDeposit = 0;
        $totalWithdraws = 0;
        $totalReward = 0;
        $loss = 0;
        $win = 0;

        $transactions = $user->transactions()
            ->whereBetween('created_at', [$startOfHour, $endOfHour])
            ->get();

        if ($transactions->isEmpty()) {
            return null;
        }

        foreach ($transactions as $transaction) {
            if ($transaction->type == $transactionTypes['deposit']) {
                $totalDeposit += $this->convertToUsd($transaction->currency, $transaction->amount);
                $lastProviderType = $transaction->game->type;
            } elseif ($transaction->type == $transactionTypes['withdraw']) {
                $totalWithdraws += $this->convertToUsd($transaction->currency, $transaction->amount);
            }
        }

        $delta = $totalDeposit - $totalWithdraws;

        if (isMinusValue($delta)) {
            $win = abs($delta);
        } else {
            $loss = $delta;
            $totalReward = $this->calculateReward($loss, $lastProviderType);
        }

        return [
            'totalDeposit' => formatBalance($totalDeposit),
            'totalWithdraws' => formatBalance($totalWithdraws),
            'totalReward' => formatBalance($totalReward),
            'loss' => formatBalance($loss),
            'win' => formatBalance($win),
        ];
    }

    public function calculateReward($amount, $providerType = null): float
    {
        $providerPercent = $this->getProviderPercent($providerType);
        $paySystemPercent = config('enums.costs.pay_system');
        $bonusPercent = config('enums.affiliate.RewardPercent');

        $reducedAmount = $amount * (1 - $providerPercent - $paySystemPercent);
        return $reducedAmount * $bonusPercent;
    }

    private function getProviderPercent($providerType): float
    {
        switch ($providerType) {
            case 1:
                return config('enums.costs.tomhorn');
            case 2:
                return config('enums.costs.mancala');
            case 3:
                return config('enums.costs.b2bslots');
            default:
                return 0;
        }
    }

    public function getMetrics(User $user)
    {
        return $this->statisticService->mapReferralMetrics($user);
    }

    public function successResponse(array $data)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    private function convertToUsd($currency, $amount): float
    {
        return (float) formatBalance(Helper::convertToCurrency(
            $currency,
            'USD',
            $amount
        ));
    }

    private function sendReward(float $reward, User $user, User $referrer): void
    {
        AffiliateProgram::create([
            'user_id' => $referrer->id,
            'referral_id' => $user->id,
            'amount' => $reward,
            'completed' => true,
        ]);

        $referrer->increment('balance', $reward);
    }
}
