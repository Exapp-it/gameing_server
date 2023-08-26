<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Affiliate\AffiliateService;
use Illuminate\Http\Request;

class AffiliateManagerController extends Controller
{
    private $service;

    public function __construct(AffiliateService $service)
    {
        $this->service = $service;
    }


    public function index(Request $request)
    {
        $user = $request->user();
        $statistics = $this->service->getMetrics($user);

        return $this->service
            ->successResponse($statistics);
    }
}
