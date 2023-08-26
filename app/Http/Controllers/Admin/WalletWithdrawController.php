<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Models\WalletWithdraw;
use App\Services\Expay\Client;

use Illuminate\Http\Client\RequestException;

class WalletWithdrawController extends Controller
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
            'amount' => 'required|numeric|min:0',
            'wallet' => 'required|string'  
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
        $wallet = $validator->validated()['wallet'];

        $withdraw = WalletWithdraw::create([
            'amount' => $amount,
            'withdraw_wallet' => $wallet
        ]);

        $url = config('app.url').'/api/payment/wallet_withdraw_status?id='.$withdraw->id;

        $client = app()->make(Client::class);
        $result = $client->wallet_withdraw([
            'currency' => "USDTTRC",
            'amount' => $amount,
            'url' => $url,
            'address' => $wallet
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
                    'message' => 'Successfully created wallet withdraw'
                ]
            ],
            200
        );
    }
}
