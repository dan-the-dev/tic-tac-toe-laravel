<?php

namespace App\Services\Commands\MakeAMove;

readonly class MakeAMoveResult
{
    public function __construct(
        /**
         * @var array<int|string> $status
         */
        public array $status,
        public ?string $winner = null,
        public bool $finished = false
    )
    {
    }
}
