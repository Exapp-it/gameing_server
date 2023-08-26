<?php

namespace App\Http\Controllers\Payment\Status;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WalletInvoice;
use App\Services\Expay\Client;

class WalletInvoiceStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $invoice = WalletInvoice::find($request->id);
        $client = app()->make(Client::class);
        $status = $client->get_status($invoice->internal_id);
        $invoice->status = $status;
        $invoice->save();
    }
}
