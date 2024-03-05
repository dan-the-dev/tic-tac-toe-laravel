<?php

namespace App\Services\Commands\MakeAMove;

use App\PlayerTips;
use Illuminate\Validation\Rules\Enum;

readonly class MakeAMoveResult
{
    public function __construct(
        /**
         * @var array<int|string> $status
         */
        public array $status,
        public ?string $winner = null,
        public bool $finished = false,
        public ?array $setWinner = null,
        public ?PlayerTips $tip = null
    )
    {
    }
}
