<?php

namespace App\Services\Referral;

use App\Models\User;

class ReferralStatisticService
{

    protected $transactionTypes;

    public function __construct()
    {
        $this->transactionTypes = config('enums.transaction_types');
    }

    public function mapReferralMetrics(User $user): array
    {
        return [
            'balance' => formatBalance($user->balance),
            'linkClickCount' => $user->referralClicks->count(),
            'totalReferrals' => $this->getCountTotalReferrals($user),
            'referralDepositSum' => formatBalance($this->getSumOfDepositsByReferrals($user)),
            'referralDepositCount' => $this->getCountTotalDepositsByReferrals($user),
            'totalLossFromReferrals' => formatBalance($this->getLossByReferrals($user)),
            'totalWinsFromReferrals' => formatBalance($this->getWinsByReferrals($user)),
            'earn' => formatBalance($user->affiliateProgram->sum('amount')),
        ];
    }


    public function getCountTotalReferrals(User $user)
    {
        return $user->referrals->count();
    }

    public function getCountTotalDepositsByReferrals(User $user)
    {
        return $user->referrals()
            ->join('payments', 'referrals.referral_id', '=', 'payments.user_id')
            ->where('payments.status', 'DONE')
            ->count();
    }


    public function getSumOfDepositsByReferrals(User $user)
    {
        return $user->referrals()
            ->join('payments', 'referrals.referral_id', '=', 'payments.user_id')
            ->where('payments.status', 'DONE')
            ->sum('payments.amount');
    }


    public function getDepositsByReferral(User $referral)
    {
        return $referral->payments->where('status', 'DONE')->get();
    }


    public function getSumOfDepositsByReferral(User $referral)
    {
        return $referral->payments->where('status', 'DONE')->sum('amount');
    }

    public function getLossByReferrals(User $user)
    {
        $totalDeposits = $this->getTotalByTransactionType($user, 'deposit');
        $totalWithdraws = $this->getTotalByTransactionType($user, 'withdraw');
        $loss = $totalWithdraws - $totalDeposits;

        return max($loss, 0);
    }

    public function getWinsByReferrals(User $user)
    {
        $totalDeposits = $this->getTotalByTransactionType($user, 'deposit');
        $totalWithdraws = $this->getTotalByTransactionType($user, 'withdraw');
        $win = $totalDeposits - $totalWithdraws;

        return max($win, 0);
    }

    protected function getTotalByTransactionType(User $user, string $type): float
    {
        return $user->referrals()
            ->join('transactions', 'referrals.referral_id', '=', 'transactions.user_id')
            ->where('transactions.type', $this->transactionTypes[$type])
            ->sum('transactions.amount');
    }
}
