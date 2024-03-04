<?php

namespace App\Services\Query\GetGame;

use App\Services\Commands\MakeAMove\MakeAMoveCommand;
use App\Services\Commands\MakeAMove\MakeAMoveResult;

interface GetGameQueryHandler
{
    public function handle(GetGameQuery $query): GetGameResult;
}
