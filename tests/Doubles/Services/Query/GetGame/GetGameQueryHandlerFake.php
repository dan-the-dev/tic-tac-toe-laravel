<?php

namespace Tests\Doubles\Services\Query\GetGame;

use App\Services\Query\GetGame\GetGameQuery;
use App\Services\Query\GetGame\GetGameQueryHandler;
use App\Services\Query\GetGame\GetGameResult;

class GetGameQueryHandlerFake implements GetGameQueryHandler
{

    public function handle(GetGameQuery $query): GetGameResult
    {
        return new GetGameResult([0, 1, 'X', 3, 4, 5, 6, 7, 8]);
    }
}
