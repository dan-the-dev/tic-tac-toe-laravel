<?php

namespace Tests\Doubles\Services\Commands\NewGame;

use App\Services\Commands\NewGame\NewGameCommandHandler;
use App\Services\Commands\NewGame\NewGameResult;

class NewGameCommandHandlerFake implements NewGameCommandHandler
{
    public function __construct(
        private readonly int $id,
    )
    {
    }

    public function handle(): NewGameResult
    {
        return new NewGameResult(
            $this->id
        );
    }
}
