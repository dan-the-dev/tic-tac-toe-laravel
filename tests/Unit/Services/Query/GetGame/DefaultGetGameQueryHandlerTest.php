<?php

namespace Tests\Unit\Services\Query\GetGame;

use App\Services\Query\GetGame\DefaultGetGameQueryHandler;
use App\Services\Query\GetGame\GetGameQuery;
use App\Services\Query\GetGame\GetGameResult;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Repositories\GameRepository\FakeGameRepository;

/**
 * - when game not exist
 */
class DefaultGetGameQueryHandlerTest extends TestCase
{
    public function testItReturnGameStatusCorrectlyWhenGameExist(): void
    {
        $gameRepository = new FakeGameRepository();
        $gameRepository->setFakeInitialStatus(['X', 1, 2, 3, 4, 5, 6, 7, 8]);

        $handler = new DefaultGetGameQueryHandler(
            $gameRepository
        );
        $query = new GetGameQuery(gameId: 1);
        $actual = $handler->handle($query);

        $this->assertEquals(new GetGameResult(
            status: ['X', 1, 2, 3, 4, 5, 6, 7, 8],
            winner: null,
            finished: false
        ), $actual);
    }
}
