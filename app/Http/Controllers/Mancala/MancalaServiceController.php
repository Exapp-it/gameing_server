<?php

namespace App\Http\Controllers\Mancala;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GameSession;
use App\Models\Transaction;
use App\Models\User;

use App\Services\Mancala\Client;

/*

To-do:
- Currency conversion
- Free games check
- Line count

*/

class MancalaServiceController extends Controller
{
    public function Balance(Request $request)
    {
        $data = $request->all();

        $sessionID = $data['SessionId'];
        $hash = $data['Hash'];

        $client = app()->make(Client::class);
        $true_hash = $client->genHash(['/Balance', $sessionID, $client->key]);

        if ($hash !== $true_hash) {
            return response()->json([
                "Error"   => config('enums.mancalaCodes')['HashMismatch'],
                "Msg" => "Invalid hash"
            ]);
        }

        $session = GameSession::where("session_id", "=", $sessionID)->first();

        if (!$session) {
            return response()->json([
                "Error"   => config('enums.mancalaCodes')['InternalServiceError'],
                "Msg" => "No user with such session_id"
            ]);
        }

        $user = $session->user;

        return response()->json([
            "Error"   => config('enums.mancalaCodes')['NoErrors'],
            "Balance" => number_format($user->balance, 2, ".", "")
        ]);
    }

    public function Credit(Request $request)
    {
        /*       
        "SessionId": "c45717dc-2872-4157-9624-e0d671837668",
        "RoundGuid": "d530e1e9-f333-4346-8193-2f92569ffcbf",
        "TransactionGuid": "89a1645d-e47f-41fd-a33f-ef0b9955b914",
        "LinesCount": 5,
        "Amount": 2.50,
        "Hash": "d5945e1c31e20012131c7476c67c1ec9",
        "BonusTransaction": true,
        "ExtraData": "data",
        "ExternalBonusId": "YbGLQN5cDwBgGqn"
        }
        */
        $data = $request->all();

        $sessionID = $data['SessionId'];
        $transactionID = $data['TransactionGuid'];
        $roundID = $data['RoundGuid'];
        $amount =  $data['Amount'];
        $hash = $data['Hash'];

        $client = app()->make(Client::class);
        $true_hash = $client->genHash([
            '/Credit',
            $sessionID,
            $transactionID,
            $roundID,
            $amount,
            $client->key
        ]);

        if ($hash !== $true_hash) {
            return response()->json([
                "Error"   => config('enums.mancalaCodes')['HashMismatch'],
                "Msg"     => "Invalid hash"
            ]);
        }

        $session = GameSession::where("session_id", "=", $sessionID)->first();

        if (!$session) {
            return response()->json([
                "Error"   => config('enums.mancalaCodes')['InternalServiceError'],
                "Msg"     => "No user with such session_id"
            ]);
        }

        $user = $session->user;
        $amount = floatval($amount);
        
        if ($user->balance - $amount < 0) {
            return response()->json([
                "Error"   => config('enums.mancalaCodes')['InternalServiceError'],
                "Msg"     => "Insufficent Balance"
            ]);
        }

        Transaction::create([
            'amount'          => $amount,
            'currency'        => $user->currency,
            'user_id'         => $user->id,
            'reference'       => $transactionID,
            'game_session_id' => $session->id,
            'game_round_id'   => $roundID,
            'game_id'         => NULL,
            'type'            => config('enums.transaction_types')['deposit'],
            'completed'       => true
        ]);

        $user->balance -= $amount;
        $user->save();

        return response()->json([
            "Error"   => config('enums.mancalaCodes')['NoErrors'],
            "Balance" => number_format($user->balance, 2, ".", "")
        ]);
    }

    public function Debit(Request $request)
    {
        /*       
        "SessionId": "c45717dc-2872-4157-9624-e0d671837668",
        "RoundGuid": "d530e1e9-f333-4346-8193-2f92569ffcbf",
        "TransactionGuid": "fc8e0e71-fe25-4e86-995b-5c87fa482fcd",
        "Amount": 30.00,
        "DebitType": 2,
        "Hash": "d3ba9e67a78822adc40bf104ead1722c",
        "BonusTransaction": true,
        "ExtraData": "data",
        "ExternalBonusId": "YbGLQN5cDwBgGqn"
        }
        */
        $data = $request->all();

        $sessionID = $data['SessionId'];
        $transactionID = $data['TransactionGuid'];
        $roundID = $data['RoundGuid'];
        $amount =  $data['Amount'];
        $hash = $data['Hash'];

        $client = app()->make(Client::class);
        $true_hash = $client->genHash([
            '/Debit',
            $sessionID,
            $transactionID,
            $roundID,
            $amount,
            $client->key
        ]);

        if ($hash !== $true_hash) {
            return response()->json([
                "Error"   => config('enums.mancalaCodes')['HashMismatch'],
                "Msg"     => "Invalid hash"
            ]);
        }

        $session = GameSession::where("session_id", "=", $sessionID)->first();

        if (!$session) {
            return response()->json([
                "Error"   => config('enums.mancalaCodes')['InternalServiceError'],
                "Msg"     => "No user with such session_id"
            ]);
        }

        $user = $session->user;
        $amount = floatval($amount);

        Transaction::create([
            'amount'          => $amount,
            'currency'        => $user->currency,
            'user_id'         => $user->id,
            'reference'       => $transactionID,
            'game_session_id' => $session->id,
            'game_round_id'   => $roundID,
            'game_id'         => NULL,
            'type'            => config('enums.transaction_types')['withdraw'],
            'completed'       => true
        ]);

        $user->balance += $amount;
        $user->save();

        return response()->json([
            "Error"   => config('enums.mancalaCodes')['NoErrors'],
            "Balance" => number_format($user->balance, 2, ".", "")
        ]);
    }

    public function Refund(Request $request)
    {
        /*       
        "SessionId": "c45717dc-2872-4157-9624-e0d671837668",
        "RoundGuid": "d530e1e9-f333-4346-8193-2f92569ffcbf",
        "TransactionGuid": "89a1645d-e47f-41fd-a33f-ef0b9955b914",
        "RefundTransactionGuid": "0bc212c4-b6b2-41c0-a4ae-761b84ef6eaa",
        "Amount": 2.50,
        "Hash": "a372aeb618cbaa71ad0b819e67b5aef6",
        "ExtraData": "data"
        */
        $data = $request->all();

        $sessionID = $data['SessionId'];
        $transactionID = $data['TransactionGuid'];
        $refundTransactionID = $data['RefundTransactionGuid'];
        $roundID = $data['RoundGuid'];
        $amount =  $data['Amount'];
        $hash = $data['Hash'];

        $client = app()->make(Client::class);
        $true_hash = $client->genHash([
            '/Refund',
            $sessionID,
            $transactionID,
            $refundTransactionID,
            $roundID,
            $amount,
            $client->key
        ]);

        if ($hash !== $true_hash) {
            return response()->json([
                "Error"   => config('enums.mancalaCodes')['HashMismatch'],
                "Msg"     => "Invalid hash"
            ]);
        }

        $session = GameSession::where("session_id", "=", $sessionID)->first();

        if (!$session) {
            return response()->json([
                "Error"   => config('enums.mancalaCodes')['InternalServiceError'],
                "Msg"     => "No user with such session_id"
            ]);
        }

        $user = $session->user;

        $transaction = Transaction::where('reference', '=', $refundTransactionID)->first();
        if (!$transaction) {
            return response()->json([
                'Error'  => config('enums.mancalaCodes')['InternalServiceError'],
                'Msg'    => 'No such transaction'
            ]);
        }

        if (!$transaction->completed) {
            return response()->json([
                'Error' => config('enums.mancalaCodes')['InternalServiceError'],
                'Msg'   => 'Transaction already rollbacked'
            ]);
        }

        $amount = floatval($transaction->amount);
        $transaction->completed = false;
        $transaction->save();

        if (config('enums.transaction_types')['deposit'] === $transaction->type) {
            $amount *= -1;
        }
        $user->balance += $amount;
        $user->save();

        return response()->json([
            "Error"   => config('enums.mancalaCodes')['NoErrors'],
            "Balance" => number_format($user->balance, 2, ".", "")
        ]);
    }
}
