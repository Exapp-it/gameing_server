<?php

namespace App\Http\Controllers\Payment\Status;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WalletWithdraw;
use App\Services\Expay\Client;

class WalletWithdrawStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $withdraw = WalletWithdraw::find($request->id);
        $client = app()->make(Client::class);
        $status = $client->get_status($withdraw->internal_id);
        $withdraw->status = $status;
        $withdraw->save();
    }
}
