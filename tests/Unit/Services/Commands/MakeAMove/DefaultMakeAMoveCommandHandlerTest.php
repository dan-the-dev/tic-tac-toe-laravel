<?php

namespace Tests\Unit\Services\Commands\MakeAMove;

use App\Services\Commands\MakeAMove\DefaultMakeAMoveCommandHandler;
use App\Services\Commands\MakeAMove\MakeAMoveCommand;
use App\Services\Commands\MakeAMove\MakeAMoveResult;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\Repositories\GameRepository\FakeGameRepository;

class DefaultMakeAMoveCommandHandlerTest extends TestCase
{
    public function testItMakeAMoveCorrectly(): void
    {
        $gameRepository = new FakeGameRepository();
        $gameRepository->setFinalStatus(['X', 1, 2, 3, 4, 5, 6, 7, 8]);

        $handler = new DefaultMakeAMoveCommandHandler(
            $gameRepository
        );

        $command = new MakeAMoveCommand(
            gameId: 1, player: 'X', position: 0
        );
        $actual = $handler->handle($command);

        $this->assertEquals(new MakeAMoveResult(
            status: ['X', 1, 2, 3, 4, 5, 6, 7, 8],
            winner: null,
            finished: false
        ), $actual);
    }
    public function testItSetAsFinishedWhenMovesAreFinished(): void
    {
        /**
         * X | Y | X      X | Y | X
         * Y | X | 5  ->  Y | X | X  -> no winner
         * Y | X | Y      Y | X | Y
         */

        $gameRepository = new FakeGameRepository();
        $gameRepository->setFinalStatus(['X', 'Y', 'X', 'Y', 'X', 'X', 'Y', 'X', 'Y']);
        $gameRepository->setMoves(9);

        $handler = new DefaultMakeAMoveCommandHandler(
            $gameRepository
        );

        $command = new MakeAMoveCommand(
            gameId: 1, player: 'X', position: 5
        );
        $actual = $handler->handle($command);

        $this->assertEquals(new MakeAMoveResult(
            status: ['X', 'Y', 'X', 'Y', 'X', 'X', 'Y', 'X', 'Y'],
            winner: null,
            finished: true
        ), $actual);
    }
    public function testItSetAsFinishedWhenXWinsInRow(): void
    {
        /**
         * X |   | Y      X |   | Y
         * Y | Y |    ->  Y | Y |    -> X wins
         * X |   | X      X | X | X
         */

        $gameRepository = new FakeGameRepository();
        $gameRepository->setFinalStatus(['X', 1, 'Y', 'Y', 'Y', 5, 'X', 'X', 'X']);
        $gameRepository->setMoves(7);

        $handler = new DefaultMakeAMoveCommandHandler(
            $gameRepository
        );

        $command = new MakeAMoveCommand(
            gameId: 1, player: 'X', position: 7
        );
        $actual = $handler->handle($command);

        $this->assertEquals(new MakeAMoveResult(
            status: ['X', 1, 'Y', 'Y', 'Y', 5, 'X', 'X', 'X'],
            winner: 'X',
            finished: true
        ), $actual);
    }
    public function testItSetAsFinishedWhenYWinsInDiagonal(): void
    {
        /**
         * X |   | Y      X |   | Y
         *   |   | X  ->    | Y | X  -> Y wins
         * Y | X |        Y | X |
         */

        $gameRepository = new FakeGameRepository();
        $gameRepository->setFinalStatus(['X', 1, 'Y', 3, 'Y', 'X', 'Y', 'X', 8]);
        $gameRepository->setMoves(6);

        $handler = new DefaultMakeAMoveCommandHandler(
            $gameRepository
        );

        $command = new MakeAMoveCommand(
            gameId: 1, player: 'Y', position: 4
        );
        $actual = $handler->handle($command);

        $this->assertEquals(new MakeAMoveResult(
            status: ['X', 1, 'Y', 3, 'Y', 'X', 'Y', 'X', 8],
            winner: 'Y',
            finished: true
        ), $actual);
    }
    public function testItSetAsFinishedWhenXWinsInColumn(): void
    {
        /**
         * Y |   | Y      Y | X | Y
         *   | X | X  ->    | X | X  -> X wins
         * Y | X |        Y | X |
         */

        $gameRepository = new FakeGameRepository();
        $gameRepository->setFinalStatus(['Y', 'X', 'Y', 3, 'X', 'X', 'Y', 'X', 8]);
        $gameRepository->setMoves(7);

        $handler = new DefaultMakeAMoveCommandHandler(
            $gameRepository
        );

        $command = new MakeAMoveCommand(
            gameId: 1, player: 'X', position: 1
        );
        $actual = $handler->handle($command);

        $this->assertEquals(new MakeAMoveResult(
            status: ['Y', 'X', 'Y', 3, 'X', 'X', 'Y', 'X', 8],
            winner: 'X',
            finished: true
        ), $actual);
    }
}
