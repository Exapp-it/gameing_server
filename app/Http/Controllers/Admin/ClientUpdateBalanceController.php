<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ClientUpdateBalanceController extends Controller
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
            'amount'    => 'required|numeric|min:1',
            'direction' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $validator->errors()->toJson()
                ], 400
            );
        }

        $user = User::findOrFail($request->id);

        $delta = $validator->validated()['amount'];
        if ($validator->validated()['direction']) {
            $delta *= -1;
        }

        $user->balance += $delta;
        $user->save();
        
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Successfully changed balance'
            ], 200
        );
    }
}
