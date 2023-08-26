<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Game;
use App\Http\Resources\GameResource;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $games = Game::all();
        $filtered = [];
        foreach ($games as $idx => $game) {
            if (!str_contains($game->name, "92RTP")) {
                $filtered[] = $game;
            }
        }
        return [
            'status' => 'success',
            'data'   => GameResource::collection($filtered)
        ];
    }
}
