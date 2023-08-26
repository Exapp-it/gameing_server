<?php

namespace App\Http\Controllers\Payment;

use App\Services\Expay\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $client = app()->make(Client::class);
        return $client->balance();
    }
}
