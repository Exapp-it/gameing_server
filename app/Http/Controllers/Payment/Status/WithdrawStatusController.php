<?php

namespace App\Http\Controllers\Payment\Status;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Withdraw;
use App\Services\Expay\Client;

class WithdrawStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $withdraw = Withdraw::find($request->id);
        $client = app()->make(Client::class);
        $status = $client->get_status($withdraw->internal_id);

        if ($withdraw->status !== $status && $status === "DONE") {
            $user = $withdraw->user;
            $user->balance -= $withdraw->amount;
            $user->save();
        }

        $withdraw->status = $status;
        $withdraw->save();
    }
}
