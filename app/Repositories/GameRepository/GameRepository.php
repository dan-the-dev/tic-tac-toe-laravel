<?php

namespace App\Repositories\GameRepository;

use App\Models\Game;

interface GameRepository
{
    public function create(): int;
    public function get(int $gameId): Game;
    public function move(int $gameId, string $player, int $position): Game;
    public function setWinner(int $gameId, string $player): Game;
    public function setFinished(int $gameId): Game;
}
