<?php

namespace App\Services\Commands\NewGame;

use App\Models\Game;
use App\Repositories\GameRepository\GameRepository;

class DefaultNewGameCommandHandler implements NewGameCommandHandler
{
    public function __construct(
        private GameRepository $gameRepository
    )
    {
    }

    public function handle(): NewGameResult
    {
        $gameId = $this->gameRepository->create();

        return new NewGameResult($gameId);
    }
}
