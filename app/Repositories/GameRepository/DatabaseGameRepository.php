<?php

namespace App\Repositories\GameRepository;

use App\Models\Game;
use DateTimeImmutable;

class DatabaseGameRepository implements GameRepository
{

    public function create(): int
    {
        $game = new Game();
        $game->status = Game::BOARD_START;
        $game->moves = 0;
        $game->saveOrFail();

        return $game->id;
    }

    public function move(int $gameId, string $player, int $position): Game
    {
        /** @var Game $game */
        $game = Game::findOrFail($gameId);

        $status = $game->status;
        $status[$position] = $player;
        $game->status = $status;
        $game->moves ++;
        $game->last_move = $player;
        $game->saveOrFail();

        return $game;
    }

    public function setWinner(int $gameId, string $player): Game
    {
        /** @var Game $game */
        $game = Game::findOrFail($gameId);
        $game->winner = $player;
        $game->finished_at = new DateTimeImmutable();
        $game->saveOrFail();

        return $game;
    }

    public function setFinished(int $gameId): Game
    {
        /** @var Game $game */
        $game = Game::findOrFail($gameId);
        $game->finished_at = new DateTimeImmutable();
        $game->saveOrFail();

        return $game;
    }

    public function get(int $gameId): Game
    {
        /** @var Game $game */
        $game = Game::findOrFail($gameId);

        return $game;
    }
}
