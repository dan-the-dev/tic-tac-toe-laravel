<?php

namespace App\Services\Commands\MakeAMove;

use App\Models\Game;
use App\PlayerTips;
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
            return $this->createMoveResult($updatedGame, $setWinner, []);
        }

        $potentialWinner = $this->calculatePotentialWinner($updatedGame, $command->player);
        return $this->createMoveResult($updatedGame, null, $potentialWinner);
    }

    private function createMoveResult(Game $updatedGame, array $setWinner = null, array $potentialWinner = []): MakeAMoveResult
    {
        $tip = PlayerTips::NO_TIP;
        if (count($potentialWinner) >= 1) {
            $tip = PlayerTips::WARNING;
            if (count($potentialWinner) >= 2) {
                $tip = PlayerTips::MATCH_CLOSED;
            }
        }

        return new MakeAMoveResult(
            status: $updatedGame->status,
            winner: $updatedGame->winner,
            finished: !is_null($updatedGame->finished_at),
            setWinner: $setWinner,
            tip: $tip
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

    private function calculatePotentialWinner(Game $updatedGame, string $player): null|array
    {
        $winningSets = [Game::ROWS, Game::COLUMNS, Game::DIAGONALS];
        $possibleWinningSets = [];

        foreach ($winningSets as $winningSet) {
            foreach ($winningSet as $set) {

                $isPossibleWinningSet = ($updatedGame->status[$set[0]] === $player && $updatedGame->status[$set[0]] === $updatedGame->status[$set[1]] && is_numeric($updatedGame->status[$set[2]]))
                    || ($updatedGame->status[$set[0]] === $player && $updatedGame->status[$set[0]] === $updatedGame->status[$set[2]] && is_numeric($updatedGame->status[$set[1]]))
                    || ($updatedGame->status[$set[1]] === $player && $updatedGame->status[$set[1]] === $updatedGame->status[$set[2]] && is_numeric($updatedGame->status[$set[0]]));

                if ($isPossibleWinningSet) {
                    $possibleWinningSets[] = $set;
                }
            }
        }

        return $possibleWinningSets;
    }

    private function validateMove(Game $currentGame, MakeAMoveCommand $command): void
    {
        $gameFinished = !is_null($currentGame->finished_at);
        if ($gameFinished) {
            throw new GameFinishedException();
        }

        $samePlayerMovedLastTime = $currentGame->last_move === $command->player;
        if ($samePlayerMovedLastTime) {
            throw new PlayerCantMoveTwiceException();
        }

        $positionAlreadyTaken = $currentGame->status[$command->position] === 'X' || $currentGame->status[$command->position] === 'Y';
        if ($positionAlreadyTaken) {
            throw new PositionAlreadyTakenException($command->position);
        }
    }
}
