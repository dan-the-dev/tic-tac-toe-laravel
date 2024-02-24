<?php

namespace Tests\Doubles\Repositories\GameRepository;

use App\Models\Game;
use App\Repositories\GameRepository\GameRepository;
use DateTimeImmutable;
use Exception;

class FakeGameRepository implements GameRepository
{
    private $status = [0, 1, 2, 3, 4, 5, 6, 7, 8];
    private $moves = 0;
    public function __construct(private readonly bool $throwException = false)
    {
    }

    public function setFinalStatus(array $status): void
    {
        $this->status = $status;
    }

    public function setMoves(int $moves): void
    {
        $this->moves = $moves;
    }

    public function create(): int
    {
        if ($this->throwException) {
            throw new Exception();
        }

        return 1;
    }

    public function move(int $gameId, string $player, int $position): Game
    {
        return new Game([
            'id' => 1,
            'status' => $this->status,
            'moves' => $this->moves,
            'winner' => null,
            'finished_at' => null,
        ]);
    }

    public function setWinner(int $gameId, string $player): Game
    {
        return new Game([
            'id' => 1,
            'status' => $this->status,
            'moves' => $this->moves,
            'winner' => $player,
            'finished_at' => new DateTimeImmutable(),
        ]);
    }

    public function setFinished(int $gameId): Game
    {
        return new Game([
            'id' => 1,
            'status' => $this->status,
            'moves' => $this->moves,
            'winner' => null,
            'finished_at' => new DateTimeImmutable(),
        ]);
    }
}
