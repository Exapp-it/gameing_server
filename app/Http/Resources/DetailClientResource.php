<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\WithdrawResource;

class DetailClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'login'       => $this->login,
            'created_at'  => $this->created_at,
            'first_name'  => $this->first_name,
            'last_name'   => $this->last_name,
            'patronymic'  => $this->patronymic,
            'birth_date'  => $this->birth_date,
            'email'       => $this->email,
            'fingerprint' => $this->fingerprint,
            'balance'     => $this->balance,
            'payments'    => PaymentResource::collection($this->payments),
            'withdraws'   => WithdrawResource::collection($this->withdraws),
        ];
    }

    public function with($request)
    {
        return [
            "status" => "success"
        ];
    }
}
