<?php

namespace App\Services\B2bslots\DTO;


use App\Models\Game as GameModel;
use Spatie\DataTransferObject\DataTransferObject;

class Game extends DataTransferObject
{
    public int $id;
    public string $provider;
    public string $title;
    public array $images;

    public function saveToDB()
    {
        $imgFullPath = config('services.b2bslots.url'). "game/icons/" . $this->images[0]->ic_name;

        GameModel::updateOrCreate(
            [
                "name"     => $this->title
            ],
            [
                "provider" => $this->provider,
                "category" => "B2bslots",
                "type"     => config('enums.game_types')['b2bslots'],
                "info"     => strval($this->id),
                "image"    => $imgFullPath,
            ]
        );
    }
}
