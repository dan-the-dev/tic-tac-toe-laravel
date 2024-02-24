<?php

namespace App\Services\Commands\NewGame;

use App\Models\Game;

class DefaultNewGameCommandHandler implements NewGameCommandHandler
{

    public function handle(): NewGameResult
    {
        $game = new Game();
        $game->saveOrFail();

        return new NewGameResult($game->id);
    }
}
