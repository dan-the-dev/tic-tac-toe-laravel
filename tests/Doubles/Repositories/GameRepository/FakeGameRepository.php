<?php

namespace Tests\Doubles\Repositories\GameRepository;

use App\Repositories\GameRepository\GameRepository;

class FakeGameRepository implements GameRepository
{

    public function create(): int
    {
        return 1;
    }
}
