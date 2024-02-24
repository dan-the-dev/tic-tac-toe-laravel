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
        $currentGame = $this->gameRepository->get($command->gameId);

        $this->validateMove($currentGame, $command);

        $updatedGame = $this->gameRepository->move(
            gameId: $command->gameId,
            player: $command->player,
            position: $command->position
        );

        $lastMoveReached = $updatedGame->moves >= count(Game::BOARD_START);
        if ($lastMoveReached) {
            $updatedGame = $this->gameRepository->setFinished(gameId: $updatedGame->id);
            return $this->createMoveResult($updatedGame);
        }

        $winner = $this->calculateWinner($updatedGame);
        $someoneWon = !is_null($winner);
        if ($someoneWon) {
            $updatedGame = $this->gameRepository->setWinner(gameId: $updatedGame->id, player: $winner);
            return $this->createMoveResult($updatedGame);
        }

        return $this->createMoveResult($updatedGame);
    }

    private function createMoveResult(Game $updatedGame): MakeAMoveResult
    {
        return new MakeAMoveResult(
            status: $updatedGame->status,
            winner: $updatedGame->winner,
            finished: !is_null($updatedGame->finished_at)
        );
    }

    private function calculateWinner(Game $updatedGame): null|string
    {
        $winningSets = [Game::ROWS, Game::COLUMNS, Game::DIAGONALS];
        $winner = null;
        foreach ($winningSets as $winningSet) {
            foreach ($winningSet as $set) {
                if (
                    is_string($updatedGame->status[$set[0]]) &&
                    $updatedGame->status[$set[0]] === $updatedGame->status[$set[1]] && $updatedGame->status[$set[0]] === $updatedGame->status[$set[2]]
                ) {
                    $winner = $updatedGame->status[$set[0]];
                    break;
                }
            }
        }
        return $winner;
    }

    private function validateMove(Game $currentGame, MakeAMoveCommand $command): void
    {
        if (
            $currentGame->last_move === $command->player
        ) {
            throw new PlayerCantMoveTwiceException();
        }

        if (
            $currentGame->status[$command->position] === 'X' ||
            $currentGame->status[$command->position] === 'Y'
        ) {
            throw new PositionAlreadyTakenException();
        }
    }
}
