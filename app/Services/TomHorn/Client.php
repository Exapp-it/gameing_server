<?php

namespace App\Services\TomHorn;

use Illuminate\Support\Facades\Http;
use App\Services\TomHorn\Actions\CreateGame;


class Client {
    private $integration_service;
    private $report_service;
    private $partnerID;
    private $key;

    public function __construct(
        string $integration_service,
        string $report_service,
        string $partnerID,
        string $key
    )
    {
        $this->integration_service = $integration_service;
        $this->report_service = $report_service;
        $this->partnerID = $partnerID;
        $this->key = $key;
    }

    public function genSign($params)
    {
        $data = $this->partnerID;
        foreach ($params as $param) {
            $data .= $param;
        }
        return strtoupper(hash_hmac('sha256', pack('A*', $data), pack('A*', $this->key)));
    }

    public function checkPartnerID($partnerID)
    {
        return $this->partnerID === $partnerID;
    }

    public function getGameModules()
    {
        $data = json_encode([
            "partnerID" => $this->partnerID,
            "sign"      => $this->genSign([]),
            "channel"   => "",
        ]);

        $url = $this->integration_service.'GetGameModules';
        $response = Http::withBody($data, 'application/json')
                        ->post($url);

        if (!$response->successful()) {
            return $response->toException();
        }

        $games = [];

        foreach ($response["GameModules"] as $game) {
            array_push(
                $games,
                CreateGame::handle($game)
            );
        }

        return $games;
    }

    public function getModuleInfo($sessionID, $module)
    {
        $data = json_encode([
            "partnerID" => $this->partnerID,
            "sign"      => $this->genSign([$sessionID, $module]),
            "sessionID" => $sessionID,
            "module"    => $module,
        ]);

        $url = $this->integration_service.'GetModuleInfo';
        $response = Http::withBody($data, 'application/json')
                        ->post($url);

        if (!$response->successful()) {
            return $response->toException();
        }

        return $response["Parameters"];
    }

    public function getPlayMoneyModuleInfo($module, $currency)
    {
        $data = json_encode([
            "partnerID" => $this->partnerID,
            "sign"      => $this->genSign([$module, $currency]),
            "module"    => $module,
            'currency'  => $currency,
        ]);

        $url = $this->integration_service.'GetPlayMoneyModuleInfo';
        $response = Http::withBody($data, 'application/json')
                        ->post($url);

        if (!$response->successful()) {
            return $response->toException();
        }

        return $response["Parameters"];
    }

    public function createIdentity($name, $currency)
    {
        // Add prove that player has identity
        $data = json_encode([
            "partnerID" => $this->partnerID,
            "sign"      => $this->genSign([$name, $currency]),
            "name"      => $name,
            "currency"  => $currency,
        ]);

        $url = $this->integration_service.'CreateIdentity';
        $response = Http::withBody($data, 'application/json')
                        ->post($url);

        if (!$response->successful()) {
            return $response->toException();
        }

        return $response['Code'] === 0;
    }

    public function getIdentity($name)
    {
        // Add prove that player has identity
        $data = json_encode([
            "partnerID" => $this->partnerID,
            "sign"      => $this->genSign([$name]),
            "name"      => $name,
        ]);

        $url = $this->integration_service.'GetIdentity';
        $response = Http::withBody($data, 'application/json')
                        ->post($url);

        if (!$response->successful()) {
            return $response->toException();
        }

        return $response['Code'] === 0;
    }

    public function createSession($name)
    {
        // Add prove that player is not in opened session
        $sign = hash_hmac(
            'sha256',
            $this->partnerID.$name,
            $this->key
        );
        $data = json_encode([
            "partnerID" => $this->partnerID,
            "sign"      => $this->genSign([$name]),
            "name"      => $name
        ]);

        $url = $this->integration_service.'CreateSession';
        $response = Http::withBody($data, 'application/json')
                        ->post($url);

        if (!$response->successful()) {
            return $response->toException();
        }

        if ($response['Code'] === 0) {
            return $response['Session']["ID"];
        }
        return 0;
    }

    public function closeSession($sessionID)
    {
        $data = json_encode([
            "partnerID" => $this->partnerID,
            "sign"      => $this->genSign([$sessionID]),
            "sessionID" => $sessionID
        ]);

        $url = $this->integration_service.'CloseSession';
        $response = Http::withBody($data, 'application/json')
                        ->post($url);

        if (!$response->successful()) {
            return $response->toException();
        }

        if ($response['Code'] === 0) {
            return $response['Session']["End"];
        }
        return "";
    }
}