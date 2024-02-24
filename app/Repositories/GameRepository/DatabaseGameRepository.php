<?php

namespace App\Repositories\GameRepository;

use App\Models\Game;

class DatabaseGameRepository implements GameRepository
{

    public function create(): int
    {
        $game = new Game();
        $game->saveOrFail();

        return $game->id;
    }
}
