<?php

namespace App\Http\Controllers\Payment\Status;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Services\Expay\Client;

class PaymentStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $payment = Payment::find($request->id);
        $client = app()->make(Client::class);
        $status = $client->get_status($payment->internal_id);

        if ($payment->status !== $status && $status === "DONE") {
            $user = $payment->user;
            $user->balance += $payment->amount;
            $user->save();
        }

        $payment->status = $status;
        $payment->save();
    }
}
