<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

use App\Models\Game;
use App\Services\TomHorn\Helper;

use App\Jobs\StrikeJob;

use App\Services\B2bslots\Client as B2bslotsClient;
use App\Services\TomHorn\Client as TomHornClient;
use App\Services\Mancala\Client as MancalaClient;

class DemoGameController extends Controller
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
            'id' => 'required|integer|exists:games,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $validator->errors()->toJson()
                ], 400
            );
        }

        $game = Game::find($validator->validated()["id"]);
        $user = $request->user();

        if ($game->type === config('enums.game_types')['tomhorn']) {
            return $this->tomhorn_demo($user, $game);
        } else if ($game->type === config('enums.game_types')['mancala']) {
            return $this->mancala_demo($user, $game);
        } else if ($game->type === config('enums.game_types')['b2bslots']) {
            return $this->b2bslotsStart($user, $game, $request->all());
        }

        return response()->json(
            [
                'status' => 'error',
                'error' => 'Unknown game'
            ], 500
        );
    }

    public function tomhorn_demo($user, $game)
    {
        $client = app()->make(TomHornClient::class);
        $game_info = $client->getPlayMoneyModuleInfo($game->info, "USD");

        if (!$game_info) {
            return response()->json([
                'status'  => 'error',
                'message' => "Error occured while trying to create game page, try again later"
            ]);
        }
        
        if ($user) {
            dispatch(new StrikeJob([
                'user' => $user
            ]));
        }

        return response()->json([
            'status'  => 'success',
            'type'    => 'TomHorn',
            //'html' => Helper::preparePage($game_info)
            'params'  => $game_info
        ]);
    }

    public function mancala_demo($user, $game)
    {
        $client = app()->make(MancalaClient::class);
        $res = $client->GetToken(
            intval($game->info),
            "",
            $user ? $user->currency : "RUB",
            true
        );
        $res['status'] = 'success';

        if ($user) {
            dispatch(new StrikeJob([
                'user' => $user
            ]));
        }

        return response()->json($res);
    }

    public function b2bslotsStart($user, $game, $requestData)
    {
        $data = toArray($requestData['data']);
        $client = App::make(B2bslotsClient::class);

        try {
            // $client->validationData($user, $game, $data);
            // GameSession::create([
            //     'user_id'    => $user->id,
            //     'type'       => config('enums.game_types')['b2bslots'],
            //     'session_id' => $client->generateToken([$user->identity, $game->info]),
            // ]);
            return response()->json($client->authResponse($user, true));
        } catch (\InvalidArgumentException $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ],
                400
            );
        }
    }
}
