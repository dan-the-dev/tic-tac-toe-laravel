<?php

namespace Tests\Doubles\Services\Commands\MakeAMove;

use App\Services\Commands\MakeAMove\MakeAMoveCommand;
use App\Services\Commands\MakeAMove\MakeAMoveCommandHandler;
use App\Services\Commands\MakeAMove\MakeAMoveResult;

class MakeAMoveCommandHandlerFake implements MakeAMoveCommandHandler
{

    public function handle(MakeAMoveCommand $command): MakeAMoveResult
    {
        return new MakeAMoveResult([0, 1, 'X', 3, 4, 5, 6, 7, 8]);
    }
}
