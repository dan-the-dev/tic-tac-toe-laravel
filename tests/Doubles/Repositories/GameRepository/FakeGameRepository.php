<?php

namespace Tests\Doubles\Repositories\GameRepository;

use App\Models\Game;
use App\Repositories\GameRepository\GameRepository;
use DateTimeImmutable;
use Exception;

class FakeGameRepository implements GameRepository
{
    const INITIAL_STATUS = [0, 1, 2, 3, 4, 5, 6, 7, 8];
    private array $initialStatus;
    private array $finalStatus;
    private ?string $last_move = null;
    private bool $finished = false;
    private $moves = 0;
    public function __construct(private readonly bool $throwException = false)
    {
        $this->finalStatus = self::INITIAL_STATUS;
        $this->initialStatus = self::INITIAL_STATUS;
    }

    public function setFakeFinalStatus(array $status): void
    {
        $this->finalStatus = $status;
    }

    public function setFakeInitialStatus(array $status): void
    {
        $this->initialStatus = $status;
    }

    public function setFakeLastMove(string $player): void
    {
        $this->last_move = $player;
    }

    public function setFakeMoves(int $moves): void
    {
        $this->moves = $moves;
    }

    public function setFakeAsFinished(): void
    {
        $this->finished = true;
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
            'status' => $this->finalStatus,
            'last_move' => $player,
            'moves' => $this->moves,
            'winner' => null,
            'finished_at' => null,
        ]);
    }

    public function setWinner(int $gameId, string $player): Game
    {
        return new Game([
            'id' => 1,
            'status' => $this->finalStatus,
            'last_move' => $this->last_move,
            'moves' => $this->moves,
            'winner' => $player,
            'finished_at' => new DateTimeImmutable(),
        ]);
    }

    public function setFinished(int $gameId): Game
    {
        return new Game([
            'id' => 1,
            'status' => $this->finalStatus,
            'last_move' => $this->last_move,
            'moves' => $this->moves,
            'winner' => null,
            'finished_at' => new DateTimeImmutable(),
        ]);
    }

    public function get(int $gameId): Game
    {
        $finished = null;
        if ($this->finished) {
            $finished = new DateTimeImmutable();
        }

        return new Game([
            'id' => 1,
            'status' => $this->initialStatus,
            'last_move' => $this->last_move,
            'moves' => $this->moves,
            'winner' => null,
            'finished_at' => $finished,
        ]);
    }
}
