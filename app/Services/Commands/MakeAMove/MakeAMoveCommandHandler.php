<?php

namespace App\Services\Commands\MakeAMove;

interface MakeAMoveCommandHandler
{
    public function handle(MakeAMoveCommand $command): MakeAMoveResult;
}
