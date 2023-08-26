<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommonClientResource extends JsonResource
{
    public static $wrap = 'clients';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"         => $this->id,
            "login"      => $this->login,
            "first_name" => $this->first_name,
            "last_name"  => $this->last_name,
            "patronymic" => $this->patronymic,
            "gender"     => $this->gender,
            "email"      => $this->email,
            "phone"      => $this->phone,
            "country"    => $this->country,
            "city"       => $this->city,
            "currency"   => $this->currency,
            "bonus"      => $this->bonus,
            "balance"    => $this->balance,
        ];
    }
}
