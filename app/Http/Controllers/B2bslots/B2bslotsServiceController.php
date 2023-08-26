<?php

namespace App\Http\Controllers\B2bslots;

use App\Http\Controllers\Controller;
use App\Models\GameSession;
use App\Services\B2bslots\Client;
use Illuminate\Http\Request;

class B2bslotsServiceController extends Controller
{
    protected $service;

    public function __construct(Client $client)
    {
        $this->service = $client;
    }

    public function debit(Request $request)
    {
        $data = toArray($request->all()['data']);
        $gameSessionToken = $data['user_game_token'];
        $transactionHash = $data['transaction_id'];
        $gameCode = $data['game_code'];
        $amount = floatval($data['debit_amount']);
        $roundId = $data['round_id'];

        $session = $this->service->getToken($gameSessionToken);

        if (!$session) {
            return $this->service->respondWithError(
                config('enums.b2bslotsCodes.TokenNotFound'),
                "No user with such token"
            );
        }

        $user = $session->user;

        $validationResult = $this->service->validateBalance($user, $amount);
        if ($validationResult !== true) {
            return $validationResult;
        }

        try {
            $this->service->createTransactionAndUpdateBalance(
                $user,
                $session,
                $amount,
                $transactionHash,
                $roundId,
                getCurrentMethodName(),
                $gameCode,
            );
        } catch (\Exception $e) {
            return $this->service->respondWithError(
                config('enums.b2bslotsCodes.TokenNotFound'),
                'Transaction expired'
            );
        }

        return $this->service->debitResponse($user, $data);
    }

    public function credit(Request $request)
    {
        $data = toArray($request->all()['data']);
        $gameSessionToken = $data['user_game_token'];
        $transactionHash = $data['transaction_id'];
        $gameCode = $data['game_code'];
        $amount = floatval($data['credit_amount']);
        $roundId = $data['round_id'];

        $session = $this->service->getToken($gameSessionToken);

        if (!$session) {
            return $this->service->respondWithError(
                config('enums.b2bslotsCodes.TokenNotFound'),
                "No user with such token"
            );
        }

        $user = $session->user;

        try {
            $this->service->createTransactionAndUpdateBalance(
                $user,
                $session,
                $amount,
                $transactionHash,
                $roundId,
                getCurrentMethodName(),
                $gameCode,
            );
        } catch (\Exception $e) {
            return $this->service->respondWithError(
                config('enums.b2bslotsCodes.TransactionExpired'),
                'Transaction expired'
            );
        }

        return $this->service->creditResponse($user, $data);
    }

    public function getFeatures(Request $request)
    {
        $data = toArray($request->all()['data']);
        $gameSessionToken = $data['user_game_token'];


        $session = $this->service->getToken($gameSessionToken);

        if (!$session) {
            return $this->service->respondWithError(
                config('enums.b2bslotsCodes.TokenNotFound'),
                "No user with such token"
            );
        }

        $user = $session->user;

        return $this->service->getFeatureResponse($user, $data);
    }

    public function activateFeatures(Request $request)
    {
        $data = toArray($request->all()['data']);
        $gameSessionToken = $data['user_game_token'];

        $session = $this->service->getToken($gameSessionToken);
        if (!$session) {
            return $this->service->respondWithError(
                config('enums.b2bslotsCodes.TokenNotFound'),
                "No user with such token"
            );
        }

        $user = $session->user;

        $this->service->activateBonus($user, $data);

        return $this->service->activateFeatureResponse($user, $data);
    }

    public function updateFeatures(Request $request)
    {
        $data = toArray($request->all()['data']);
        $gameSessionToken = $data['user_game_token'];

        $session = $this->service->getToken($gameSessionToken);
        if (!$session) {
            return $this->service->respondWithError(
                config('enums.b2bslotsCodes.TokenNotFound'),
                "No user with such token"
            );
        }

        $user = $session->user;

        $this->service->updateBonus($user, $data);

        return $this->service->updateFeatureResponse($user, $data);
    }

    public function endFeatures(Request $request)
    {
        $data = toArray($request->all()['data']);
        $gameSessionToken = $data['user_game_token'];

        $session = $this->service->getToken($gameSessionToken);
        if (!$session) {
            return $this->service->respondWithError(
                config('enums.b2bslotsCodes.TokenNotFound'),
                "No user with such token"
            );
        }

        $user = $session->user;

        $this->service->endBonus($user, $data);

        return $this->service->endFeatureResponse($user, $data);
    }
}
