<?php

namespace Tests\Feature;

use App\Services\Commands\MakeAMove\GameFinishedException;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class GameSimulationTest extends TestCase
{
    public function test_simulate_a_game(): void
    {
        $response = $this->post('/api/new-game');
        $response->assertStatus(200);

        $newGame = json_decode($response->getContent(), true);
        $gameId = $newGame['gameId'];

        /**
         * 1st move
         *   |   |        X |   |
         *   |   |    ->    |   |
         *   |   |          |   |
         */
        $gameStatus = $this->makeAMove(gameId: $gameId, player: 'X', position: 0);
        $this->assertEquals(['X', '1', '2', '3', '4', '5', '6', '7', '8'], $gameStatus['status']);
        $this->assertNull($gameStatus['winner']);
        $this->assertFalse($gameStatus['finished']);

        /**
         * 2nd move
         * X |   |        X | Y |
         *   |   |    ->    |   |
         *   |   |          |   |
         */
        $gameStatus = $this->makeAMove(gameId: $gameId, player: 'Y', position: 1);
        $this->assertEquals(['X', 'Y', '2', '3', '4', '5', '6', '7', '8'], $gameStatus['status']);
        $this->assertNull($gameStatus['winner']);
        $this->assertFalse($gameStatus['finished']);

        /**
         * 3rd move
         * X | Y |        X | Y | X
         *   |   |    ->    |   |
         *   |   |          |   |
         */
        $gameStatus = $this->makeAMove(gameId: $gameId, player: 'X', position: 2);
        $this->assertEquals(['X', 'Y', 'X', '3', '4', '5', '6', '7', '8'], $gameStatus['status']);
        $this->assertNull($gameStatus['winner']);
        $this->assertFalse($gameStatus['finished']);

        /**
         * 4th move
         * X | Y | X      X | Y | X
         *   |   |    ->  Y |   |
         *   |   |          |   |
         */
        $gameStatus = $this->makeAMove(gameId: $gameId, player: 'Y', position: 3);
        $this->assertEquals(['X', 'Y', 'X', 'Y', '4', '5', '6', '7', '8'], $gameStatus['status']);
        $this->assertNull($gameStatus['winner']);
        $this->assertFalse($gameStatus['finished']);

        /**
         * 5th move
         * X | Y | X      X | Y | X
         * Y |   |    ->  Y | X |
         *   |   |          |   |
         */
        $gameStatus = $this->makeAMove(gameId: $gameId, player: 'X', position: 4);
        $this->assertEquals(['X', 'Y', 'X', 'Y', 'X', '5', '6', '7', '8'], $gameStatus['status']);
        $this->assertNull($gameStatus['winner']);
        $this->assertFalse($gameStatus['finished']);

        /**
         * 6th move
         * X | Y | X      X | Y | X
         * Y | X |    ->  Y | X | Y
         *   |   |          |   |
         */
        $gameStatus = $this->makeAMove(gameId: $gameId, player: 'Y', position: 5);
        $this->assertEquals(['X', 'Y', 'X', 'Y', 'X', 'Y', '6', '7', '8'], $gameStatus['status']);
        $this->assertNull($gameStatus['winner']);
        $this->assertFalse($gameStatus['finished']);

        /**
         * 7th move
         * X | Y | X      X | Y | X
         * Y | X | Y  ->  Y | X | Y  -> X WINS!
         *   |   |        X |   |
         */
        $gameStatus = $this->makeAMove(gameId: $gameId, player: 'X', position: 6);
        $this->assertEquals(['X', 'Y', 'X', 'Y', 'X', 'Y', 'X', '7', '8'], $gameStatus['status']);
        $this->assertEquals('X', $gameStatus['winner']);
        $this->assertTrue($gameStatus['finished']);

        /**
         * 8th move - will fail because game is over
         */
        $gameStatus = $this->makeAMove(gameId: $gameId, player: 'Y', position: 7);
        $this->assertEquals('Something went wrong!', $gameStatus['message']);
        $this->assertEquals(GameFinishedException::class, $gameStatus['exception']);
    }

    public function makeAMove(int $gameId, string $player, int $position): array
    {
        $response = $this->post('/api/move', [
            'gameId' => $gameId,
            'player' => $player,
            'position' => $position,
        ]);
        $response->assertStatus(200);
        return json_decode($response->getContent(), true);
    }
}
