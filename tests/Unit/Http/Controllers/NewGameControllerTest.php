<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\NewGameController;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class NewGameControllerTest extends TestCase
{
    public function testItCreatesANewGameCorrectly(): void
    {
        $controller = new NewGameController();

        $actual = $controller();

        $this->assertEquals(new Response(['gameId' => 1]), $actual);
    }
}
