<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GameSession;
use App\Services\TomHorn\Client;

class CloseSessionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $game_session = GameSession
            ::whereNull('end')
            ->where(
                'user_id', '=', $request->user()->id
            )
            ->first();
        
        if (!$game_session) {
            return response()->json([
                'status'  => 'error',
                'message' => "User doesn't have any open sessions"
            ]);
        }

        $sessionID = intval($game_session->session_id);
        $client = app()->make(Client::class);
        $end = $client->closeSession($sessionID);
        if (empty($end)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Error occured while trying to close session"
            ]);
        }

        $game_session->end = $end;
        $game_session->save();

        return response()->json([
            'status'  => 'success',
            'message' => "Successfully closed session"
        ]);
    }
}
