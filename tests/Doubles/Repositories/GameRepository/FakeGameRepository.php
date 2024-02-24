<?php

namespace Tests\Doubles\Repositories\GameRepository;

use App\Repositories\GameRepository\GameRepository;
use Exception;

class FakeGameRepository implements GameRepository
{
    public function __construct(private readonly bool $throwException = false)
    {
    }

    public function create(): int
    {
        if ($this->throwException) {
            throw new Exception();
        }

        return 1;
    }
}
