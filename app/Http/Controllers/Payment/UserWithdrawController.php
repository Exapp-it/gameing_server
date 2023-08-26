<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Withdraw;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UserWithdrawController extends Controller
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
            'currency'     => [
                'required',
                Rule::in(config('enums.currency')),
            ],
            'amount'       => 'required|numeric|min:1',
            'fiat_address' => 'required|string'
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
        $fiat_address = $validator->validated()['fiat_address'];

        Withdraw::create([
            "amount" => $amount,
            "currency" => $currency,
            "fiat_address" => $fiat_address,
            "user_id" => $request->user()->id
        ]);

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'message' => 'Successfully created withdraw'
                ]
            ],
            200
        );
    }
}
