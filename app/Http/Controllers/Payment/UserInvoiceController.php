<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Models\Payment;
use App\Services\Expay\Client;
use App\Jobs\StrikeJob;

use Illuminate\Http\Client\RequestException;

class UserInvoiceController extends Controller
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
            'currency' => [
                'required',
                Rule::in(config('enums.currency')),
            ],
            'amount'   => 'required|numeric|min:1'
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
        $currency = $validator->validated()['currency'];

        $payment = Payment::create([
            'amount' => $amount,
            'user_id' => $request->user()->id,
            'currency' => $currency
        ]);

        $url = config('app.url').'/api/payment/payment_status?id='.$payment->id;
        $redirect_url = config('app.url').'/';

        $client = app()->make(Client::class);
        $result = $client->income_refer([
            'currency' => $currency,
            'amount' => $amount,
            'url' => $url,
            'redirect_url' => $redirect_url
        ]);

        if ($result instanceof RequestException) {
            throw $result;
        }

        if ($result === false) {
            $payment->status = 'CANCELLED';
            $payment->save();
            return response()->json(
                [
                    'status' => 'error',
                    'error' => 'Couldn\'t create transaction, try again'
                ], 400
            );
        }

        $payment->internal_id = $result['internal_id'];
        $payment->save();

        dispatch(new StrikeJob([
            'user' => $request->user()
        ]));

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'message' => 'Successfully created income refer',
                    'form_link' => $result['form_link']
                ]
            ],
            200
        );
    }
}
