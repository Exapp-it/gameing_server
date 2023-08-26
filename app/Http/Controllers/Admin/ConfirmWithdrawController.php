<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Models\Withdraw;
use App\Services\Expay\Client;

use Illuminate\Http\Client\RequestException;

class ConfirmWithdrawController extends Controller
{
    /**
     * Handle the incoming Request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $validator->errors()->toJson()
                ], 400
            );
        }

        $id = $validator->validated()['id'];
        $withdraw = Withdraw::findOrFail($id);

        if ($withdraw->confirmed) {
            return response()->json(
                [
                    'status' => 'error',
                    'error'  => 'Withdraw already confirmed'
                ], 400
            );
        }

        $withdraw->confirmed = true;
        $withdraw->save();

        $amount = floatval($withdraw->amount);
        $currency = $withdraw->currency;
        $fiat_address = $withdraw->fiat_address;

        $url = config('app.url').'/api/payment/withdraw_status?id='.$withdraw->id;

        $client = app()->make(Client::class);
        $result = $client->outcome_refer([
            'currency'     => $currency,
            'amount'       => $amount,
            'url'          => $url,
            'fiat_address' => $fiat_address
        ]);

        if ($result instanceof RequestException) {
            throw $result;
        }

        if ($result === false) {
            $withdraw->status = 'CANCELLED';
            $withdraw->save();
            return response()->json(
                [
                    'status' => 'error',
                    'error' => 'Couldn\'t create transaction, try again'
                ], 400
            );
        }

        $withdraw->internal_id = $result;
        $withdraw->save();

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'message' => 'Successfully created outcome refer'
                ]
            ],
            200
        );
    }
}
