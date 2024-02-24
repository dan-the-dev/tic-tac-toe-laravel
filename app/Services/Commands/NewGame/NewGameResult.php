<?php

namespace App\Services\Commands\NewGame;

readonly class NewGameResult
{
    public function __construct(
        public int $id,
    )
    {
    }
}
