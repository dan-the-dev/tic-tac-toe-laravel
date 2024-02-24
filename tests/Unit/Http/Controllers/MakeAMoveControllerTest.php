<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\MakeAMoveController;
use App\Http\Requests\MakeAMoveRequest;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Services\Commands\MakeAMove\MakeAMoveCommandHandlerFake;

class MakeAMoveControllerTest extends TestCase
{
    public function testItMakeAMoveCorrectly(): void
    {
        $commandHandler = new MakeAMoveCommandHandlerFake();
        $controller = new MakeAMoveController($commandHandler);

        $actual = $controller(MakeAMoveRequest::create('/move', 'POST', [
            'gameId' => 1,
            'player' => 'X',
            'position' => 2,
        ]));

        $this->assertEquals(new Response([
            'status' => [0, 1, 'X', 3, 4, 5, 6, 7, 8],
            'winner' => null,
            'finished' => false,
        ]), $actual);
    }
}
