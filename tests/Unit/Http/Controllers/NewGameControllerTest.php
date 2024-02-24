<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\NewGameController;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Services\Commands\NewGame\NewGameCommandHandlerFake;

class NewGameControllerTest extends TestCase
{
    public function testItCreatesANewGameCorrectly(): void
    {
        $commandHandler = new NewGameCommandHandlerFake(10);
        $controller = new NewGameController($commandHandler);

        $actual = $controller();

        $this->assertEquals(new Response(['gameId' => 10]), $actual);
    }
}
