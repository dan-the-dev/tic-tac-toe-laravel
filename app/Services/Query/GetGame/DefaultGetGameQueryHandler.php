<?php

namespace App\Services\Query\GetGame;

use App\Models\Game;
use App\Repositories\GameRepository\GameRepository;

class DefaultGetGameQueryHandler implements GetGameQueryHandler
{
    public function __construct(private readonly GameRepository $gameRepository)
    {
    }

    public function handle(GetGameQuery $query): GetGameResult
    {
        $game = $this->gameRepository->get(gameId: $query->gameId);

        $setWinner = $this->calculateSetWinner($game);
        $someoneWon = !is_null($setWinner);

        return new GetGameResult(
            status: $game->status,
            winner: $game->winner,
            finished: $someoneWon,
            setWinner: $setWinner
        );
    }


    private function calculateSetWinner(Game $updatedGame): null|array
    {
        $winningSets = [Game::ROWS, Game::COLUMNS, Game::DIAGONALS];
        $setWinner = null;

        foreach ($winningSets as $winningSet) {
            foreach ($winningSet as $set) {
                $wholeSetHasSameValues = $updatedGame->status[$set[0]] === $updatedGame->status[$set[1]] && $updatedGame->status[$set[0]] === $updatedGame->status[$set[2]];
                if ($wholeSetHasSameValues) {
                    $setWinner = $set;
                    break;
                }
            }
        }

        return $setWinner;
    }
}
