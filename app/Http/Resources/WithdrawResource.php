<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawResource extends JsonResource
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
            'id'           => $this->id,
            'amount'       => $this->amount,
            'status'       => $this->status,
            'fiat_address' => $this->fiat_address,
            'created_at'   => $this->created_at,
            'confirmed'    => $this->confirmed
        ];
    }
}
