<?php

namespace App\Services\Commands\MakeAMove;

use App\Models\Game;
use App\Repositories\GameRepository\GameRepository;

class DefaultMakeAMoveCommandHandler implements MakeAMoveCommandHandler
{
    public function __construct(
        private GameRepository $gameRepository
    )
    {
    }

    public function handle(MakeAMoveCommand $command): MakeAMoveResult
    {
        $updatedGame = $this->gameRepository->move(
            gameId: $command->gameId,
            player: $command->player,
            position: $command->position
        );

        if ($updatedGame->moves >= count(Game::BOARD_START)) {
            $updatedGame = $this->gameRepository->setFinished($updatedGame->id);
            return $this->createMoveResult($updatedGame);
        }

        $winner = $this->calculateWinner(Game::ROWS, $updatedGame);
        if (!is_null($winner)) {
            $updatedGame = $this->gameRepository->setWinner($updatedGame->id, $winner);
            return $this->createMoveResult($updatedGame);
        }

        $winner = $this->calculateWinner(Game::COLUMNS, $updatedGame);
        if (!is_null($winner)) {
            $updatedGame = $this->gameRepository->setWinner($updatedGame->id, $winner);
            return $this->createMoveResult($updatedGame);
        }

        $winner = $this->calculateWinner(Game::DIAGONALS, $updatedGame);
        if (!is_null($winner)) {
            $updatedGame = $this->gameRepository->setWinner($updatedGame->id, $winner);
            return $this->createMoveResult($updatedGame);
        }

        return $this->createMoveResult($updatedGame);
    }

    public function createMoveResult(Game $updatedGame): MakeAMoveResult
    {
        return new MakeAMoveResult(
            status: $updatedGame->status,
            winner: $updatedGame->winner,
            finished: !is_null($updatedGame->finished_at)
        );
    }

    public function calculateWinner(array $winningSet, Game $updatedGame): null|string
    {
        $winner = null;
        foreach ($winningSet as $set) {
            if (
                is_string($updatedGame->status[$set[0]]) &&
                $updatedGame->status[$set[0]] === $updatedGame->status[$set[1]] && $updatedGame->status[$set[0]] === $updatedGame->status[$set[2]]
            ) {
                $winner = $updatedGame->status[$set[0]];
                break;
            }
        }
        return $winner;
    }
}
