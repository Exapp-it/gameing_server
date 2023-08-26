<?php

namespace App\Http\Controllers;


use App\Services\Referral\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ReferralController extends Controller
{
    protected $service;

    public function __construct(ReferralService $referralService)
    {
        $this->service = $referralService;
    }

    public function __invoke(Request $request)
    {
        $code = $request->route('code');

        if (!$code) {
            Log::error('Referral code not found in the request.');
            throw new \InvalidArgumentException('Code not found');
        }

        $referrer = $this->service->getUserByReferralCode($code);

        if (!$referrer) {
            Log::error('Referrer not found for the provided code: ' . $code);
            throw new \InvalidArgumentException('Referrer not found for code: ' . $code);
        }
        $this->service->registerReferralClick(
            $referrer,
            $request->ip(),
            $request->headers->get('referer')
        );

        return $this->service->respondWithReferrerId($referrer->id);
    }
}
