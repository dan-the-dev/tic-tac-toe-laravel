<?php

namespace App\Services\Query\GetGame;

readonly class GetGameResult
{
    public function __construct(
        /**
         * @var array<int|string> $status
         */
        public array $status,
        public ?string $winner = null,
        public bool $finished = false,
        public ?array $setWinner = null
    )
    {
    }
}
