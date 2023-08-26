<?php

namespace App\Services\Mancala;

use Illuminate\Support\Facades\Http;

use App\Services\Mancala\Actions\CreateGame;

class Client {
    protected $uri;
    protected $guid;
    public $key;
    
    public function __construct(
        string $uri,
        string $guid,
        string $key
    ) {
        $this->uri = $uri."partnersV2/";
        $this->guid = $guid;
        $this->key = $key;
    }

    public function genHash($params)
    {
        $data = "";
        foreach ($params as $param) {
            $data .= $param;
        }
        return md5($data);
    }

    public function GetAvailableGames()
    {
        $path = "GetAvailableGames/";
        $data = json_encode([
            "ClientGuid" => $this->guid,
            "Hash"       => $this->genHash([$path, $this->guid, $this->key]),
        ]);

        $url = $this->uri.$path;
        $response = Http::withBody($data, 'application/json')
                        ->post($url);

        if (!$response->successful()) {
            return $response->toException();
        }

        $games = [];

        foreach ($response["Games"] as $game) {
            array_push(
                $games,
                CreateGame::handle($game)
            );
        }

        return $games;
    }

    public function GetToken(
        int $gameID,
        string $userID,
        string $currency,
        bool $isDemo
    ) {
        $path = "GetToken/";
        
        $data = [
            "ClientGuid" => $this->guid,
            "GameId"     => $gameID,
            "Currency"   => $currency,
            "Lang"       => "RU",
            "IsVirtual"  => false,
            "ApiVersion" => "v2",
            "DemoMode"   => $isDemo,
            "ExtraData"  => ""
        ];

        if (!$isDemo) {
            $data['Hash'] = $this->genHash([
                $path,
                $this->guid,
                $gameID,
                $userID,
                $currency,
                $this->key
            ]);
            $data["UserId"] = $userID;
        } else {
            $data['Hash'] = $this->genHash([
                $path,
                $this->guid,
                $gameID,
                $this->key
            ]);
        }
        $data = json_encode($data);

        $url = $this->uri.$path;
        $response = Http::withBody($data, 'application/json')
                        ->post($url);

        if (!$response->successful()) {
            return $response->toException();
        }

        return [
            "url"   => $response['IframeUrl'],
            'token' => $response['Token'],
            'type'  => 'Mancala'
        ];
    }
}
