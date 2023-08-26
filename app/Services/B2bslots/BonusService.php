<?php

namespace App\Services\B2bslots;

use App\Models\B2bBonus;
use App\Models\User;


class BonusService
{
    public static function getBonuses(User $user)
    {
        $bonus = B2bBonus::where('status', true)->first();

        if ($bonus) {
            $cp = formatBalance($bonus->cp);
            return [
                "id" => $bonus->id,
                "count" => self::getCount($user->bonus, $bonus->bet, $bonus->lines),
                "bet" => $bonus->bet,
                "lines" => $bonus->lines,
                "mpl" => $bonus->mpl_bonus,
                "cp" => "$cp",
                "version" => 2,
            ];
        }
    }

    private static function getCount($bonusBalance, $bet, $lines)
    {
        $amount = $bet * $lines;
        if ($amount <= 0) {
            return 0;
        }

        return floor($bonusBalance / $amount);
    }
}
