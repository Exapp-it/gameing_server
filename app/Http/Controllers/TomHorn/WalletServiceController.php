<?php

namespace App\Http\Controllers\TomHorn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\TomHorn\Client;
use App\Services\Helper;
use App\Models\User;
use App\Models\Game;
use App\Models\GameSession;
use App\Models\Transaction;

class WalletServiceController extends Controller
{
    public function GetBalance(Request $request)
    {
        $data = $request->all();
        $partnerID = $data['partnerID'];
        $sign = $data['sign'];

        $client = app()->make(Client::class);
        if (!$client->checkPartnerID($partnerID)) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['partner_err'],
                'Message' => 'Invalid partnerID'
            ]);
        }

        $true_sign = $client->genSign([
            $data['name'],
            $data['currency'],
            $data['sessionID'],
            $data['gameModule'],
            $data['type']
        ]);

        if ($sign !== $true_sign) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['sign_err'],
                'Message' => 'Invalid sign'
            ]);
        }

        $user = User::where('identity', '=', $data['name'])->first();
        if (!$user) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['identity_err'],
                'Message' => 'Invalid identity'
            ]);
        }

        $sessionID = intval($data['sessionID']);
        $game_session = GameSession
            ::whereNull('end')
            ->where(
                'user_id', '=', $user->id
            )
            ->where(
                'type', '=', config('enums.game_types')['tomhorn']
            )
            ->first();
        
        if (!$game_session || intval($game_session->session_id) !== $sessionID) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['params_err'],
                'Message' => 'Invalid params: sessionID'
            ]);
        }

        return response()->json([
            'Code' => config('enums.tomHornCodes')['ok'],
            'Message' => '',
            'Balance' => [
                'Amount'   => number_format($user->balance, 2, ".", ""),
                'Currency' => $user->currency
            ]
        ]);
    }

    public function Withdraw(Request $request)
    {
        $data = $request->all();
        $partnerID = $data['partnerID'];
        $sign = $data['sign'];

        $client = app()->make(Client::class);
        if (!$client->checkPartnerID($partnerID)) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['partner_err'],
                'Message' => 'Invalid partnerID'
            ]);
        }

        if (!is_numeric($data['amount'])) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['params_err'],
                'Message' => 'Invalid params: amount'
            ]);
        }

        $amount = floatval($data['amount']);
        if ($amount < 0) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['params_err'],
                'Message' => 'Invalid params: amount'
            ]);
        }
        $data['amount'] = number_format($amount, 2, ".", "");

        $true_sign = $client->genSign([
            $data['name'],
            $data['amount'],
            $data['currency'],
            $data['reference'],
            $data['sessionID'],
            $data['gameRoundID'],
            $data['gameModule'],
            $data['fgbCampaignCode']
        ]);

        if ($sign !== $true_sign) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['sign_err'],
                'Message' => 'Invalid sign'
            ]);
        }

        $user = User::where('identity', '=', $data['name'])->first();
        if (!$user) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['identity_err'],
                'Message' => 'Invalid identity'
            ]);
        }

        $amount = Helper::convertToCurrency(
            $data['currency'],
            $user->currency,
            $amount
        );

        if ($user->balance - $amount < 0.001) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['funds_err'],
                'Message' => 'Funds error'
            ]);
        }

        $sessionID = intval($data['sessionID']);
        $game_session = GameSession
            ::whereNull('end')
            ->where(
                'user_id', '=', $user->id
            )
            ->where(
                'type', '=', config('enums.game_types')['tomhorn']
            )
            ->first();
        
        if (!$game_session || intval($game_session->session_id) !== $sessionID) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['params_err'],
                'Message' => 'Invalid params: sessionID'
            ]);
        }

        $gameModule = $data['gameModule'];
        $game = Game::where('info', '=', $gameModule)->first();
        if (!$game) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['params_err'],
                'Message' => 'Invalid params: gameModule'
            ]);
        }

        $transaction = Transaction::where('reference', '=', $data['reference'])->first();
        if ($transaction) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['reference_err'],
                'Message' => 'Reference duplicate'
            ]);
        }
        $transaction = Transaction::create([
            'amount'          => $amount,
            'currency'        => $data['currency'],
            'user_id'         => $user->id,
            'reference'       => $data['reference'],
            'game_session_id' => $game_session->id,
            'game_round_id'   => $data['gameRoundID'],
            'game_id'         => $game->id,
            'type'            => config('enums.transaction_types')['withdraw'],
            'completed'       => true
        ]);
        $user->balance -= $amount;
        $user->save();

        return response()->json([
            'Code'        => config('enums.tomHornCodes')['ok'],
            'Message'     => '',
            'Transaction' => [
                'Balance'  => $user->balance,
                'Currency' => $user->currency,
                'ID'       => $transaction->id
            ]
        ]);
    }

    public function Deposit(Request $request)
    {
        $data = $request->all();
        $partnerID = $data['partnerID'];
        $sign = $data['sign'];

        $client = app()->make(Client::class);
        if (!$client->checkPartnerID($partnerID)) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['partner_err'],
                'Message' => 'Invalid partnerID'
            ]);
        }

        if (!is_numeric($data['amount'])) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['params_err'],
                'Message' => 'Invalid params: amount'
            ]);
        }

        $amount = floatval($data['amount']);
        if ($amount < 0) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['params_err'],
                'Message' => 'Invalid params: amount'
            ]);
        }
        $data['amount'] = number_format($amount, 2, ".", "");

        $true_sign = $client->genSign([
            $data['name'],
            $data['amount'],
            $data['currency'],
            $data['reference'],
            $data['sessionID'],
            $data['gameRoundID'],
            $data['gameModule'],
            $data['type'],
            $data['fgbCampaignCode'],
            $data['isRoundEnd']
        ]);

        if ($sign !== $true_sign) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['sign_err'],
                'Message' => 'Invalid sign'
            ]);
        }

        $user = User::where('identity', '=', $data['name'])->first();
        if (!$user) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['identity_err'],
                'Message' => 'Invalid identity'
            ]);
        }

        $amount = Helper::convertToCurrency(
            $data['currency'],
            $user->currency,
            $amount
        );

        $gameModule = $data['gameModule'];
        $game = Game::where('info', '=', $gameModule)->first();
        if (!$game) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['params_err'],
                'Message' => 'Invalid params: gameModule'
            ]);
        }
        
        /*
        add entity round and register it's end
        if ($data['isRoundEnd'] === 'True') {
            
        }
        */

        $transaction = Transaction::where('reference', '=', $data['reference'])->first();
        if ($transaction) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['reference_err'],
                'Message' => 'Reference duplicate'
            ]);
        }

        $game_session = GameSession::where('session_id', '=', $data['sessionID'])->first();
        $sessionID = NULL;
        if ($game_session) {
            $sessionID = $game_session->id;
        }

        $transaction = Transaction::create([
            'amount'          => $amount,
            'currency'        => $data['currency'],
            'user_id'         => $user->id,
            'reference'       => $data['reference'],
            'game_session_id' => $sessionID,
            'game_round_id'   => $data['gameRoundID'],
            'game_id'         => $game->id,
            'type'            => config('enums.transaction_types')['deposit'],
            'completed'       => true
        ]);

        $user->balance -= $amount;
        $user->save();

        return response()->json([
            'Code'        => config('enums.tomHornCodes')['ok'],
            'Message'     => '',
            'Transaction' => [
                'Balance'  => $user->balance,
                'Currency' => $user->currency,
                'ID'       => $transaction->id
            ]
        ]);
    }

    public function RollbackTransaction(Request $request)
    {
        $data = $request->all();
        $partnerID = $data['partnerID'];
        $sign = $data['sign'];

        $client = app()->make(Client::class);
        if (!$client->checkPartnerID($partnerID)) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['partner_err'],
                'Message' => 'Invalid partnerID'
            ]);
        }

        $true_sign = $client->genSign([
            $data['name'],
            $data['reference'],
            $data['sessionID']
        ]);

        if ($sign !== $true_sign) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['sign_err'],
                'Message' => 'Invalid sign'
            ]);
        }

        $user = User::where('identity', '=', $data['name'])->first();
        if (!$user) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['identity_err'],
                'Message' => 'Invalid identity'
            ]);
        }

        $transaction = Transaction::where('reference', '=', $data['reference'])->first();
        if (!$transaction) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['transaction_err'],
                'Message' => 'No such transaction'
            ]);
        }
        if (!$transaction->completed) {
            return response()->json([
                'Code'    => config('enums.tomHornCodes')['rollback_err'],
                'Message' => 'Transaction already rollbacked'
            ]);
        }

        $transaction->completed = false;
        $transaction->save();

        $amount = Helper::convertToCurrency(
            $transaction->currency,
            $user->currency,
            floatval($transaction->amount)
        );

        if (config('enums.transaction_types')['deposit'] === $transaction->type) {
            $amount *= -1;
        }
        $user->balance += $amount;
        $user->save();

        return response()->json([
            'Code'    => config('enums.tomHornCodes')['ok'],
            'Message' => ''
        ]);
    }
}
