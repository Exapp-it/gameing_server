<?php

namespace App\Http\Controllers\Mancala;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Mancala\Client;

class TestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $client = app()->make(Client::class);
        return $client->GetAvailableGames();
    }
}
