<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\GetGameController;
use App\Http\Controllers\MakeAMoveController;
use App\Http\Requests\GetGameRequest;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Services\Query\GetGame\GetGameQueryHandlerFake;

class GetGameControllerTest extends TestCase
{
    public function testItRetrieveAGameCorrectly(): void
    {
        $commandHandler = new GetGameQueryHandlerFake();
        $controller = new GetGameController($commandHandler);

        $actual = $controller(GetGameRequest::create('/game', 'POST', [
            'gameId' => 1,
        ]));

        $this->assertEquals(new Response([
            'status' => [0, 1, 'X', 3, 4, 5, 6, 7, 8],
            'winner' => null,
            'finished' => false,
        ]), $actual);
    }
}
