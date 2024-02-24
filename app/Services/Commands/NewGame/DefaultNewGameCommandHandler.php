<?php

namespace App\Services\Commands\NewGame;

use App\Models\Game;
use App\Repositories\GameRepository\GameRepository;
use Throwable;

class DefaultNewGameCommandHandler implements NewGameCommandHandler
{
    public function __construct(
        private GameRepository $gameRepository
    )
    {
    }

    public function handle(): NewGameResult
    {
        try {
            $gameId = $this->gameRepository->create();
            return new NewGameResult($gameId);
        } catch (Throwable $exception) {
            throw new GameNotCreatedException();
        }
    }
}
