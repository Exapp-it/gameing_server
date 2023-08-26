<?php

namespace App\Services\Mancala\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class Game extends DataTransferObject
{
    public int $Id;
    public string $Title;
    public array $Images;
    public string $Dt;

    public function saveToDB()
    {
        \App\Models\Game::updateOrCreate([
            "name"     => $this->Title
        ],
        [
            "provider" => "Mancala",
            "category" => "Mancala",
            "type"     => config('enums.game_types')['mancala'],
            "info"     => strval($this->Id),
            "image"    => $this->Images[0],
        ]);
    }
}