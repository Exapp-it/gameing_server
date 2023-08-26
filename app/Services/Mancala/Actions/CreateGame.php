<?php

namespace App\Services\Mancala\Actions;

use App\Services\Mancala\DTO\Game;

class CreateGame {
    public static function handle(array $item): Game
    {
        return new Game($item);
    }
}