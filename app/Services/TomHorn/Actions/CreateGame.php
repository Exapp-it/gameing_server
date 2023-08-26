<?php

namespace App\Services\TomHorn\Actions;

use App\Services\TomHorn\DTO\Game;

class CreateGame {
    public static function handle(array $item): Game
    {
        return new Game($item);
    }
}