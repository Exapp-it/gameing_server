<?php

namespace App\Http\Controllers\B2bslots;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class testController extends Controller
{
  // public function test()
  // {



  //     $user_id = 1;
  //     $auth_token = "asldgfoausdfa6sd5f46asd8";

  //     $params = [
  //         'api' => 'do-auth-user-ingame',
  //         'data' => [
  //             'operator_id' => 40065,
  //             'user_id' => $user_id,
  //             // 'user_ip' => '1111',
  //             'user_auth_token' => $auth_token,
  //             'currency' => 'RUB',
  //             'language' => 'EN',
  //             'home_url' => url()->previous(),
  //         ],
  //     ];


  //     $base_url = 'https://int.apiforb2b.com/';
  //     $path = 'gamesbycode/1017.gamecode?operator_id=0&user_id=1&auth_token=asldgfoausdfa6sd5f46asd8&currency=RUB[&language=EN][&home_url=http://127.0.0.1:8000/api/b2bslots_service]';
  //     $url = $base_url . $path;
  //     $response = Http::get($url);

  //   return $response->headers();
  // }

  public function test(Request $request)
  {
    $client = app()->make(\App\Services\B2bslots\Client::class);
    
    $response = $client->auth($request->user(), $request->all());



    return response()->json($response);
  }
}


// "https://int.apiforb2b.com/gamesbycode/1017.gamecode?operator_id=40065&user_id=1&auth_token=asldgfoausdfa6sd5f46asd8&currency=RUB[&language=EN][&home_url=http://127.0.0.1:8000/api/b2bslots_service]";