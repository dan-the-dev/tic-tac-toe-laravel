<?php

namespace App\Services\Commands\MakeAMove;

readonly class MakeAMoveCommand
{
    public function __construct(
        public int $gameId,
        public string $player,
        public int $position
    )
    {
    }
}
