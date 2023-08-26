<?php

namespace App\Services\B2bslots\Actions;

use App\Services\B2bslots\DTO\Game;

class CreateGame
{
    public static function handle(array $item): Game
    {
        return new Game($item);
    }
}
