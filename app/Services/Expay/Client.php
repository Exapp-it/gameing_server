<?php

namespace App\Services\Expay;

use Illuminate\Support\Facades\Http;

class Client {
    protected $uri;
    protected $public;
    protected $private;
    
    public function __construct(
        string $uri,
        string $public,
        string $private
    ) {
        $this->uri = $uri;
        $this->public = $public;
        $this->private = $private;
        $this->get_key = hash_hmac(
            'sha512',
            $public,
            $private
        );
    }

    public function balance()
    {
        $request = Http::withHeaders([
            'ApiKey' => $this->public,
            'SecretKey' => $this->get_key
        ])->acceptJson();

        $response = $request->get($this->uri.'balance');
        
        if (!$response->successful()) {
            return $response->toException();
        }

        return $response;
    }

    public function appeals()
    {
        $request = Http::withHeaders([
            'ApiKey' => $this->public,
            'SecretKey' => $this->get_key
        ])->acceptJson();

        $response = $request->get($this->uri.'appeals');
        
        if (!$response->successful()) {
            return $response->toException();
        }

        return $response;
    }

    public function income_refer($data)
    {
        $data = json_encode($data);
        $secret = hash_hmac(
            'sha512',
            $data,
            $this->private
        );

        $response = Http::withHeaders([
            'ApiKey'       => $this->public,
            'SecretKey'    => $secret
        ])->withBody($data, 'application/json')
          ->post($this->uri.'create_refer_in');

        if (!$response->successful()) {
            return $response->toException();
        }

        if (!$response['success']) {
            return false;
        }

        return [
            'internal_id' => $response['result']['internal_id'],
            'form_link'   => $response['result']['form_link']
        ];
    }

    public function outcome_refer($data)
    {
        $data = json_encode($data);
        $secret = hash_hmac(
            'sha512',
            $data,
            $this->private
        );

        $response = Http::withHeaders([
            'ApiKey'       => $this->public,
            'SecretKey'    => $secret
        ])->withBody($data, 'application/json')
          ->post($this->uri.'create_refer_out');

        if (!$response->successful()) {
            return $response->toException();
        }

        if (!$response['success']) {
            return false;
        }

        return $response['internal_id'];
    }

    public function wallet_withdraw($data)
    {
        $data = json_encode($data);
        $secret = hash_hmac(
            'sha512',
            $data,
            $this->private
        );

        $response = Http::withHeaders([
            'ApiKey'       => $this->public,
            'SecretKey'    => $secret
        ])->withBody($data, 'application/json')
          ->post($this->uri.'withdraw');

        if (!$response->successful()) {
            return $response->toException();
        }

        if (!$response['success']) {
            return false;
        }

        return $response['result']['internal_id'];
    }

    public function wallet_invoice($data)
    {
        $data = json_encode($data);
        $secret = hash_hmac(
            'sha512',
            $data,
            $this->private
        );

        $response = Http::withHeaders([
            'ApiKey'       => $this->public,
            'SecretKey'    => $secret
        ])->withBody($data, 'application/json')
          ->post($this->uri.'invoice');

        if (!$response->successful()) {
            return $response->toException();
        }

        if (!$response['success']) {
            return false;
        }

        return $response['result']['internal_id'];
    }

    public function get_status($internal_id)
    {
        $request = Http::withHeaders([
            'ApiKey' => $this->public,
            'SecretKey' => $this->get_key
        ])->acceptJson();

        $response = $request->get($this->uri.'transaction/'.$internal_id);
        
        if (!$response->successful()) {
            return $response->toException();
        }

        $status_id = $response['status_id'];
        return config('enums.transaction_status')[$status_id];
    }
}