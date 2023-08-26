<?php

namespace App\Services\Referral;

use App\Models\Referral;
use App\Models\User;
use App\Models\ReferralClick;
use Carbon\Carbon;

class ReferralService
{

    public static function createReferral(int $referrerId, int $referralId)
    {
        Referral::create([
            'referrer_id' => $referrerId,
            'referral_id' => $referralId,
            'joined_at' => Carbon::now(),
        ]);
    }

    public function getUserByReferralCode($code)
    {
        return User::where('referral_code', $code)->first();
    }

    public function registerReferralClick(User $user, $ip, $domain)
    {
        ReferralClick::create([
            'user_id' => $user->id,
            'ip' => $ip,
            'domain' => $domain ?? 'unknown',
        ]);
    }

    public function respondWithReferrerId($referrerId)
    {
        $cookieDuration = config('referral.cookie_duration', 43200);
        $response = response()->json([
            'referrer' => $referrerId,
            'redirect_url' => config('app.url'),
        ]);
        $response->withCookie(cookie('referrer_id', $referrerId, $cookieDuration));

        return $response;
    }

    public function genRefCode($length = 8)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
