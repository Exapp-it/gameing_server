<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StrikeResource extends JsonResource
{
    public static $wrap = 'strikes';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "date" => $this->date
        ];
    }
}
