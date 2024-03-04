<?php

namespace App\Services\Query\GetGame;

readonly class GetGameQuery
{
    public function __construct(
        public int $gameId,
    )
    {
    }
}
