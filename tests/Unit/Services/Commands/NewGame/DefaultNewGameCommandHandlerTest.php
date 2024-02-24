<?php

namespace Tests\Unit\Services\Commands\NewGame;


use App\Models\Game;
use App\Services\Commands\NewGame\DefaultNewGameCommandHandler;
use App\Services\Commands\NewGame\GameNotCreatedException;
use App\Services\Commands\NewGame\NewGameResult;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Repositories\GameRepository\FakeGameRepository;

class DefaultNewGameCommandHandlerTest extends TestCase
{

    public function testItCreatesNewGameAndReturnsIdOfTheGame():  void
    {
        $handler = new DefaultNewGameCommandHandler(
            new FakeGameRepository()
        );

        $actual = $handler->handle();
        $this->assertEquals(new NewGameResult(1), $actual);
    }

    public function testItThrowsCustomExceptionWhenRepositoryBreakUp():  void
    {
        $handler = new DefaultNewGameCommandHandler(
            new FakeGameRepository(throwException: true)
        );

        $this->expectException(GameNotCreatedException::class);
        $handler->handle();
    }
}
