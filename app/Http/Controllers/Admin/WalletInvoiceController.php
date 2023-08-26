<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Models\WalletInvoice;
use App\Services\Expay\Client;

use Illuminate\Http\Client\RequestException;

class WalletInvoiceController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $validator->errors()->toJson()
                ], 400
            );
        }

        $amount = floatval($validator->validated()['amount']);

        $invoice = WalletInvoice::create([
            'amount' => $amount
        ]);

        $url = config('app.url').'/api/payment/wallet_invoice_status?id='.$invoice->id;

        $client = app()->make(Client::class);
        $result = $client->wallet_invoice([
            'currency' => "USDTTRC",
            'amount' => $amount,
            'url' => $url
        ]);

        if ($result instanceof RequestException) {
            throw $result;
        }

        if ($result === false) {
            $invoice->status = 'CANCELLED';
            $invoice->save();
            return response()->json(
                [
                    'status' => 'error',
                    'error' => 'Couldn\'t create transaction, try again'
                ], 400
            );
        }

        $invoice->internal_id = $result;
        $invoice->save();

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'message' => 'Successfully created wallet invoice'
                ]
            ],
            200
        );
    }
}
