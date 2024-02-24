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

        $setWinner = $this->calculateSetWinner($updatedGame);
        $someoneWon = !is_null($setWinner);
        if ($someoneWon) {
            $winner = $updatedGame->status[$setWinner[0]];
            $updatedGame = $this->gameRepository->setWinner(gameId: $updatedGame->id, player: $winner);
            return $this->createMoveResult($updatedGame, $setWinner);
        }

        return $this->createMoveResult($updatedGame);
    }

    private function createMoveResult(Game $updatedGame, array $setWinner = null): MakeAMoveResult
    {
        return new MakeAMoveResult(
            status: $updatedGame->status,
            winner: $updatedGame->winner,
            finished: !is_null($updatedGame->finished_at),
            setWinner: $setWinner
        );
    }

    private function calculateSetWinner(Game $updatedGame): null|array
    {
        $winningSets = [Game::ROWS, Game::COLUMNS, Game::DIAGONALS];
        $setWinner = null;
        foreach ($winningSets as $winningSet) {
            foreach ($winningSet as $set) {
                if (
                    is_string($updatedGame->status[$set[0]]) &&
                    $updatedGame->status[$set[0]] === $updatedGame->status[$set[1]] && $updatedGame->status[$set[0]] === $updatedGame->status[$set[2]]
                ) {
                    $setWinner = $set;
                    break;
                }
            }
        }
        return $setWinner;
    }

    private function validateMove(Game $currentGame, MakeAMoveCommand $command): void
    {
        if (
            !is_null($currentGame->finished_at)
        ) {
            throw new GameFinishedException();
        }

        if (
            $currentGame->last_move === $command->player
        ) {
            throw new PlayerCantMoveTwiceException();
        }

        if (
            $currentGame->status[$command->position] === 'X' ||
            $currentGame->status[$command->position] === 'Y'
        ) {
            throw new PositionAlreadyTakenException($command->position);
        }
    }
}
